<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
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

    public function index(Request $request)
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');

        // --- !! FILTER LOGIC START !! ---
        // Ambil filter dari request, default 'daily'
        $filter = $request->input('filter', 'daily');
        // --- !! FILTER LOGIC END !! ---


        // ... (Kode untuk $totalIncome, $totalExpense, $currentBalance, $monthlyIncome, $monthlyExpense) ...
        $totalIncome = $user->totalIncome();
        $totalExpense = $user->totalExpense();
        $currentBalance = $totalIncome - $totalExpense;
        $monthlyIncome = $user->transactions()->income()->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])->sum('amount');
        $monthlyExpense = $user->transactions()->expense()->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])->sum('amount');


        // ... (Kode untuk $recentTransactions) ...
        $recentTransactions = $user->transactions()->with('category')->orderBy('date', 'desc')->orderBy('created_at', 'desc')->limit(5)->get();


        // --- !! CHART DATA LOGIC CHANGES START !! ---
        $chartData = [];
        $chartLabels = [];
        $chartIncomeData = [];
        $chartExpenseData = [];
        $chartTitle = ''; // Judul grafik dinamis

        if ($filter === 'daily') {
            $chartTitle = 'Daily Trend (Last 7 Days)';
            // Ambil data 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateString = $date->format('Y-m-d');
                $label = $date->format('D, M d'); // Format label: Fri, Oct 24

                $income = $user->transactions()
                    ->income()
                    // Filter berdasarkan tanggal persis
                    ->whereDate('date', $dateString)
                    ->sum('amount');

                $expense = $user->transactions()
                    ->expense()
                    ->whereDate('date', $dateString)
                    ->sum('amount');

                $chartLabels[] = $label;
                $chartIncomeData[] = $income;
                $chartExpenseData[] = $expense;
            }
        } else { // filter === 'monthly'
            $chartTitle = 'Monthly Trend (Last 6 Months)';
            // Logika 6 bulan terakhir Anda (sudah benar)
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i)->format('Y-m');
                $label = now()->subMonths($i)->format('M Y'); // Format label: Oct 2025

                $income = $user->transactions()
                    ->income()
                    ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month])
                    ->sum('amount');

                $expense = $user->transactions()
                    ->expense()
                    ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month])
                    ->sum('amount');

                $chartLabels[] = $label;
                $chartIncomeData[] = $income;
                $chartExpenseData[] = $expense;
            }
        }
        // --- !! CHART DATA LOGIC CHANGES END !! ---


        // ... (Kode untuk $categoryBreakdown) ...
        $categoryBreakdown = $user->transactions()->expense()->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])->select('category_id', DB::raw('SUM(amount) as total'))->with('category')->groupBy('category_id')->orderBy('total', 'desc')->get();


        // ... (Kode untuk $currentMonthBudgets dan $budgetSummary) ...
        $currentMonthBudgetsQuery = $user->budgets()->with('category')->where('month', $currentMonth);
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


        // Kirim data baru ke view
        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'currentBalance',
            'monthlyIncome',
            'monthlyExpense',
            'recentTransactions',
            // 'monthlyData', // Hapus variabel lama
            'categoryBreakdown',
            'budgetSummary',
            'currentMonthBudgets',
            'chartLabels',        // <-- Data Baru
            'chartIncomeData',    // <-- Data Baru
            'chartExpenseData',   // <-- Data Baru
            'chartTitle',         // <-- Data Baru
            'filter'              // <-- Filter aktif
        ));
    }
}
