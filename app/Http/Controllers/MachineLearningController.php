<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MachineLearningController extends Controller
{
    private ?string $mlBaseUrl = null;

    public function __construct()
    {
        $this->mlBaseUrl = env('ML_BASE_URL', 'http://localhost:5000');
    }

    public function index()
    {
        $user = auth()->user();
        $currentMonth = "2025-10"; // Dapat dikembalikan ke now()->format('Y-m') jika sudah siap
        $currentYear = now()->year;
        $currentMonthNum = now()->month;

        // --- 1. Classification Stats ---
        $classificationStats = [
            'total_transactions' => $user
                ->transactions()->whereNull('investment_transaction_id')->count(),
            'model_info' => 'Model Naive Bayes dilatih pada data Anda.',
        ];

        // --- 2. Fetch Actual Next Month Prediction ---
        $nextMonthPredictionSummary = "N/A";

        $dailySpending = $user->transactions()
            ->where('type', 'expense')
            ->whereNull('investment_transaction_id')
            ->selectRaw('DATE(date) as date, SUM(amount) as amount')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        if ($dailySpending->count() > 10) {
            try {
                $responsePredict = Http::timeout(15)->post($this->mlBaseUrl . '/predict', $dailySpending);
                if ($responsePredict->successful()) {
                    $nextMonthPredictionSummary = $responsePredict->json('next_month_total', 'Error');
                } else {
                    Log::error('Python API /predict call failed from ml.index: ' . $responsePredict->status());
                    $nextMonthPredictionSummary = "Error";
                }
            } catch (\Exception $e) {
                Log::error('Python API /predict connection failed from ml.index: ' . $e->getMessage());
                $nextMonthPredictionSummary = "Error";
            }
        } else {
            $nextMonthPredictionSummary = "Data Kurang";
        }

        // --- 3. Fetch Actual Recommendations (Preview) ---
        $recommendationPreview = [['type' => 'info', 'message' => 'Analisis sedang berjalan...']];

        $currentMonthBudgets = $user->budgets()
            ->with('category')
            ->where('month', $currentMonth)
            ->get();
        $totalBudgetLimit = $currentMonthBudgets->sum('limit');

        $monthlyExpense = $user->transactions()
            ->expense()
            ->whereYear('date', $currentYear)
            ->whereNull('investment_transaction_id')
            ->whereMonth('date', $currentMonthNum)
            ->sum('amount');

        $monthlyIncome = $user->transactions()
            ->income()
            ->whereYear('date', $currentYear)
            ->whereNull('investment_transaction_id')
            ->whereMonth('date', $currentMonthNum)
            ->sum('amount');

        $budgetSummary = null;
        if ($totalBudgetLimit > 0) {
            $budgetProgress = ($monthlyExpense / $totalBudgetLimit) * 100;
            $budgetRemaining = $totalBudgetLimit - $monthlyExpense;
            $isOverBudget = $monthlyExpense > $totalBudgetLimit;
            $budgetSummary = (object) [
                'limit' => $totalBudgetLimit,
                'spent' => $monthlyExpense,
                'remaining' => $budgetRemaining,
                'progress' => $budgetProgress,
                'isOverBudget' => $isOverBudget
            ];
        }

        $transactions = $user->transactions()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->whereNull('investment_transaction_id')
            ->with('category')
            ->get(['description', 'category_id', 'amount', 'type', 'date']);

        $payload = [
            'budgets' => $currentMonthBudgets->map(function ($b) {
                return [
                    'category' => $b->category->name ?? 'Uncategorized',
                    'budget' => $b->limit,
                    'spent' => $b->spent
                ];
            }),
            'transactions' => $transactions->map(function ($t) {
                return [
                    'description' => $t->description,
                    'category' => $t->category->name ?? 'Uncategorized',
                    'amount' => $t->amount,
                    'type' => $t->type,
                    'date' => $t->date,
                ];
            })
        ];

        try {
            $responsePython = Http::timeout(15)->post($this->mlBaseUrl . '/recommend', $payload);
            if ($responsePython->successful()) {
                $pythonInsights = $responsePython->json('insights', []);
                $recommendationPreview = array_slice($pythonInsights, 0, 2);
                if (empty($recommendationPreview)) {
                    $recommendationPreview = [['type' => 'info', 'message' => 'Tidak ada rekomendasi spesifik saat ini.']];
                }
            } else {
                Log::error('Python API /recommend failed from ml.index: ' . $responsePython->status());
                $recommendationPreview = [['type' => 'error', 'message' => 'Gagal memuat rekomendasi.']];
            }
        } catch (\Exception $e) {
            Log::error('Python API /recommend connection failed from ml.index: ' . $e->getMessage());
            $recommendationPreview = [['type' => 'error', 'message' => 'Gagal terhubung ke layanan rekomendasi.']];
        }

        return view('ml.index', compact(
            'classificationStats',
            'nextMonthPredictionSummary',
            'recommendationPreview'
        ));
    }

    public function classifyTransaction(Request $request)
    {
        $description = $request->input('description', '');
        if (empty($description)) {
            return response()->json(['error' => 'Description cannot be empty'], 400);
        }

        $startTime = microtime(true);
        try {
            $response = Http::post($this->mlBaseUrl . '/classify', [
                'description' => $description,
            ]);

            $duration = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $mlResult = $response->json();

                $suggestedCategoryName = $mlResult['predicted_category'] ?? 'Other Expense';
                $predictedType = $mlResult['predicted_type'] ?? 'expense';

                $this->storeMetric('classify_latency', $duration);
                $this->storeMetric('classify_confidence_cat', $mlResult['confidence_category'] ?? 0);
                $this->storeMetric('classify_confidence_type', $mlResult['confidence_type'] ?? 0);

                $category = Category::where('name', $suggestedCategoryName)->first();

                return response()->json([
                    'suggested_category_id' => $category ? $category->category_id : null,
                    'predicted_category'    => $suggestedCategoryName,
                    'confidence_category'   => $mlResult['confidence_category'] ?? 0,
                    'predicted_type'        => $predictedType,
                    'confidence_type'       => $mlResult['confidence_type'] ?? 0,
                    'explanation'           => $mlResult['explanation'] ?? 'Classification failed.',
                ]);
            } else {
                Log::error('ML API Classification Failed: ' . $response->body());
                Cache::increment('ml_classify_errors');
                return response()->json([
                    'error' => 'Classification service error.',
                    'suggested_category_id' => null,
                    'predicted_type' => 'expense',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('ML API Connection Failed: ' . $e->getMessage());
            Cache::increment('ml_classify_errors');
            return response()->json([
                'error' => 'Could not connect to classification service.',
                'suggested_category_id' => null,
                'predicted_type' => 'expense',
            ], 500);
        }
    }

    public function predictions()
    {
        $dailySpending = auth()->user()->transactions()
            ->where('type', 'expense')
            ->whereNull('investment_transaction_id')
            ->selectRaw('DATE(date) as date, SUM(amount) as amount')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();

        $forecastData = [];
        $nextMonthPrediction = "Rp 0";

        if (count($dailySpending) > 10) {
            $startTime = microtime(true);
            try {
                $response = Http::post($this->mlBaseUrl . '/predict', $dailySpending);
                $duration = round((microtime(true) - $startTime) * 1000);
                $this->storeMetric('predict_latency', $duration);

                if ($response->successful()) {
                    $data = $response->json();
                    $forecastData = $data['forecast_data'] ?? [];
                    $nextMonthPrediction = $data['next_month_total'] ?? "Error";
                } else {
                    Cache::increment('ml_predict_errors');
                    $nextMonthPrediction = "Error: Service not responding";
                }
            } catch (\Exception $e) {
                Cache::increment('ml_predict_errors');
                $nextMonthPrediction = "Error: " . $e->getMessage();
            }
        } else {
            $nextMonthPrediction = "Butuh lebih banyak data transaksi untuk prediksi.";
        }

        return view('ml.predictions', [
            'forecastData' => $forecastData,
            'nextMonthPrediction' => $nextMonthPrediction
        ]);
    }

    public function recommendations()
    {
        $user = auth()->user();
        $cacheKey = "recommendations_user_{$user->id}";

        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            if ($cachedData['geminiRecommendationText'] == "Maaf, ringkasan AI tidak dapat dimuat saat ini.") {
                $this->clearPortfolioCache($user->id);
            } else {
                return view('ml.recommendations', [
                    'geminiRecommendationText' => $cachedData['geminiRecommendationText'],
                    'pythonInsights' => $cachedData['pythonInsights']
                ]);
            }
        }

        $currentMonth = '2025-10';
        $currentYear = now()->year;
        $currentMonthNum = 10;

        $monthlyEarnedIncome = $user->transactions()
            ->income()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->whereNull('investment_transaction_id')
            ->sum('amount');

        $monthlySpending = $user->transactions()
            ->expense()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->whereNull('investment_transaction_id')
            ->sum('amount');

        $monthlyInvestmentPurchase = $user->transactions()
            ->expense()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->whereNotNull('investment_transaction_id')
            ->sum('amount');

        $monthlyInvestmentSale = $user->transactions()
            ->income()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->whereNotNull('investment_transaction_id')
            ->sum('amount');

        $netCashFlow = $monthlyEarnedIncome - $monthlySpending;

        $currentMonthBudgets = $user->budgets()
            ->with('category')
            ->where('month', $currentMonth)
            ->get();
        $totalBudgetLimit = $currentMonthBudgets->sum('limit');

        $budgetSummary = null;
        if ($totalBudgetLimit > 0) {
            $budgetProgress = ($monthlySpending / $totalBudgetLimit) * 100;
            $budgetRemaining = $totalBudgetLimit - $monthlySpending;
            $isOverBudget = $monthlySpending > $totalBudgetLimit;
            $budgetSummary = (object) [
                'limit'        => $totalBudgetLimit,
                'spent'        => $monthlySpending,
                'remaining'    => $budgetRemaining,
                'progress'     => $budgetProgress,
                'isOverBudget' => $isOverBudget
            ];
        }

        $consumptiveTransactions = $user->transactions()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->whereNull('investment_transaction_id')
            ->with('category')
            ->get(['description', 'category_id', 'amount', 'type', 'date']);

        $payload = [
            'budgets' => $currentMonthBudgets->map(function ($b) {
                return [
                    'category' => $b->category->name ?? 'Uncategorized',
                    'budget'   => $b->limit,
                    'spent'    => $b->spent
                ];
            }),
            'transactions' => $consumptiveTransactions->map(function ($t) {
                return [
                    'description' => $t->description,
                    'category'    => $t->category->name ?? 'Uncategorized',
                    'amount'      => $t->amount,
                    'type'        => $t->type,
                    'date'        => $t->date,
                ];
            })
        ];

        $pythonInsights = [];
        $pythonStartTime = microtime(true);
        try {
            $responsePython = Http::post($this->mlBaseUrl . '/recommend', $payload);
            $this->storeMetric('recommend_python_latency', round((microtime(true) - $pythonStartTime) * 1000));
            if ($responsePython->successful()) {
                $pythonInsights = $responsePython->json('insights', []);
            } else {
                Log::error('Python API /recommend failed: ' . $responsePython->status() . ' - ' . $responsePython->body());
                Cache::increment('ml_recommend_python_errors');
            }
        } catch (\Exception $e) {
            Log::error('Python API /recommend connection failed: ' . $e->getMessage());
            Cache::increment('ml_recommend_python_errors');
        }

        $geminiRecommendationText = "Maaf, ringkasan AI tidak dapat dimuat saat ini.";
        $geminiStartTime = microtime(true);
        $apiKey = env('GEMINI_API_KEY');

        if ($apiKey) {
            $prompt = "Anda adalah asisten keuangan pribadi yang ramah dan memotivasi untuk aplikasi Smart Finance.\n\n";
            $prompt .= "Berikut ringkasan kondisi keuangan pengguna bulan ini:\n";
            $prompt .= "- Total Pemasukan (Gaji, dll): Rp " . number_format($monthlyEarnedIncome, 0, ',', '.') . "\n";
            $prompt .= "- Total Pengeluaran (Konsumtif): Rp " . number_format($monthlySpending, 0, ',', '.') . "\n";
            $prompt .= "- Arus Kas Bersih (Sisa Uang): Rp " . number_format($netCashFlow, 0, ',', '.') . "\n";

            $prompt .= "\nSebagai tambahan, pengguna melakukan aktivitas investasi:\n";
            $prompt .= "- Total Pembelian Aset (Investasi): Rp " . number_format($monthlyInvestmentPurchase, 0, ',', '.') . "\n";
            $prompt .= "- Total Penjualan Aset (Hasil Investasi): Rp " . number_format($monthlyInvestmentSale, 0, ',', '.') . "\n";

            if ($budgetSummary) {
                $prompt .= "\nStatus Anggaran (hanya melacak pengeluaran konsumtif):\n";
                $prompt .= "- Total Anggaran: Rp " . number_format($budgetSummary->limit, 0, ',', '.') . "\n";
                $prompt .= "- Status: " . ($budgetSummary->isOverBudget ? "Melebihi batas!" : number_format($budgetSummary->progress, 1) . "% terpakai") . "\n";
            } else {
                $prompt .= "\n- Status Anggaran: (Belum diatur)\n";
            }

            $prompt .= "\nAnalisis AI (hanya dari data konsumtif) menemukan wawasan berikut:\n";
            if (!empty($pythonInsights)) {
                foreach ($pythonInsights as $insight) {
                    $prompt .= "- " . $insight['message'] . "\n";
                }
            } else {
                $prompt .= "- Tidak ada temuan spesifik yang perlu perhatian khusus bulan ini.\n";
            }

            $prompt .= "\n\nTugas Anda:\n";
            $prompt .= "1. Berikan ringkasan singkat (1-2 kalimat) tentang kondisi *cash flow* pengguna (Pemasukan vs Pengeluaran Konsumtif).\n";
            $prompt .= "2. Berikan komentar singkat tentang aktivitas *investasi* mereka. Apresiasi jika mereka berinvestasi.\n";
            $prompt .= "3. Pilih 1-2 wawasan *paling penting* dari analisis AI dan jelaskan dengan bahasa yang memotivasi.\n";
            $prompt .= "4. Jaga agar total respons tetap ringkas (maksimal 5-6 kalimat).\n";
            $prompt .= "5. Format sebagai teks biasa (plain text).\n";
            $prompt .= "Respons Anda:";

            try {
                $responseGemini = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent($prompt);
                $this->storeMetric('recommend_gemini_latency', round((microtime(true) - $geminiStartTime) * 1000));

                if ($responseGemini->text()) {
                    $geminiRecommendationText = $responseGemini->text();
                } else {
                    Log::error('Gemini API call failed: ' . json_encode($responseGemini));
                    Cache::increment('ml_recommend_gemini_errors');
                }
            } catch (\Exception $e) {
                Log::error('Gemini API connection failed: ' . $e->getMessage());
                Cache::increment('ml_recommend_gemini_errors');
            }
        } else {
            Log::warning('GEMINI_API_KEY not set. Skipping Gemini call.');
            $geminiRecommendationText = "Ringkasan AI dinonaktifkan. Silakan periksa wawasan detail di bawah.";
        }

        Cache::put($cacheKey, [
            'geminiRecommendationText' => $geminiRecommendationText,
            'pythonInsights' => $pythonInsights
        ], now()->addHours(6));

        return view('ml.recommendations', [
            'geminiRecommendationText' => $geminiRecommendationText,
            'pythonInsights' => $pythonInsights
        ]);
    }

    private function clearPortfolioCache($user_id)
    {
        if (Auth::check()) {
            $cacheKey = "recommendations_user_$user_id";
            Cache::forget($cacheKey);
        }
    }

    private function getMockPredictions()
    {
        $user = auth()->user();
        $currentMonth = now();

        $averageExpense = 0;
        for ($i = 1; $i <= 3; $i++) {
            $date = $currentMonth->copy()->subMonths($i);

            // REFRACTORED: Menggunakan whereYear & whereMonth agar mendukung MySQL dan PostgreSQL
            $monthExpense = $user->transactions()
                ->expense()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $averageExpense += $monthExpense;
        }
        $averageExpense = $averageExpense / 3;

        $predictions = [];
        for ($i = 1; $i <= 3; $i++) {
            $predictedMonth = $currentMonth->copy()->addMonths($i);

            $variance = rand(-20, 20) / 100;
            $predictedAmount = $averageExpense * (1 + $variance);

            $predictions[] = [
                'month'             => $predictedMonth->format('M Y'),
                'predicted_expense' => $predictedAmount,
                'confidence'        => rand(75, 90),
                'trend'             => $variance > 0 ? 'increase' : 'decrease',
            ];
        }

        return $predictions;
    }

    private function getMockRecommendations()
    {
        $user = auth()->user();
        $now = now();

        // REFRACTORED: Menggunakan whereYear & whereMonth agar mendukung MySQL dan PostgreSQL
        $categoryExpenses = $user->transactions()
            ->expense()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $recommendations = [];

        foreach ($categoryExpenses as $expense) {
            if ($expense->total > 1000000) {
                $recommendations[] = [
                    'type'              => 'reduce_spending',
                    'category'          => $expense->category->name,
                    'current_amount'    => $expense->total,
                    'suggested_amount'  => $expense->total * 0.8,
                    'potential_savings' => $expense->total * 0.2,
                    'tips'               => $this->getTipsForCategory($expense->category->name),
                    'priority'          => $expense->total > 2000000 ? 'high' : 'medium',
                ];
            }
        }

        $recommendations[] = [
            'type'              => 'emergency_fund',
            'category'          => 'Savings',
            'current_amount'    => 0,
            'suggested_amount'  => $user->totalIncome() * 0.1,
            'potential_savings' => $user->totalIncome() * 0.1,
            'tips'               => ['Set up automatic savings', 'Aim for 3-6 months of expenses', 'Use high-yield savings account'],
            'priority'          => 'high',
        ];

        return $recommendations;
    }

    private function getTipsForCategory($categoryName)
    {
        $tips = [
            'Food & Dining' => [
                'Cook more meals at home',
                'Use food delivery promo codes',
                'Buy groceries in bulk',
                'Plan your meals weekly'
            ],
            'Transportation' => [
                'Use public transportation',
                'Carpool with colleagues',
                'Walk or bike for short distances',
                'Combine multiple errands in one trip'
            ],
            'Shopping' => [
                'Make a shopping list before going out',
                'Wait 24 hours before buying non-essentials',
                'Compare prices online',
                'Buy during sales or discount periods'
            ],
            'Entertainment' => [
                'Cancel unused subscriptions',
                'Look for free entertainment options',
                'Share family plans with friends',
                'Set a monthly entertainment budget'
            ],
        ];

        return $tips[$categoryName] ?? ['Review your spending in this category', 'Set a monthly budget limit', 'Track expenses regularly'];
    }

    private function storeMetric(string $key, $value)
    {
        $expiry = 1440 * 60;

        $data = Cache::get($key, []);
        $data[] = $value;

        if (count($data) > 100) {
            $data = array_slice($data, -100);
        }

        Cache::put($key, $data, $expiry);
    }
}
