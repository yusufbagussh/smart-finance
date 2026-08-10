<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')
            ->whereHas('transactions', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->count();

        $totalTransactions = Transaction::count();
        $totalCategories = Category::count();

        // Total Revenue & Expenses (all users)
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');

        // Recent Users (last 10)
        $recentUsers = User::where('role', 'user')
            ->latest()
            ->limit(10)
            ->get();

        // User Activity (users with most transactions)
        $topUsers = User::where('role', 'user')
            ->withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->limit(5)
            ->get();

        // Monthly Transaction Growth (last 6 months)
        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');

            // REFRACTORED: Menggunakan whereYear & whereMonth agar aman di MySQL maupun PostgreSQL
            $userCount = User::where('role', 'user')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $transactionCount = Transaction::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyGrowth[] = [
                'month'        => $monthName,
                'users'        => $userCount,
                'transactions' => $transactionCount,
            ];
        }

        // Category Usage Statistics
        $categoryStats = Category::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->limit(10)
            ->get();

        // Platform Revenue (if you have subscription or premium features)
        $currentMonthRevenue = Transaction::where('type', 'income')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'totalTransactions',
            'totalCategories',
            'totalIncome',
            'totalExpense',
            'recentUsers',
            'topUsers',
            'monthlyGrowth',
            'categoryStats',
            'currentMonthRevenue'
        ));
    }
}
