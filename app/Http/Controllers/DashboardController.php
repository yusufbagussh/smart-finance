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

    // INJECT SERVICE VIA CONSTRUCTOR
    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil komponen tahun dan bulan secara terpisah
        $now = now();
        $currentYear = $now->year;
        $currentMonthNumber = $now->month;
        $currentMonth = $now->format('Y-m'); // Tetap digunakan untuk query string 'YYYY-MM' di tabel budget

        // --- FILTER & DATE RANGE LOGIC ---
        $filter = $request->input('filter', 'daily');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // --- HITUNG TOTAL INVESTASI ---
        $portfolios = $user->portfolios;
        $totalInvestmentValue = 0;
        foreach ($portfolios as $portfolio) {
            $summary = $this->portfolioService->calculatePortfolioSummary($portfolio);
            $totalInvestmentValue += $summary['total_value'];
        }

        // Hitung total cash income & expense (mengabaikan investasi)
        $totalIncome = $user->totalIncome();
        $totalExpense = $user->totalExpense();

        // HITUNG TOTAL LIABILITAS (Hutang & Piutang)
        $totalPayables = auth()->user()->liabilities()
            ->where('type', 'payable')
            ->sum('current_balance');

        $totalReceivables = auth()->user()->liabilities()
            ->where('type', 'receivable')
            ->sum('current_balance');

        // Saldo KAS (Cash Balance)
        $currentBalance = $user->accounts()->sum('current_balance');

        // Total Kekayaan Bersih
        $totalNetWorth = $currentBalance + $totalInvestmentValue + $totalReceivables - $totalPayables;

        // Hitung income & expense bulanan (KONSUMTIF)
        $monthlyIncome = $user->transactions()
            ->income()
            ->whereNull('investment_transaction_id')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNumber)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->expense()
            ->whereNull('investment_transaction_id')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNumber)
            ->sum('amount');

        // Ambil transaksi KONSUMTIF terbaru
        $recentTransactions = $user->transactions()
            ->with('category')
            ->whereNull('investment_transaction_id')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // --- CHART DATA LOGIC ---
        $chartLabels = [];
        $chartIncomeData = [];
        $chartExpenseData = [];
        $chartTitle = '';
        $startDate = null;
        $endDate = null;

        if ($filter === 'daily') {
            if ($dateFrom && $dateTo) {
                try {
                    $startDate = Carbon::parse($dateFrom)->startOfDay();
                    $endDate = Carbon::parse($dateTo)->startOfDay();
                    if ($startDate->diffInDays($endDate) > 29) {
                        $startDate = $endDate->copy()->subDays(29);
                        $chartTitle = 'Daily Trend (Last 30 Days)';
                    } else {
                        $chartTitle = 'Daily Trend (' . $startDate->format('M d') . ' - ' . $endDate->format('M d') . ')';
                    }
                } catch (\Exception $e) {
                    $endDate = now()->startOfDay();
                    $startDate = now()->subDays(6)->startOfDay();
                    $chartTitle = 'Daily Trend (Last 7 Days)';
                }
            } else {
                $endDate = now()->startOfDay();
                $startDate = now()->subDays(6)->startOfDay();
                $chartTitle = 'Daily Trend (Last 7 Days)';
            }

            // Loop Harian
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateString = $date->format('Y-m-d');
                $label = $date->format('D, M d');

                $income = $user->transactions()->income()
                    ->whereNull('investment_transaction_id')
                    ->whereDate('date', $dateString)
                    ->sum('amount');

                $expense = $user->transactions()->expense()
                    ->whereNull('investment_transaction_id')
                    ->whereDate('date', $dateString)
                    ->sum('amount');

                $chartLabels[] = $label;
                $chartIncomeData[] = $income;
                $chartExpenseData[] = $expense;
            }
        } else { // filter === 'monthly'
            if ($dateFrom && $dateTo) {
                try {
                    $startDate = Carbon::parse($dateFrom)->startOfMonth();
                    $endDate = Carbon::parse($dateTo)->startOfMonth();
                    if ($startDate->diffInMonths($endDate) > 11) {
                        $startDate = $endDate->copy()->subMonths(11);
                        $chartTitle = 'Monthly Trend (Last 12 Months)';
                    } else {
                        $chartTitle = 'Monthly Trend (' . $startDate->format('M Y') . ' - ' . $endDate->format('M Y') . ')';
                    }
                } catch (\Exception $e) {
                    $endDate = now()->startOfMonth();
                    $startDate = now()->subMonths(5)->startOfMonth();
                    $chartTitle = 'Monthly Trend (Last 6 Months)';
                }
            } else {
                $endDate = now()->startOfMonth();
                $startDate = now()->subMonths(5)->startOfMonth();
                $chartTitle = 'Monthly Trend (Last 6 Months)';
            }

            // Loop Bulanan
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addMonth()) {
                $label = $date->format('M Y');

                // DIBERSIHKAN: Menggunakan whereYear dan whereMonth
                $income = $user->transactions()->income()
                    ->whereNull('investment_transaction_id')
                    ->whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->sum('amount');

                $expense = $user->transactions()->expense()
                    ->whereNull('investment_transaction_id')
                    ->whereYear('date', $date->year)
                    ->whereMonth('date', $date->month)
                    ->sum('amount');

                $chartLabels[] = $label;
                $chartIncomeData[] = $income;
                $chartExpenseData[] = $expense;
            }
        }

        // Breakdown Kategori (KONSUMTIF)
        $categoryBreakdown = $user->transactions()
            ->expense()
            ->whereNull('investment_transaction_id')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonthNumber)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->get();

        // Budget
        $currentMonthBudgetsQuery = $user->budgets()->with('category')
            ->whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })
            ->where('month', $currentMonth);

        $currentMonthBudgets = $currentMonthBudgetsQuery->get();

        $currentMonthBudgetsSorted = $currentMonthBudgets->sortByDesc(function ($budget) {
            if ($budget->limit > 0) return ($budget->spent / $budget->limit) * 100;
            return -1;
        });

        $totalBudgetLimit = $currentMonthBudgets->sum('limit');
        $budgetSummary = null;

        if ($totalBudgetLimit > 0) {
            $budgetedCategoryIds = $currentMonthBudgets->pluck('category_id')->toArray();
            $budgetedExpense = $user->transactions()
                ->expense()
                ->whereNull('investment_transaction_id')
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $currentMonthNumber)
                ->whereIn('category_id', $budgetedCategoryIds)
                ->sum('amount');

            $budgetProgress = ($budgetedExpense / $totalBudgetLimit) * 100;
            $budgetRemaining = $totalBudgetLimit - $budgetedExpense;
            $isOverBudget = $budgetedExpense > $totalBudgetLimit;

            $budgetSummary = (object) [
                'limit'        => $totalBudgetLimit,
                'spent'        => $budgetedExpense,
                'remaining'    => $budgetRemaining,
                'progress'     => $budgetProgress,
                'isOverBudget' => $isOverBudget
            ];
        }

        return view('dashboard', [
            'totalIncome'          => $totalIncome,
            'totalExpense'         => $totalExpense,
            'currentBalance'       => $currentBalance,
            'monthlyIncome'        => $monthlyIncome,
            'monthlyExpense'       => $monthlyExpense,
            'totalInvestmentValue' => $totalInvestmentValue,
            'totalNetWorth'        => $totalNetWorth,
            'recentTransactions'   => $recentTransactions,
            'categoryBreakdown'    => $categoryBreakdown,
            'budgetSummary'        => $budgetSummary,
            'currentMonthBudgets'  => $currentMonthBudgetsSorted,
            'chartLabels'          => $chartLabels,
            'chartIncomeData'      => $chartIncomeData,
            'chartExpenseData'     => $chartExpenseData,
            'chartTitle'           => $chartTitle,
            'filter'               => $filter,
            'dateFrom'             => $dateFrom,
            'dateTo'               => $dateTo,
            'totalReceivables'     => $totalReceivables,
            'totalPayables'        => $totalPayables,
        ]);
    }
}
