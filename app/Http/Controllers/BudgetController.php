<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $currentMonth = now()->format('Y-m');
        $user = auth()->user();

        // 1. Ambil SEMUA budget untuk bulan ini (tidak dipaginasi, sudah diurutkan)
        $currentMonthBudgets = $user->budgets()
            ->with('category')
            ->where('month', $currentMonth)
            ->get() // Ambil semua untuk bulan ini
            ->sortByDesc(function ($budget) { // Urutkan di sini
                if ($budget->limit > 0) return ($budget->spent / $budget->limit) * 100;
                return -1;
            });

        // 2. Hitung total ringkasan untuk card spesial (SAMA seperti sebelumnya)
        $totalLimit = $currentMonthBudgets->sum('limit');
        $totalSpent = $currentMonthBudgets->sum('spent'); // Gunakan data yg sudah diambil
        $totalRemaining = $totalLimit - $totalSpent;
        $totalProgress = ($totalLimit > 0) ? ($totalSpent / $totalLimit) * 100 : 0;
        $isOverTotal = $totalSpent > $totalLimit;

        $currentMonthSummary = (object) [
            'limit' => $totalLimit,
            'spent' => $totalSpent,
            'remaining' => $totalRemaining,
            'progress' => $totalProgress,
            'isOverBudget' => $isOverTotal
        ];

        // --- !! PERUBAHAN DI SINI !! ---
        // 3. Ambil data RIWAYAT (paginasi), KECUALI bulan ini
        $budgets = $user->budgets()
            ->with('category')
            ->where('month', '!=', $currentMonth) // <-- Tambahkan kondisi ini
            ->orderBy('month', 'desc')
            ->paginate(12); // Paginasi hanya untuk riwayat
        // --- !! AKHIR PERUBAHAN !! ---


        // 4. Kirim semua data ke view
        return view('budgets.index', compact(
            'budgets',              // Hanya berisi riwayat
            'currentMonthBudgets',  // Berisi semua kategori bulan ini
            'currentMonthSummary'   // Berisi total bulan ini
        ));
    }

    public function create()
    {
        $categories = Category::select('category_id', 'name')
            ->get();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            // Tambahkan validasi category_id
            'category_id' => 'required|exists:categories,category_id',
            'limit' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();

        // Check if budget already exists for this month
        $existingBudget = auth()->user()->budgets()
            ->where('category_id', $validated['category_id'])
            ->where('month', $validated['month'])
            ->first();

        if ($existingBudget) {
            return back()->withErrors(['month' => 'Budget for this month and category already exists.']);
        }

        $budget = auth()->user()->budgets()->updateOrCreate(
            [
                // Cari berdasarkan ini:
                'month' => $validated['month'],
                'category_id' => $validated['category_id']
            ],
            [
                // Update/Create dengan data ini:
                'limit' => $validated['limit'],
                // user_id akan terisi otomatis oleh relasi
            ]
        );
        $budget->updateSpentAmount();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully!');
    }

    public function edit(Budget $budget)
    {
        $categories = Category::select('category_id', 'name')
            ->get();
        return view('budgets.edit', compact('budget', 'categories'));
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
