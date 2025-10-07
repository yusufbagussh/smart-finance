<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->transactions()->with('category');

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'LIKE', '%' . $request->search . '%');
        }

        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = Category::all();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'required|string|max:500',
        ]);

        $validated['user_id'] = auth()->id();

        Transaction::create($validated);

        // Update budget if it's an expense
        if ($validated['type'] === 'expense') {
            $this->updateBudget($validated['date']);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction added successfully!');
    }

    public function edit(Transaction $transaction)
    {
        // $this->authorize('update', $transaction);
        $categories = Category::all();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        // $this->authorize('update', $transaction);
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'required|string|max:500',
        ]);

        $oldDate = $transaction->date;
        $transaction->update($validated);

        // Update budgets for both old and new dates if expense
        if ($validated['type'] === 'expense') {
            $this->updateBudget($oldDate);
            $this->updateBudget($validated['date']);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Transaction $transaction)
    {
        // $this->authorize('delete', $transaction);
        $date = $transaction->date;
        $type = $transaction->type;

        $transaction->delete();

        // Update budget if it was an expense
        if ($type === 'expense') {
            $this->updateBudget($date);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully!');
    }

    private function updateBudget($date)
    {
        $month = Carbon::parse($date)->format('Y-m');
        $budget = auth()->user()->budgets()->where('month', $month)->first();

        if ($budget) {
            $budget->updateSpentAmount();
        }
    }
}
