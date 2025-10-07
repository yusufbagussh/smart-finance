<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = auth()->user()->budgets()
            ->orderBy('month', 'desc')
            ->paginate(12);

        // Update spent amounts
        foreach ($budgets as $budget) {
            $budget->updateSpentAmount();
        }

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        return view('budgets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'limit' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();

        // Check if budget already exists for this month
        $existingBudget = auth()->user()->budgets()
            ->where('month', $validated['month'])
            ->first();

        if ($existingBudget) {
            return back()->withErrors(['month' => 'Budget for this month already exists.']);
        }

        $budget = Budget::create($validated);
        $budget->updateSpentAmount();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully!');
    }

    public function edit(Budget $budget)
    {
        // $this->authorize('update', $budget);
        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        // $this->authorize('update', $budget);

        $validated = $request->validate([
            'limit' => 'required|numeric|min:0',
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully!');
    }

    public function destroy(Budget $budget)
    {
        // $this->authorize('delete', $budget);

        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully!');
    }
}
