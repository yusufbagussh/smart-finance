<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user')->withCount('transactions', 'budgets');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereHas('transactions', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                });
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        if ($user->isAdmin()) {
            abort(403, 'Cannot view admin user details.');
        }

        $user->load(['transactions.category', 'budgets']);

        // User statistics
        $totalIncome = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        // Recent activity
        $recentTransactions = $user->transactions()
            ->with('category')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Monthly activity
        $monthlyActivity = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthName = now()->subMonths($i)->format('M Y');

            $income = $user->transactions()
                ->where('type', 'income')
                // ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
                ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month])
                ->sum('amount');

            $expense = $user->transactions()
                ->where('type', 'expense')
                // ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
                ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month])
                ->sum('amount');

            $monthlyActivity[] = [
                'month' => $monthName,
                'income' => $income,
                'expense' => $expense,
            ];
        }

        return view('admin.users.show', compact(
            'user',
            'totalIncome',
            'totalExpense',
            'currentBalance',
            'recentTransactions',
            'monthlyActivity'
        ));
    }

    public function toggleStatus(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot modify admin user.');
        }

        // Implement your status toggle logic here
        // For example, you could add an 'active' field to users table

        return back()->with('success', 'User status updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
