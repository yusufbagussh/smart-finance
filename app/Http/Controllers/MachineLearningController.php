<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MachineLearningController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Mock classification accuracy
        $classificationStats = [
            'total_transactions' => $user->transactions()->count(),
            'auto_classified' => rand(60, 85),
            'accuracy_rate' => rand(85, 95),
        ];

        // Mock prediction data
        $predictions = $this->getMockPredictions();

        // Mock recommendations
        $recommendations = $this->getMockRecommendations();

        return view('ml.index', compact('classificationStats', 'predictions', 'recommendations'));
    }

    public function classifyTransaction(Request $request)
    {
        $description = $request->input('description', '');

        // --- HAPUS LOGIKA MOCK LAMA ---
        /* $mockClassifications = [ ... ];
        foreach ($mockClassifications as $keyword => $categoryName) { ... }
        */
        // --- AKHIR LOGIKA MOCK LAMA ---


        // --- LOGIKA API PYTHON BARU ---
        try {
            // 1. Panggil API Python (Flask) Anda
            $response = Http::post('http://127.0.0.1:5000/classify', [
                'description' => $description,
            ]);

            // 2. Periksa apakah panggilan API sukses
            if ($response->successful()) {
                $mlResult = $response->json();

                // 3. Dapatkan nama kategori dari hasil ML
                $suggestedCategoryName = $mlResult['category_name'] ?? 'Other Expense';

                // 4. Cari Category ID di database Laravel berdasarkan nama
                //    Ini sama seperti logika Anda sebelumnya
                $category = Category::where('name', $suggestedCategoryName)->first();

                // 5. Kembalikan JSON ke frontend (create.blade.php)
                return response()->json([
                    'suggested_category' => $category ? $category->category_id : null,
                    'category_name' => $suggestedCategoryName,
                    'confidence' => $mlResult['confidence'] ?? 0.5,
                    'explanation' => $mlResult['explanation'] ?? 'Classification failed.',
                ]);
            }
        } catch (\Exception $e) {
            // (Opsional) Catat error jika API Python mati atau error
            Log::error('ML API Classification Failed: ' . $e->getMessage());
        }

        // Jika API gagal, kembalikan respon default
        // agar frontend tidak rusak
        return response()->json([
            'suggested_category' => null,
            'category_name' => 'Error',
            'confidence' => 0,
            'explanation' => 'Could not connect to classification service.',
        ], 500);
    }

    public function predictions()
    {
        $predictions = $this->getMockPredictions();
        return view('ml.predictions', compact('predictions'));
    }

    // public function predictions()
    // {
    //     // --- LOGIKA BARU YANG LEBIH EFISIEN ---

    //     // 1. Ambil riwayat transaksi, TAPI SUDAH DI-AGREGASI (DIJUMLAHKAN) PER HARI
    //     // Query ini meminta database untuk:
    //     // - Mengambil HANYA PENGELUARAN (expense)
    //     // - Mengelompokkannya berdasarkan TANGGAL (GROUP BY date)
    //     // - Menjumlahkan total 'amount' untuk setiap hari itu (SUM(amount))

    //     $dailySpending = auth()->user()->transactions()
    //         ->where('type', 'expense')
    //         // 'DATE(date)' -> memastikan kita mengabaikan jam/menit/detik
    //         ->selectRaw('DATE(date) as date, SUM(amount) as amount')
    //         ->groupBy('date') // Ini adalah kunci efisiensinya
    //         ->orderBy('date', 'asc')
    //         ->get()
    //         ->toArray(); // Hasilnya: [{date: '2025-01-01', amount: 150000}, {date: '2025-01-02', amount: 75000}, ...]
    //     // Dengan cara ini, bahkan jika Anda punya 100.000 transaksi
    //     // selama 3 tahun, Anda hanya akan mengirim ~1095 baris data (365 hari * 3).
    //     // Ini SANGAT ringan dan cepat.

    //     $forecastData = [];
    //     $nextMonthPrediction = "Rp 0";
    //     // Hanya panggil API jika user punya data (misal: lebih dari 10 hari transaksi)
    //     if (count($dailySpending) > 10) {
    //         try {
    //             // 2. Panggil API Python /predict dengan data yang SUDAH DIJUMLAHKAN
    //             $response = Http::post('http://127.0.0.1:5000/predict', $dailySpending); // $dailySpending, BUKAN $transactions
    //             // dd($response->json());
    //             if ($response->successful()) {
    //                 $data = $response->json();
    //                 $forecastData = $data['forecast_data'] ?? [];
    //                 $nextMonthPrediction = $data['next_month_total'] ?? "Error";
    //             } else {
    //                 $nextMonthPrediction = "Error: Service not responding";
    //             }
    //         } catch (\Exception $e) {
    //             $nextMonthPrediction = "Error: " . $e->getMessage();
    //         }
    //     } else {
    //         $nextMonthPrediction = "Butuh lebih banyak data transaksi untuk prediksi.";
    //     }

    //     // dd([
    //     //     'forecastData' => $forecastData,
    //     //     'nextMonthPrediction' => $nextMonthPrediction
    //     // ]);

    //     // 3. Kirim data ke view
    //     return view('ml.predictions', [
    //         'forecastData' => $forecastData,
    //         'nextMonthPrediction' => $nextMonthPrediction
    //     ]);
    // }


    public function recommendations()
    {
        $recommendations = $this->getMockRecommendations();
        return view('ml.recommendations', compact('recommendations'));
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
