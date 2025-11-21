<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $portfolioService;

    // 2. INJECT SERVICE VIA CONSTRUCTOR
    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');

        // --- FILTER & DATE RANGE LOGIC START (Tidak berubah) ---
        $filter = $request->input('filter', 'daily');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // --- 3. HITUNG TOTAL INVESTASI (LOGIKA BARU) ---
        $portfolios = $user->portfolios;
        $totalInvestmentValue = 0;
        foreach ($portfolios as $portfolio) {
            $summary = $this->portfolioService->calculatePortfolioSummary($portfolio);
            $totalInvestmentValue += $summary['total_value'];
        }

        // Hitung total cash income & expense (mengabaikan investasi)
        $totalIncome = $user->totalIncome();
        $totalExpense = $user->totalExpense();

        //HITUNG TOTAL LIABILITAS (Hutang)
        $totalLiabilities = Auth::user()->liabilities()->sum('current_balance');

        // Saldo ini sekarang adalah Saldo KAS (Cash Balance)
        $currentBalance = $totalIncome - $totalExpense;

        // Ini adalah Total Kekayaan Bersih Anda
        $totalNetWorth = $currentBalance + $totalInvestmentValue - $totalLiabilities;


        // Hitung income & expense bulanan (KONSUMTIF)s
        $monthlyIncome = $user->transactions()
            ->income()
            ->whereNull('investment_transaction_id')
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->expense()
            ->whereNull('investment_transaction_id')
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->sum('amount');


        // Ambil transaksi KONSUMTIF terbaru
        $recentTransactions = $user->transactions()
            ->with('category')
            ->whereNull('investment_transaction_id')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)->get();


        // --- CHART DATA LOGIC (DIPERBARUI TOTAL) ---
        $chartLabels = [];
        $chartIncomeData = [];
        $chartExpenseData = [];
        $chartTitle = '';
        $startDate = null; // Untuk menyimpan tanggal mulai iterasi
        $endDate = null;   // Untuk menyimpan tanggal akhir iterasi

        // Tentukan Tanggal Mulai dan Akhir berdasarkan Filter dan Input
        if ($filter === 'daily') {
            if ($dateFrom && $dateTo) {
                try {
                    $startDate = Carbon::parse($dateFrom)->startOfDay();
                    $endDate = Carbon::parse($dateTo)->startOfDay();
                    // Batasi maksimal 30 hari
                    if ($startDate->diffInDays($endDate) > 29) { // diffInDays(30) itu 31 hari
                        $startDate = $endDate->copy()->subDays(29);
                        $chartTitle = 'Daily Trend (Last 30 Days)';
                    } else {
                        $chartTitle = 'Daily Trend (' . $startDate->format('M d') . ' - ' . $endDate->format('M d') . ')';
                    }
                } catch (\Exception $e) {
                    // Jika input tanggal tidak valid, fallback ke default 7 hari
                    $endDate = now()->startOfDay();
                    $startDate = now()->subDays(6)->startOfDay();
                    $chartTitle = 'Daily Trend (Last 7 Days)';
                }
            } else {
                // Default 7 hari
                $endDate = now()->startOfDay();
                $startDate = now()->subDays(6)->startOfDay();
                $chartTitle = 'Daily Trend (Last 7 Days)';
            }

            // Loop Harian
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateString = $date->format('Y-m-d');
                $label = $date->format('D, M d');

                // Query (lebih efisien jika digabung, tapi terpisah lebih mudah dibaca)
                $income = $user->transactions()->income()
                    ->whereNull('investment_transaction_id')
                    ->whereDate('date', $dateString)->sum('amount');

                $expense = $user->transactions()->expense()
                    ->whereNull('investment_transaction_id')
                    ->whereDate('date', $dateString)->sum('amount');

                $chartLabels[] = $label;
                $chartIncomeData[] = $income;
                $chartExpenseData[] = $expense;
            }
        } else { // filter === 'monthly'
            if ($dateFrom && $dateTo) {
                try {
                    // Parse sebagai awal bulan
                    $startDate = Carbon::parse($dateFrom)->startOfMonth();
                    $endDate = Carbon::parse($dateTo)->startOfMonth();
                    // Batasi maksimal 12 bulan
                    if ($startDate->diffInMonths($endDate) > 11) { // diffInMonths(12) itu 13 bulan
                        $startDate = $endDate->copy()->subMonths(11);
                        $chartTitle = 'Monthly Trend (Last 12 Months)';
                    } else {
                        $chartTitle = 'Monthly Trend (' . $startDate->format('M Y') . ' - ' . $endDate->format('M Y') . ')';
                    }
                } catch (\Exception $e) {
                    // Fallback ke default 6 bulan
                    $endDate = now()->startOfMonth();
                    $startDate = now()->subMonths(5)->startOfMonth();
                    $chartTitle = 'Monthly Trend (Last 6 Months)';
                }
            } else {
                // Default 6 bulan
                $endDate = now()->startOfMonth();
                $startDate = now()->subMonths(5)->startOfMonth();
                $chartTitle = 'Monthly Trend (Last 6 Months)';
            }

            // Loop Bulanan
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addMonth()) {
                $monthString = $date->format('Y-m');
                $label = $date->format('M Y');

                // Query per bulan (gunakan TO_CHAR atau YEAR/MONTH)
                // Sesuaikan dengan database Anda, TO_CHAR lebih umum tapi bisa lambat
                $income = $user->transactions()->income()
                    ->whereNull('investment_transaction_id')
                    ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$monthString])->sum('amount');

                $expense = $user->transactions()->expense()
                    ->whereNull('investment_transaction_id')
                    ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$monthString])->sum('amount');
                // Alternatif MySQL/MariaDB: ->whereYear('date', $date->year)->whereMonth('date', $date->month)

                $chartLabels[] = $label;
                $chartIncomeData[] = $income;
                $chartExpenseData[] = $expense;
            }
        }


        // Breakdown Kategori (KONSUMTIF)
        $categoryBreakdown = $user->transactions()
            ->expense()
            ->whereNull('investment_transaction_id') // <-- FILTER
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->with('category')->groupBy('category_id')
            ->orderBy('total', 'desc')->get();


        // Budget (Menggunakan data expense yang sudah bersih)
        $currentMonthBudgetsQuery = $user->budgets()->with('category')
            ->whereHas('category', function ($query) {
                $query->where('type', 'expense'); // Hanya kategori expense
            })
            ->where('month', $currentMonth);

        $currentMonthBudgetsUnsorted = clone $currentMonthBudgetsQuery;
        $currentMonthBudgets = $currentMonthBudgetsQuery->get()->sortByDesc(function ($budget) {
            if ($budget->limit > 0) return ($budget->spent / $budget->limit) * 100;
            return -1;
        });

        $totalBudgetLimit = $currentMonthBudgetsUnsorted->sum('limit');
        $budgetSummary = null;
        if ($totalBudgetLimit > 0) {
            $budgetProgress = ($monthlyExpense / $totalBudgetLimit) * 100;
            $budgetRemaining = $totalBudgetLimit - $monthlyExpense;
            $isOverBudget = $monthlyExpense > $totalBudgetLimit;
            $budgetSummary = (object) ['limit' => $totalBudgetLimit, 'spent' => $monthlyExpense, 'remaining' => $budgetRemaining, 'progress' => $budgetProgress, 'isOverBudget' => $isOverBudget];
        }

        // Kirim semua data ke view
        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'currentBalance',
            'monthlyIncome',
            'monthlyExpense',
            'totalInvestmentValue', // <-- DATA BARU
            'totalNetWorth',      // <-- DATA BARU
            'recentTransactions',
            'categoryBreakdown',
            'budgetSummary',
            'currentMonthBudgets',
            'chartLabels',
            'chartIncomeData',
            'chartExpenseData',
            'chartTitle',
            'filter', // Filter aktif (daily/monthly)
            'dateFrom', // Tanggal mulai input (jika ada)
            'dateTo'    // Tanggal akhir input (jika ada)
        ));
    }

    // public function index()
    // {
    //     $user = User::with('transactions', 'budgets')->find(Auth::user()->id);

    //     // Basic stats
    //     $totalIncome = $user->totalIncome();
    //     $totalExpense = $user->totalExpense();
    //     $currentBalance = $totalIncome - $totalExpense;

    //     // Current month stats
    //     $currentMonth = now()->format('Y-m');
    //     $monthlyIncome = $user->transactions()
    //         ->income()
    //         ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
    //         ->sum('amount');

    //     $monthlyExpense = $user->transactions()
    //         ->expense()
    //         ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
    //         ->sum('amount');

    //     // Recent transactions
    //     $recentTransactions = $user->transactions()
    //         ->with('category')
    //         ->orderBy('date', 'desc')
    //         ->orderBy('created_at', 'desc')
    //         ->limit(5)
    //         ->get();

    //     // Monthly chart data (last 6 months)
    //     $monthlyData = [];
    //     for ($i = 5; $i >= 0; $i--) {
    //         $month = now()->subMonths($i)->format('Y-m');
    //         $monthName = now()->subMonths($i)->format('M Y');

    //         $income = $user->transactions()
    //             ->income()
    //             ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
    //             ->sum('amount');

    //         $expense = $user->transactions()
    //             ->expense()
    //             ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
    //             ->sum('amount');

    //         $monthlyData[] = [
    //             'month' => $monthName,
    //             'income' => $income,
    //             'expense' => $expense,
    //         ];
    //     }

    //     // Category breakdown (current month)
    //     $categoryBreakdown = $user->transactions()
    //         ->expense()
    //         ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
    //         ->select('category_id', DB::raw('SUM(amount) as total'))
    //         ->with('category')
    //         ->groupBy('category_id')
    //         ->get();

    //     // Current budget
    //     $currentBudget = $user->budgets()
    //         ->where('month', $currentMonth)
    //         ->first();

    //     if ($currentBudget) {
    //         $currentBudget->updateSpentAmount();
    //     }

    //     // dd([
    //     //     'totalIncome' => $totalIncome,
    //     //     'totalExpense' => $totalExpense,
    //     //     'currentBalance' => $currentBalance,
    //     //     'monthlyIncome' => $monthlyIncome,
    //     //     'monthlyExpense' => $monthlyExpense,
    //     //     'recentTransactions' => $recentTransactions,
    //     //     'monthlyData' => $monthlyData,
    //     //     'categoryBreakdown' => $categoryBreakdown,
    //     //     'currentBudge' => $currentBudget
    //     // ]);
    //     return view('dashboard', compact(
    //         'totalIncome',
    //         'totalExpense',
    //         'currentBalance',
    //         'monthlyIncome',
    //         'monthlyExpense',
    //         'recentTransactions',
    //         'monthlyData',
    //         'categoryBreakdown',
    //         'currentBudget'
    //     ));
    // }
}
