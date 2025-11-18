<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi dengan filter.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->transactions()
            ->with(['category', 'sourceAccount', 'destinationAccount']); // Load relasi akun juga

        // --- Filters (Dari Kode Lama Anda) ---
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

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Category::all();
        $accounts = $user->accounts()->orderBy('name')->get();

        if ($accounts->isEmpty()) {
            return redirect()->route('accounts.create')
                ->with('error', 'Anda harus membuat Akun (misal: "Dompet Tunai") terlebih dahulu sebelum mencatat transaksi.');
        }

        return view('transactions.create', compact('categories', 'accounts'));
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'expense');

        // Validasi Dinamis
        $rules = [
            'type' => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:255',
        ];

        if ($type === 'income') {
            $rules['destination_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id)];
            $rules['category_id'] = ['required', Rule::exists('categories', 'category_id')->where('user_id', $user->id)];
        } elseif ($type === 'expense') {
            $rules['source_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id)];
            $rules['category_id'] = ['required', Rule::exists('categories', 'category_id')->where('user_id', $user->id)];
        } elseif ($type === 'transfer') {
            $rules['source_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id)];
            $rules['destination_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id), 'different:source_account_id'];
            $rules['category_id'] = 'nullable';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $transaction = $user->transactions()->create([
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'] ?? null,
                'source_account_id' => $validated['source_account_id'] ?? null,
                'destination_account_id' => $validated['destination_account_id'] ?? null,
            ]);

            // 1. Update Saldo Akun
            $this->adjustAccountBalances($transaction);

            // 2. Update Budget (Jika Expense)
            if ($transaction->type === 'expense' && $transaction->category_id) {
                $this->updateBudget($transaction->date, $transaction->category_id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan transaksi: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan transaksi. Silakan coba lagi.')->withInput();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction added successfully!');
    }

    /**
     * Menampilkan form untuk mengedit transaksi.
     */
    public function edit(Transaction $transaction)
    {
        // $this->authorize('update', $transaction);
        $user = Auth::user();

        $categories = Category::all();
        $accounts = $user->accounts()->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories', 'accounts'));
    }

    /**
     * Memperbarui transaksi yang ada.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // $this->authorize('update', $transaction);
        $user = Auth::user();
        $type = $request->input('type', 'expense');

        // Validasi (sama seperti store)
        $rules = [
            'type' => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:255',
        ];
        if ($type === 'income') {
            $rules['destination_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id)];
            $rules['category_id'] = ['required', Rule::exists('categories', 'category_id')->where('user_id', $user->id)];
        } elseif ($type === 'expense') {
            $rules['source_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id)];
            $rules['category_id'] = ['required', Rule::exists('categories', 'category_id')->where('user_id', $user->id)];
        } elseif ($type === 'transfer') {
            $rules['source_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id)];
            $rules['destination_account_id'] = ['required', Rule::exists('accounts', 'id')->where('user_id', $user->id), 'different:source_account_id'];
            $rules['category_id'] = 'nullable';
        }
        $validated = $request->validate($rules);

        // Simpan data lama untuk update budget nanti
        $oldDate = $transaction->date;
        $oldCategoryId = $transaction->category_id;
        $oldType = $transaction->type;

        try {
            DB::beginTransaction();

            // 1. Revert Saldo Lama
            $this->adjustAccountBalances($transaction, true); // true = revert

            // 2. Update Transaksi
            $transaction->update([
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'] ?? null,
                'source_account_id' => $validated['source_account_id'] ?? null,
                'destination_account_id' => $validated['destination_account_id'] ?? null,
            ]);

            // 3. Apply Saldo Baru
            $this->adjustAccountBalances($transaction->fresh());

            // 4. Update Budget (Old & New)
            // Jika dulu expense, update budget lama
            if ($oldType === 'expense' && $oldCategoryId) {
                $this->updateBudget($oldDate, $oldCategoryId);
            }
            // Jika sekarang expense, update budget baru
            if ($transaction->type === 'expense' && $transaction->category_id) {
                $this->updateBudget($transaction->date, $transaction->category_id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate transaksi: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengupdate transaksi. Silakan coba lagi.')->withInput();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully!');
    }

    /**
     * Menghapus transaksi.
     */
    public function destroy(Transaction $transaction)
    {
        // $this->authorize('delete', $transaction);

        $oldDate = $transaction->date;
        $oldCategoryId = $transaction->category_id;
        $oldType = $transaction->type;

        try {
            DB::beginTransaction();

            // 1. Revert Saldo
            $this->adjustAccountBalances($transaction, true); // true = revert

            // 2. Hapus
            $transaction->delete();

            // 3. Update Budget (Jika Expense)
            if ($oldType === 'expense' && $oldCategoryId) {
                $this->updateBudget($oldDate, $oldCategoryId);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus transaksi: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus transaksi.');
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully!');
    }


    /**
     * Helper: Sesuaikan saldo akun.
     */
    private function adjustAccountBalances(Transaction $transaction, $revert = false)
    {
        $amount = $transaction->amount;
        $multiplier = $revert ? -1 : 1;

        if ($transaction->type === 'expense') {
            if ($transaction->source_account_id) {
                Account::where('id', $transaction->source_account_id)
                    ->decrement('current_balance', $amount * $multiplier);
            }
        } elseif ($transaction->type === 'income') {
            if ($transaction->destination_account_id) {
                Account::where('id', $transaction->destination_account_id)
                    ->increment('current_balance', $amount * $multiplier);
            }
        } elseif ($transaction->type === 'transfer') {
            if ($transaction->source_account_id) {
                Account::where('id', $transaction->source_account_id)
                    ->decrement('current_balance', $amount * $multiplier);
            }
            if ($transaction->destination_account_id) {
                Account::where('id', $transaction->destination_account_id)
                    ->increment('current_balance', $amount * $multiplier);
            }
        }
    }

    /**
     * Helper: Update realisasi budget.
     */
    private function updateBudget($date, $categoryId)
    {
        if (!$categoryId) return;

        $month = Carbon::parse($date)->format('Y-m');
        // Asumsi Anda punya relasi 'budgets' di User
        $budget = Auth::user()->budgets()
            ->where('category_id', $categoryId)
            ->where('month', $month)
            ->first();

        if ($budget) {
            // Asumsi model Budget Anda punya method ini
            $budget->updateSpentAmount();
        }
    }
}
