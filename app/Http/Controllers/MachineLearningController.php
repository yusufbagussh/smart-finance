<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MachineLearningController extends Controller
{
    private ?string $mlBaseUrl = null;
    // public function index()
    // {
    //     $user = auth()->user();
    //     // Mock classification accuracy
    //     $classificationStats = [
    //         'total_transactions' => $user->transactions()->count(),
    //         'auto_classified' => rand(60, 85),
    //         'accuracy_rate' => rand(85, 95),
    //     ];

    //     // Mock prediction data
    //     $predictions = $this->getMockPredictions();

    //     // Mock recommendations
    //     $recommendations = $this->getMockRecommendations();

    //     return view('ml.index', compact('classificationStats', 'predictions', 'recommendations'));
    // }

    public function __construct()
    {
        $this->mlBaseUrl = env('ML_BASE_URL', 'http://localhost:5000');
    }

    public function index()
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');
        $currentYear = now()->year;
        $currentMonthNum = now()->month;

        // --- 1. Classification Stats (Disederhanakan) ---
        $classificationStats = [
            'total_transactions' => $user->transactions()->count(),
            // Kita hapus mock accuracy, ganti dengan info model
            'model_info' => 'Model Naive Bayes dilatih pada data Anda.',
        ];

        // --- 2. Fetch Actual Next Month Prediction ---
        $nextMonthPredictionSummary = "N/A"; // Default

        // Ambil data pengeluaran harian (sama seperti di predictions())
        $dailySpending = $user->transactions()
            ->where('type', 'expense')
            ->selectRaw('DATE(date) as date, SUM(amount) as amount')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        if ($dailySpending->count() > 10) { // Hanya panggil jika data cukup
            try {
                $responsePredict = Http::timeout(15)->post($this->mlBaseUrl . '/predict', $dailySpending);
                if ($responsePredict->successful()) {
                    // Ambil HANYA total prediksi
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
        $recommendationPreview = [['type' => 'info', 'message' => 'Analisis sedang berjalan...']]; // Default

        // Ambil Anggaran & Hitung Summary (Kita butuh summary untuk Gemini)
        $currentMonthBudgets = $user->budgets()
            ->with('category')
            ->where('month', $currentMonth)
            ->get();
        $totalBudgetLimit = $currentMonthBudgets->sum('limit');
        $monthlyExpense = $user->transactions() // Ambil total expense bulan ini
            ->expense()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->sum('amount');
        $monthlyIncome = $user->transactions() // Ambil total income bulan ini
            ->income()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->sum('amount');

        $budgetSummary = null;
        if ($totalBudgetLimit > 0) {
            $budgetProgress = ($monthlyExpense / $totalBudgetLimit) * 100;
            $budgetRemaining = $totalBudgetLimit - $monthlyExpense;
            $isOverBudget = $monthlyExpense > $totalBudgetLimit;
            $budgetSummary = (object) ['limit' => $totalBudgetLimit, 'spent' => $monthlyExpense, 'remaining' => $budgetRemaining, 'progress' => $budgetProgress, 'isOverBudget' => $isOverBudget];
        }

        // Ambil Transaksi (untuk Python)
        $transactions = $user->transactions()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->with('category') // Eager load category
            ->get(['description', 'category_id', 'amount', 'type', 'date']);

        // Payload untuk Python API
        $payload = [
            'budgets' => $currentMonthBudgets->map(function ($b) { // Map data budget
                return [
                    'category' => $b->category->name ?? 'Uncategorized',
                    'budget' => $b->limit,
                    'spent' => $b->spent // Asumsi spent sudah di-update
                ];
            }),
            'transactions' => $transactions->map(function ($t) { // Map data transaksi
                return [
                    'description' => $t->description,
                    'category' => $t->category->name ?? 'Uncategorized',
                    'amount' => $t->amount,
                    'type' => $t->type,
                    'date' => $t->date,
                ];
            })
        ];

        // Panggil API Python
        $pythonInsights = [];
        try {
            $responsePython = Http::timeout(15)->post($this->mlBaseUrl . '/recommend', $payload);
            if ($responsePython->successful()) {
                $pythonInsights = $responsePython->json('insights', []);
                // Ambil 1-2 insight pertama untuk preview
                $recommendationPreview = array_slice($pythonInsights, 0, 2);
                if (empty($recommendationPreview)) { // Jika Python tidak mengembalikan apa2
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

        // Kita tidak panggil Gemini di sini agar halaman index cepat

        // --- Kirim Data ke View ---
        return view('ml.index', compact(
            'classificationStats',
            'nextMonthPredictionSummary', // Ganti nama variabel
            'recommendationPreview'       // Ganti nama variabel
        ));
    }

    public function classifyTransaction(Request $request)
    {
        $description = $request->input('description', '');
        if (empty($description)) {
            return response()->json(['error' => 'Description cannot be empty'], 400);
        }

        try {
            // 1. Panggil API Python (Flask)
            $response = Http::post($this->mlBaseUrl . '/classify', [
                'description' => $description,
            ]);

            // 2. Periksa apakah panggilan API sukses
            if ($response->successful()) {
                $mlResult = $response->json();

                // 3. Dapatkan nama kategori & tipe dari hasil ML
                $suggestedCategoryName = $mlResult['predicted_category'] ?? 'Other Expense';
                $predictedType = $mlResult['predicted_type'] ?? 'expense'; // Default ke expense jika gagal

                // 4. Cari Category ID di database Laravel berdasarkan nama
                $category = Category::where('name', $suggestedCategoryName)->first();

                // 5. Kembalikan JSON ke frontend (create.blade.php)
                return response()->json([
                    'suggested_category_id' => $category ? $category->category_id : null, // Ganti ke category_id
                    'predicted_category' => $suggestedCategoryName,
                    'confidence_category' => $mlResult['confidence_category'] ?? 0,
                    'predicted_type' => $predictedType,              // <-- Kirim tipe
                    'confidence_type' => $mlResult['confidence_type'] ?? 0, // <-- Kirim confidence tipe
                    'explanation' => $mlResult['explanation'] ?? 'Classification failed.',
                ]);
            } else {
                Log::error('ML API Classification Failed: ' . $response->body());
                // Jika API error, kirim response error tapi jangan crash
                return response()->json([
                    'error' => 'Classification service error.',
                    'suggested_category_id' => null,
                    'predicted_type' => 'expense', // Default
                ], 500); // Kirim status 500
            }
        } catch (\Exception $e) {
            Log::error('ML API Connection Failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Could not connect to classification service.',
                'suggested_category_id' => null,
                'predicted_type' => 'expense', // Default
            ], 500); // Kirim status 500
        }
    }

    // public function predictions()
    // {
    //     $predictions = $this->getMockPredictions();
    //     return view('ml.predictions', compact('predictions'));
    // }

    public function predictions()
    {
        // --- LOGIKA BARU YANG LEBIH EFISIEN ---

        // 1. Ambil riwayat transaksi, TAPI SUDAH DI-AGREGASI (DIJUMLAHKAN) PER HARI
        // Query ini meminta database untuk:
        // - Mengambil HANYA PENGELUARAN (expense)
        // - Mengelompokkannya berdasarkan TANGGAL (GROUP BY date)
        // - Menjumlahkan total 'amount' untuk setiap hari itu (SUM(amount))

        $dailySpending = auth()->user()->transactions()
            ->where('type', 'expense')
            // 'DATE(date)' -> memastikan kita mengabaikan jam/menit/detik
            ->selectRaw('DATE(date) as date, SUM(amount) as amount')
            ->groupBy('date') // Ini adalah kunci efisiensinya
            ->orderBy('date', 'asc')
            ->get()
            ->toArray(); // Hasilnya: [{date: '2025-01-01', amount: 150000}, {date: '2025-01-02', amount: 75000}, ...]
        // Dengan cara ini, bahkan jika Anda punya 100.000 transaksi
        // selama 3 tahun, Anda hanya akan mengirim ~1095 baris data (365 hari * 3).
        // Ini SANGAT ringan dan cepat.

        $forecastData = [];
        $nextMonthPrediction = "Rp 0";
        // Hanya panggil API jika user punya data (misal: lebih dari 10 hari transaksi)
        if (count($dailySpending) > 10) {
            try {
                // 2. Panggil API Python /predict dengan data yang SUDAH DIJUMLAHKAN
                $response = Http::post($this->mlBaseUrl . '/predict', $dailySpending); // $dailySpending, BUKAN $transactions
                // dd($response->json());
                if ($response->successful()) {
                    $data = $response->json();
                    $forecastData = $data['forecast_data'] ?? [];
                    $nextMonthPrediction = $data['next_month_total'] ?? "Error";
                } else {
                    $nextMonthPrediction = "Error: Service not responding";
                }
            } catch (\Exception $e) {
                $nextMonthPrediction = "Error: " . $e->getMessage();
            }
        } else {
            $nextMonthPrediction = "Butuh lebih banyak data transaksi untuk prediksi.";
        }

        // dd([
        //     'forecastData' => $forecastData,
        //     'nextMonthPrediction' => $nextMonthPrediction
        // ]);

        // 3. Kirim data ke view
        return view('ml.predictions', [
            'forecastData' => $forecastData,
            'nextMonthPrediction' => $nextMonthPrediction
        ]);
    }

    // public function recommendations()
    // {
    //     $recommendations = $this->getMockRecommendations();
    //     return view('ml.recommendations', compact('recommendations'));
    // }

    public function recommendations()
    {
        // 1. Tentukan bulan ini
        $currentMonth = now()->format('Y-m');
        $currentYear = now()->year;
        $currentMonthNum = now()->month;
        $user = auth()->user();

        // Ambil Anggaran & Hitung Summary (Kita butuh summary untuk Gemini)
        $currentMonthBudgets = $user->budgets()
            ->with('category')
            ->where('month', $currentMonth)
            ->get();
        $totalBudgetLimit = $currentMonthBudgets->sum('limit');
        $monthlyExpense = $user->transactions() // Ambil total expense bulan ini
            ->expense()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->sum('amount');
        $monthlyIncome = $user->transactions() // Ambil total income bulan ini
            ->income()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->sum('amount');

        $budgetSummary = null;
        if ($totalBudgetLimit > 0) {
            $budgetProgress = ($monthlyExpense / $totalBudgetLimit) * 100;
            $budgetRemaining = $totalBudgetLimit - $monthlyExpense;
            $isOverBudget = $monthlyExpense > $totalBudgetLimit;
            $budgetSummary = (object) ['limit' => $totalBudgetLimit, 'spent' => $monthlyExpense, 'remaining' => $budgetRemaining, 'progress' => $budgetProgress, 'isOverBudget' => $isOverBudget];
        }

        // Ambil Transaksi (untuk Python)
        $transactions = $user->transactions()
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNum)
            ->with('category') // Eager load category
            ->get(['description', 'category_id', 'amount', 'type', 'date']);

        // Payload untuk Python API
        $payload = [
            'budgets' => $currentMonthBudgets->map(function ($b) { // Map data budget
                return [
                    'category' => $b->category->name ?? 'Uncategorized',
                    'budget' => $b->limit,
                    'spent' => $b->spent // Asumsi spent sudah di-update
                ];
            }),
            'transactions' => $transactions->map(function ($t) { // Map data transaksi
                return [
                    'description' => $t->description,
                    'category' => $t->category->name ?? 'Uncategorized',
                    'amount' => $t->amount,
                    'type' => $t->type,
                    'date' => $t->date,
                ];
            })
        ];

        // 7. Panggil API Python /recommend
        $pythonInsights = []; // Default jika Python gagal
        try {
            $responsePython = Http::post($this->mlBaseUrl . '/recommend', $payload);
            if ($responsePython->successful()) {
                $pythonInsights = $responsePython->json('insights', []);
            } else {
                Log::error('Python API /recommend failed: ' . $responsePython->status() . ' - ' . $responsePython->body());
            }
        } catch (\Exception $e) {
            Log::error('Python API /recommend connection failed: ' . $e->getMessage());
        }

        // // 8. Kirim data ke view
        // return view('ml.recommendations', [
        //     'recommendations' => $recommendations
        // ]);

        // --- !! LANGKAH BARU: PANGGIL GEMINI API !! ---
        // --- Langkah 8: Panggil Gemini API (dengan Prompt yang Diperkaya) ---
        $geminiRecommendationText = "Maaf, ringkasan AI tidak dapat dimuat saat ini."; // Default

        $apiKey = env('GEMINI_API_KEY'); // Ambil dari config atau .env
        if ($apiKey) { // Hanya panggil jika API key ada
            // $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey;

            // Buat Prompt yang Diperkaya
            $prompt = "Anda adalah asisten keuangan pribadi yang ramah dan memotivasi untuk aplikasi Smart Finance.\n\n";
            $prompt .= "Berikut ringkasan kondisi keuangan pengguna bulan ini:\n";
            $prompt .= "- Total Pemasukan: Rp " . number_format($monthlyIncome, 0, ',', '.') . "\n";
            $prompt .= "- Total Pengeluaran: Rp " . number_format($monthlyExpense, 0, ',', '.') . "\n";
            if ($budgetSummary) {
                $prompt .= "- Total Anggaran: Rp " . number_format($budgetSummary->limit, 0, ',', '.') . "\n";
                $prompt .= "- Status Anggaran: " . ($budgetSummary->isOverBudget ? "Melebihi batas!" : number_format($budgetSummary->progress, 1) . "% terpakai") . "\n";
            } else {
                $prompt .= "- Status Anggaran: (Belum diatur)\n";
            }
            $prompt .= "\nAnalisis AI detail menemukan wawasan berikut:\n";
            if (!empty($pythonInsights)) {
                foreach ($pythonInsights as $insight) {
                    // Hanya sertakan pesan, tipe bisa disimpulkan Gemini
                    $prompt .= "- " . $insight['message'] . "\n";
                }
            } else {
                $prompt .= "- Tidak ada temuan spesifik yang perlu perhatian khusus bulan ini.\n";
            }
            $prompt .= "\n\nTugas Anda:\n";
            $prompt .= "1. Berikan ringkasan singkat (1-2 kalimat) tentang kondisi keuangan pengguna secara keseluruhan bulan ini berdasarkan data ringkasan.\n";
            $prompt .= "2. Pilih 1-2 wawasan *paling penting* dari analisis AI detail (jika ada) dan jelaskan dengan bahasa yang memotivasi dan actionable.\n";
            $prompt .= "3. Berikan SATU tips keuangan umum tambahan yang relevan dengan kondisi atau wawasan pengguna.\n";
            $prompt .= "4. Jaga agar total respons tetap ringkas (maksimal 4-5 kalimat).\n";
            $prompt .= "5. Format sebagai teks biasa (plain text).\n";
            $prompt .= "Respons Anda:";

            try {
                $responseGemini = Gemini::generativeModel(model: 'gemini-2.5-pro')->generateContent($prompt); // Sesuaikan model jika perlu (geminiPro(), etc.)                dd($responseGemini->json());
                if ($responseGemini->text()) {
                    $geminiRecommendationText = $responseGemini->text();
                    // Ekstrak teks dari respons Gemini
                    // $geminiRecommendationText = $responseGemini->json('candidates.0.content.parts.0.text', $geminiRecommendationText);
                } else {
                    Log::error('Gemini API call failed: ' . json_encode($responseGemini));
                    // Jika Gemini gagal, gunakan pesan default atau mungkin hanya insight Python
                    // Untuk sekarang, kita biarkan pakai default message.
                }
            } catch (\Exception $e) {
                Log::error('Gemini API connection failed: ' . $e->getMessage());
                // Jika koneksi gagal, gunakan pesan default
            }
        }
        // --- !! AKHIR LANGKAH GEMINI !! ---
        // 8. Kirim Teks Final dari Gemini ke View
        return view('ml.recommendations', [
            'geminiRecommendationText' => $geminiRecommendationText, // Teks dari Gemini
            'pythonInsights' => $pythonInsights                   // Array wawasan dari Python
        ]);
    }

    private function getMockPredictions()
    {
        $user = auth()->user();
        $currentMonth = now();

        // Calculate average monthly expense for last 3 months
        $averageExpense = 0;
        for ($i = 1; $i <= 3; $i++) {
            $month = $currentMonth->copy()->subMonths($i)->format('Y-m');
            $monthExpense = $user->transactions()
                ->expense()
                ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month])
                ->sum('amount');
            $averageExpense += $monthExpense;
        }
        $averageExpense = $averageExpense / 3;

        // Mock predictions for next 3 months
        $predictions = [];
        for ($i = 1; $i <= 3; $i++) {
            $predictedMonth = $currentMonth->copy()->addMonths($i);

            // Add some randomness to make it realistic
            $variance = rand(-20, 20) / 100; // ±20%
            $predictedAmount = $averageExpense * (1 + $variance);

            $predictions[] = [
                'month' => $predictedMonth->format('M Y'),
                'predicted_expense' => $predictedAmount,
                'confidence' => rand(75, 90),
                'trend' => $variance > 0 ? 'increase' : 'decrease',
            ];
        }

        return $predictions;
    }

    private function getMockRecommendations()
    {
        $user = auth()->user();

        // Get current month expenses by category
        $currentMonth = now()->format('Y-m');
        $categoryExpenses = $user->transactions()
            ->expense()
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $recommendations = [];

        // Mock recommendations based on spending patterns
        foreach ($categoryExpenses as $expense) {
            if ($expense->total > 1000000) { // If spending > 1M IDR
                $recommendations[] = [
                    'type' => 'reduce_spending',
                    'category' => $expense->category->name,
                    'current_amount' => $expense->total,
                    'suggested_amount' => $expense->total * 0.8,
                    'potential_savings' => $expense->total * 0.2,
                    'tips' => $this->getTipsForCategory($expense->category->name),
                    'priority' => $expense->total > 2000000 ? 'high' : 'medium',
                ];
            }
        }

        // Add general recommendations
        $recommendations[] = [
            'type' => 'emergency_fund',
            'category' => 'Savings',
            'current_amount' => 0,
            'suggested_amount' => $user->totalIncome() * 0.1,
            'potential_savings' => $user->totalIncome() * 0.1,
            'tips' => ['Set up automatic savings', 'Aim for 3-6 months of expenses', 'Use high-yield savings account'],
            'priority' => 'high',
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
}
