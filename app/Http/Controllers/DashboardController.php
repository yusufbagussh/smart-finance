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
    public function index()
    {
        $user = User::with('transactions', 'budgets')->find(Auth::user()->id);

        // Basic stats
        $totalIncome = $user->totalIncome();
        $totalExpense = $user->totalExpense();
        $currentBalance = $totalIncome - $totalExpense;

        // Current month stats
        $currentMonth = now()->format('Y-m');
        $monthlyIncome = $user->transactions()
            ->income()
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->expense()
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->sum('amount');

        // Recent transactions
        $recentTransactions = $user->transactions()
            ->with('category')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly chart data (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthName = now()->subMonths($i)->format('M Y');

            $income = $user->transactions()
                ->income()
                ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
                ->sum('amount');

            $expense = $user->transactions()
                ->expense()
                ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
                ->sum('amount');

            $monthlyData[] = [
                'month' => $monthName,
                'income' => $income,
                'expense' => $expense,
            ];
        }

        // Category breakdown (current month)
        $categoryBreakdown = $user->transactions()
            ->expense()
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->get();

        // Current budget
        $currentBudget = $user->budgets()
            ->where('month', $currentMonth)
            ->first();

        if ($currentBudget) {
            $currentBudget->updateSpentAmount();
        }

        // dd([
        //     'totalIncome' => $totalIncome,
        //     'totalExpense' => $totalExpense,
        //     'currentBalance' => $currentBalance,
        //     'monthlyIncome' => $monthlyIncome,
        //     'monthlyExpense' => $monthlyExpense,
        //     'recentTransactions' => $recentTransactions,
        //     'monthlyData' => $monthlyData,
        //     'categoryBreakdown' => $categoryBreakdown,
        //     'currentBudge' => $currentBudget
        // ]);
        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'currentBalance',
            'monthlyIncome',
            'monthlyExpense',
            'recentTransactions',
            'monthlyData',
            'categoryBreakdown',
            'currentBudget'
        ));
    }
}
