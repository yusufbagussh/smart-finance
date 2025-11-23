<?php

namespace App\Http\Controllers;

use App\Models\Liability;
use App\Models\Account; // Untuk dropdown
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LiabilityController extends Controller
{

    public function __construct(private TransactionService $transactionService) {}

    // Menampilkan daftar hutang yang aktif
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->liabilities();

        // 1. Logika Pencarian (Case-Insensitive pada Nama & Kreditur)
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $query->where(function ($q) use ($searchTerm) {
                // Cari di kolom 'name' ATAU di kolom 'creditor_name'
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(creditor_name) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        // 2. Filter Status (Active vs. Closed/Paid Off)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('current_balance', '>', 0);
            } elseif ($request->status === 'closed') {
                $query->where('current_balance', '=', 0);
            }
        }

        // 3. Filter Creditor
        if ($request->filled('creditor')) {
            $query->where('creditor_name', $request->creditor);
        }

        // Apply Ordering and Pagination
        $liabilities = $query->orderBy('current_balance', 'desc')->paginate(15);
        $totalReceivables = $liabilities->where('type', 'receivable')->sum('current_balance');
        $totalLiabilities = $liabilities->where('type', 'payable')->sum('current_balance');

        // Ambil daftar unik creditor untuk dropdown
        $creditors = $user->liabilities()->distinct()->pluck('creditor_name');

        return view('liabilities.index', compact('liabilities', 'totalLiabilities', 'totalReceivables', 'creditors'));
    }

    // Menampilkan form tambah hutang
    public function create()
    {
        $accounts = Auth::user()->accounts()->orderBy('name')->get();
        if ($accounts->isEmpty()) {
            return redirect()->route('accounts.create')->with('error', 'Buat akun bank/dompet dulu untuk menerima dana pinjaman.');
        }

        return view('liabilities.create', compact('accounts'));
    }

    // Menyimpan hutang baru (dan membuat transaksi INFLOW)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'creditor_name' => 'required|string|max:255',
            'original_amount' => 'required|numeric|min:0.01',
            'tenor_months' => 'nullable|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|before_or_equal:today',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())], // Akun penerima dana
            'type' => 'required|in:payable,receivable',
        ]);

        $user = Auth::user();
        try {
            DB::beginTransaction();

            // 1. Buat Entri Liabilitas
            $liability = $user->liabilities()->create([
                'name' => $validated['name'],
                'creditor_name' => $validated['creditor_name'],
                'original_amount' => $validated['original_amount'],
                'current_balance' => $validated['original_amount'], // Saldo awal = jumlah pinjaman awal
                'start_date' => $validated['start_date'],
                'due_date' => $validated['due_date'] ?? null,
                'tenor_months' => $validated['tenor_months'],
                'interest_rate' => $validated['interest_rate'] ?? 0,
                'type' => $validated['type'],
            ]);

            // 2. Buat Transaksi Awal (Logika Bercabang)
            if ($validated['type'] === 'payable') {
                // KITA BERHUTANG -> Uang Masuk (Income)
                $transaction = $user->transactions()->create([
                    'type' => 'income',
                    'amount' => $validated['original_amount'],
                    'description' => 'Terima Pinjaman: ' . $validated['name'],
                    'date' => $validated['start_date'],
                    'destination_account_id' => $validated['account_id'], // Masuk ke akun kita
                    'liability_id' => $liability->id,
                ]);
            } else {
                // KITA KASIH HUTANG -> Uang Keluar (Expense)
                $transaction = $user->transactions()->create([
                    'type' => 'expense',
                    'amount' => $validated['original_amount'],
                    'description' => 'Beri Pinjaman: ' . $validated['name'],
                    'date' => $validated['start_date'],
                    'source_account_id' => $validated['account_id'], // Keluar dari akun kita
                    'liability_id' => $liability->id,
                ]);
            }

            // Note: Saldo Account akan otomatis di-update oleh TransactionController logic (adjustAccountBalances)
            if ($transaction) {
                $this->transactionService->handleAccountBalance($transaction);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal mencatat hutang: " . $e->getMessage());
            return back()->with('error', 'Gagal mencatat hutang dan inflow dana.')->withInput();
        }

        return redirect()->route('liabilities.index')->with('success', 'Hutang baru berhasil dicatat!');
    }

    // ... (edit, update, destroy methods akan ditambahkan sesuai kebutuhan, tapi logika update saldo sangat kompleks)

    // Untuk saat ini, kita akan melewati method edit/update/destroy karena terlalu kompleks.
    // Hutang hanya dilunasi dengan mencatat transaksi pembayaran cicilan baru (di TransactionController).

    /**
     * Menampilkan form untuk mengedit data hutang.
     */
    public function edit(Liability $liability)
    {
        // Otorisasi (Pastikan milik user)
        if ($liability->user_id !== Auth::id()) {
            abort(403);
        }

        return view('liabilities.edit', compact('liability'));
    }

    /**
     * Memperbarui data hutang.
     * Catatan: Kita tidak mengizinkan edit jumlah uang di sini demi keamanan akuntansi.
     */
    public function update(Request $request, Liability $liability)
    {
        if ($liability->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'creditor_name' => 'required|string|max:255',
            'tenor_months' => 'nullable|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Kita update data administratif saja
        $liability->update([
            'name' => $validated['name'],
            'creditor_name' => $validated['creditor_name'],
            'tenor_months' => $validated['tenor_months'],
            'interest_rate' => $validated['interest_rate'] ?? 0,
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
        ]);

        return redirect()->route('liabilities.index')
            ->with('success', 'Data hutang berhasil diperbarui.');
    }

    /**
     * Menghapus hutang dan SEMUA riwayat transaksinya.
     * Ini akan mengembalikan saldo akun ke kondisi seolah hutang tidak pernah terjadi.
     */
    public function destroy(Liability $liability)
    {
        if ($liability->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // 1. Ambil semua transaksi yang terkait dengan hutang ini
            // (Baik itu penerimaan dana awal maupun pembayaran cicilan)
            $transactions = $liability->transactions;

            foreach ($transactions as $transaction) {
                // Kita lakukan REVERT (Pembalikan) saldo untuk setiap transaksi
                // $this->revertTransactionBalance($transaction);
                $this->transactionService->handleAccountBalance($transaction, true);

                // Hapus transaksinya
                $transaction->delete();
            }

            // 2. Hapus data hutang itu sendiri
            $liability->delete();

            DB::commit();

            return redirect()->route('liabilities.index')
                ->with('success', 'Hutang dan seluruh riwayat transaksinya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus hutang: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus hutang.');
        }
    }

    /**
     * Helper Private: Mengembalikan saldo akun sebelum transaksi dihapus.
     */
    private function revertTransactionBalance($transaction)
    {
        $amount = $transaction->amount;

        // Skenario A: Ini adalah transaksi PENERIMAAN Hutang (Type: Income)
        // Uang masuk ke akun -> Saat dihapus, uang harus KELUAR dari akun.
        if ($transaction->type === 'income' && $transaction->destination_account_id) {
            Account::where('id', $transaction->destination_account_id)
                ->decrement('current_balance', $amount);
        }

        // Skenario B: Ini adalah transaksi PEMBAYARAN Cicilan (Type: Expense)
        // Uang keluar dari akun -> Saat dihapus, uang harus KEMBALI ke akun.
        elseif ($transaction->type === 'expense' && $transaction->source_account_id) {
            Account::where('id', $transaction->source_account_id)
                ->increment('current_balance', $amount);
        }
    }
}
