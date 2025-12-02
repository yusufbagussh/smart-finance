<?php

namespace App\Http\Controllers; // Sesuaikan namespace

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // Tampilkan semua akun
    public function index(Request $request)
    {
        $query = Auth::user()->accounts();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $accounts = $query->orderBy('name')->get();
        $totalBalance = $accounts->sum('current_balance');

        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    // Tampilkan form create
    public function create()
    {
        return view('accounts.create');
    }

    // Simpan akun baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50', // Misal: bank_account, e_wallet, cash
            'initial_balance' => 'required|numeric|min:0', // Saldo Awal
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
        ]);

        $user = Auth::user();

        // 1. Buat Akun
        $account = $user->accounts()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'current_balance' => $validated['initial_balance'], // Set saldo awal
            'icon' => $validated['icon'] ?? 'fas fa-piggy-bank',
            'color' => $validated['color'] ?? '#3B82F6',
        ]);

        // 2. (PENTING) Buat Transaksi "Saldo Awal"
        // Ini agar saldo Anda tercatat di riwayat utama
        if ($validated['initial_balance'] > 0) {
            $user->transactions()->create([
                'type' => 'income',
                'amount' => $validated['initial_balance'],
                'description' => 'Saldo Awal - ' . $validated['name'],
                'date' => now(),
                'destination_account_id' => $account->id,
                'category_id' => null, // Atau buat kategori "Saldo Awal"
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'Akun baru berhasil dibuat.');
    }

    // Tampilkan form edit
    public function edit(Account $account)
    {
        // Pastikan user hanya bisa edit akun miliknya
        // $this->authorize('update', $account); // Asumsi Anda punya AccountPolicy
        return view('accounts.edit', compact('account'));
    }

    // Update akun
    public function update(Request $request, Account $account)
    {
        // $this->authorize('update', $account);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            // Kita TIDAK mengizinkan edit saldo dari sini. Saldo HANYA berubah via transaksi.
        ]);

        $account->update($validated);
        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui.');
    }

    // Hapus akun
    public function destroy(Account $account)
    {
        // $this->authorize('delete', $account);

        // TODO: Tambahkan validasi
        // Sebaiknya jangan hapus akun jika masih ada transaksi terkait
        if ($account->transactionsAsSource()->exists() || $account->transactionsAsDestination()->exists()) {
            return redirect()->route('accounts.index')->with('error', 'Tidak bisa menghapus akun yang masih memiliki riwayat transaksi.');
        }

        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus.');
    }
}
