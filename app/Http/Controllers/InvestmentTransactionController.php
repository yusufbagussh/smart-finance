<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\InvestmentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreInvestmentTransactionRequest; // Akan kita buat
use App\Models\Category;
use App\Models\Portfolio;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class InvestmentTransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}
    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        // Ambil data untuk dropdown di form
        $portfolios = Auth::user()->portfolios;
        $assets = Asset::orderBy('name')->get(); // Ambil semua aset

        // 3. Ambil daftar akun untuk dropdown
        $accounts = Auth::user()->accounts()->orderBy('name')->get();

        // Jika user tidak punya portofolio, redirect
        if ($portfolios->isEmpty()) {
            return redirect()->route('portfolios.create')
                ->with('warning', 'Anda harus membuat portofolio terlebih dahulu.');
        }

        // Jika tidak ada aset di sistem, redirect
        if ($assets->isEmpty()) {
            return redirect()->route('assets.create')
                ->with('warning', 'Anda harus menambahkan data aset terlebih dahulu.');
        }

        return view('investment-transactions.create', compact('portfolios', 'assets', 'accounts'));
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(StoreInvestmentTransactionRequest $request)
    {
        $validatedData = $request->validated();
        $portfolio = Portfolio::find($validatedData['portfolio_id']);
        $asset = Asset::find($validatedData['asset_id']); // Ambil data aset

        // ... (Logika hitung total_amount dan fees) ...
        $total_amount = $validatedData['quantity'] * $validatedData['price_per_unit'];
        $fees = $validatedData['fees'] ?? 0;

        if ($validatedData['transaction_type'] === 'buy') {
            $total_amount += $fees;
        } else {
            $total_amount -= $fees;
        }

        DB::transaction(function () use ($portfolio, $validatedData, $asset, $total_amount, $fees) {
            // 3. SIMPAN TRANSAKSI INVESTASI (Ini kode yang sudah ada)
            $investmentTransaction = $portfolio->investmentTransactions()->create([
                'asset_id' => $validatedData['asset_id'],
                'transaction_type' => $validatedData['transaction_type'],
                'transaction_date' => $validatedData['transaction_date'],
                'quantity' => $validatedData['quantity'],
                'price_per_unit' => $validatedData['price_per_unit'],
                'fees' => $fees,
                'total_amount' => $total_amount,
            ]);

            // Kita kirim juga ID akun yang dipilih user
            $accountId = ($validatedData['transaction_type'] === 'buy')
                ? $validatedData['source_account_id']
                : $validatedData['destination_account_id'];

            // 4. LOGIKA BARU: BUAT TRANSAKSI DI RIWAYAT UTAMA
            $this->createMainTransaction($investmentTransaction, $asset, $validatedData['transaction_type'], $total_amount, $accountId);
        });

        $this->clearPortfolioCache($portfolio->id);

        // 5. Redirect (Kode sudah ada)
        return redirect()->route('portfolios.show', $portfolio->id)
            ->with('success', 'Transaksi investasi berhasil dicatat (dan ditambahkan ke riwayat utama)!');
    }

    /**
     * Helper function untuk membuat entri di tabel transactions (utama).
     */
    /**
     * Helper: Buat transaksi utama dan update saldo.
     */
    private function createMainTransaction($investmentTx, $asset, $type, $totalAmount, $accountId)
    {
        $user = Auth::user();
        $description = "";
        $transactionType = "";
        $sourceAccountId = null;
        $destinationAccountId = null;

        // Tentukan Tipe & Akun
        if ($type === 'buy') {
            $description = "Asset Purchase: " . $asset->name;
            $transactionType = "expense"; // Uang keluar
            $sourceAccountId = $accountId; // Kurangi saldo ini
            $icon = "📈";
            $categoryColor = "#2563EB";
            $categoryName = "Investments";
        } else { // 'sell'
            $description = "Asset Sale Proceeds: " . $asset->name;
            $transactionType = "income"; // Uang masuk
            $destinationAccountId = $accountId; // Tambah saldo ini
            $icon = "💸";
            $categoryColor = "#10B981";
            $categoryName = "Investment Income";
        }

        // Kategori Otomatis (Investments)
        $category = Category::firstOrCreate(
            [
                'name' => $categoryName,
                'type' => $transactionType,
                'icon' => $icon,
                'color' => $categoryColor
            ]
        );

        // 1. Buat Transaksi Utama
        $mainTransaction = \App\Models\Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->category_id,
            'amount' => $totalAmount,
            'type' => $transactionType,
            'description' => $description,
            'date' => $investmentTx->transaction_date,
            'investment_transaction_id' => $investmentTx->id,
            'source_account_id' => $sourceAccountId,
            'destination_account_id' => $destinationAccountId,
        ]);

        // 2. PANGGIL SERVICE UNTUK UPDATE SALDO AKUN (DOMPET/BANK)
        // Ini akan mengurangi saldo (jika buy) atau menambah saldo (jika sell)
        $this->transactionService->handleAccountBalance($mainTransaction);
    }
    public function destroy(InvestmentTransaction $investmentTransaction)
    {
        // 1. Otorisasi
        $this->authorizeUser($investmentTransaction);

        // Ambil transaksi utama (cash flow) yang terkait
        $mainTransaction = $investmentTransaction->mainTransaction;

        try {
            // 2. Gunakan DB Transaction untuk keamanan data
            DB::beginTransaction();

            if ($mainTransaction) {
                // 3. REVERT Saldo Akun (PENTING!)
                // Mengembalikan saldo akun ke kondisi sebelum transaksi ini terjadi.
                // Misal: Jika ini transaksi 'Beli', uang dikembalikan ke Akun Sumber.
                $this->transactionService->handleAccountBalance($mainTransaction, true);

                // 4. Hapus transaksi utama (cash flow) yang terkait
                $mainTransaction->delete();
            }

            // 5. Hapus transaksi investasi itu sendiri
            $investmentTransaction->delete();

            // 6. Bersihkan cache
            $this->clearPortfolioCache($investmentTransaction->portfolio_id);

            DB::commit();

            // 7. Kembali ke halaman portofolio
            return redirect()->route('portfolios.show', $investmentTransaction->portfolio_id)
                ->with('success', 'Transaksi investasi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi investasi.');
        }
    }
    // Helper untuk otorisasi (DRY - Don't Repeat Yourself)
    private function authorizeUser(InvestmentTransaction $investmentTransaction)
    {
        if ($investmentTransaction->portfolio->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Menampilkan form untuk mengedit transaksi.
     */
    public function edit(InvestmentTransaction $investmentTransaction)
    {
        // 1. Otorisasi
        $this->authorizeUser($investmentTransaction);

        // 2. Ambil data untuk dropdown
        $portfolios = Auth::user()->portfolios;
        $assets = Asset::orderBy('name')->get();
        $accounts = Auth::user()->accounts()->orderBy('name')->get();

        // Jika user tidak punya portofolio, redirect
        if ($portfolios->isEmpty()) {
            return redirect()->route('portfolios.create')
                ->with('warning', 'Anda harus membuat portofolio terlebih dahulu.');
        }

        // Jika tidak ada aset di sistem, redirect
        if ($assets->isEmpty()) {
            return redirect()->route('assets.create')
                ->with('warning', 'Anda harus menambahkan data aset terlebih dahulu.');
        }

        // 3. Tampilkan view
        return view('investment-transactions.edit', compact('investmentTransaction', 'portfolios', 'assets', 'accounts'));
    }

    /**
     * Menyimpan perubahan (update) transaksi.
     */
    public function update(StoreInvestmentTransactionRequest $request, InvestmentTransaction $investmentTransaction)
    {
        // 1. Otorisasi
        $this->authorizeUser($investmentTransaction);

        // 2. Validasi sudah ditangani oleh StoreInvestmentTransactionRequest
        $validatedData = $request->validated();

        // 3. Ambil data aset (untuk deskripsi)
        $asset = Asset::find($validatedData['asset_id']);

        // 4. Hitung ulang total amount (copy dari store)
        $total_amount = $validatedData['quantity'] * $validatedData['price_per_unit'];
        $fees = $validatedData['fees'] ?? 0;

        if ($validatedData['transaction_type'] === 'buy') {
            $total_amount += $fees;
        } else {
            $total_amount -= $fees;
        }

        // 5. Gunakan DB Transaction
        DB::transaction(function () use ($investmentTransaction, $validatedData, $asset, $total_amount, $fees) {
            $mainTransaction = $investmentTransaction->mainTransaction; // Ambil transaksi utama

            // 1. REVERT Saldo LAMA (Berdasarkan kondisi transaksi utama yang lama)
            if ($mainTransaction) {
                // Kita revert saldo akun (misal: uang 5jt dikembalikan ke akun BCA)
                $this->transactionService->handleAccountBalance($mainTransaction, true);
            }

            // 2. Hitung ulang total amount (karena harga/qty/fee bisa berubah)
            // $newTotalAmount = $validatedData['quantity'] * $validatedData['price_per_unit'] + ($validatedData['fees'] ?? 0);

            // 3. Update Investment Transaction
            $investmentTransaction->update([
                'portfolio_id' => $validatedData['portfolio_id'],
                'asset_id' => $validatedData['asset_id'],
                'transaction_type' => $validatedData['transaction_type'],
                'transaction_date' => $validatedData['transaction_date'],
                'quantity' => $validatedData['quantity'],
                'price_per_unit' => $validatedData['price_per_unit'],
                'fees' => $fees,
                'total_amount' => $total_amount,
            ]);

            // 4. Update Transaksi Utama (Cash Flow)
            $this->updateMainTransaction(
                $investmentTransaction->fresh(), // Gunakan fresh untuk memastikan data inv_tx terbaru
                $asset,
                $validatedData['transaction_type'],
                $total_amount,
                $validatedData['source_account_id'] ?? null,
                $validatedData['destination_account_id'] ?? null
            );

            // 5. APPLY Saldo BARU (Berdasarkan transaksi utama yang baru)
            $this->transactionService->handleAccountBalance($mainTransaction->fresh()); // Apply Akun
        });

        $this->clearPortfolioCache($investmentTransaction->portfolio_id);

        // 8. Redirect
        return redirect()->route('portfolios.show', $investmentTransaction->portfolio_id)
            ->with('success', 'Transaksi investasi berhasil diperbarui.');
    }

    /**
     * Helper function untuk MENGUBAH entri di tabel transactions (utama).
     */
    private function updateMainTransaction($investmentTx, $asset, $type, $totalAmount, $sourceAccountId = null, $destinationAccountId = null)
    {
        $user = Auth::user();
        $description = "";
        $categoryName = "";
        $transactionType = "";

        // Tentukan Kategori & Tipe

        // Tentukan Tipe & Akun
        if ($type === 'buy') {
            $description = "Asset Purchase: " . $asset->name;
            $transactionType = "expense"; // Uang keluar
            $icon = "📈";
            $categoryColor = "#2563EB";
            $categoryName = "Investments";
        } else { // 'sell'
            $description = "Asset Sale Proceeds: " . $asset->name;
            $transactionType = "income"; // Uang masuk
            $icon = "💸";
            $categoryColor = "#10B981";
            $categoryName = "Investment Income";
        }

        // Kategori Otomatis (Investments)
        $category = Category::firstOrCreate(
            [
                'name' => $categoryName,
                'type' => $transactionType,
                'icon' => $icon,
                'color' => $categoryColor
            ]
        );

        // 2. Cari Transaksi Utama yang terkait (harus ada)
        $mainTransaction = $investmentTx->mainTransaction;

        // 3. Update Transaksi Utama (dengan Account ID BARU)
        if ($mainTransaction) {
            $mainTransaction->update([
                'category_id' => $category->category_id,
                'amount' => $totalAmount,
                'type' => $transactionType,
                'description' => $description,
                'date' => $investmentTx->transaction_date,
                'source_account_id' => $sourceAccountId,      // <-- DARI FORM
                'destination_account_id' => $destinationAccountId, // <-- DARI FORM
            ]);
        } else {
            // Jika (karena alasan aneh) transaksi utama tidak ada, buatkan saja
            $this->createMainTransaction($investmentTx, $asset, $type, $totalAmount, $sourceAccountId ?? $destinationAccountId);
        }
    }

    // Buat helper function agar bersih
    private function clearPortfolioCache($portfolioId)
    {
        if (Auth::check()) {
            $cacheKey = "portfolio_analysis_user_" . Auth::id() . "_portfolio_" . $portfolioId;
            Cache::forget($cacheKey);
        }
    }



    // /**
    //  * Menampilkan form untuk membuat transaksi baru.
    //  */
    // public function create()
    // {
    //     // Ambil data untuk dropdown di form
    //     $portfolios = Auth::user()->portfolios;
    //     $assets = Asset::all(); // Nanti bisa di-cache

    //     return view('transactions.create', compact('portfolios', 'assets'));
    // }

    // /**
    //  * Menyimpan transaksi baru ke database.
    //  */
    // public function store(Request $request) // Ganti 'Request' dengan 'StoreInvestmentTransactionRequest' nanti
    // {
    //     // Validasi (Contoh sederhana, sebaiknya pakai Form Request)
    //     $validatedData = $request->validate([
    //         'portfolio_id' => 'required|integer',
    //         'asset_id' => 'required|integer',
    //         'transaction_type' => 'required|in:buy,sell',
    //         'transaction_date' => 'required|date',
    //         'quantity' => 'required|numeric|min:0.00000001',
    //         'price_per_unit' => 'required|numeric|min:0.01',
    //         'fees' => 'nullable|numeric|min:0',
    //     ]);

    //     // Keamanan: Pastikan portfolio_id yang di-submit adalah milik user
    //     $portfolio = Auth::user()->portfolios()->findOrFail($validatedData['portfolio_id']);

    //     // [LOGIKA PENTING]
    //     // Jika 'sell', Anda HARUS memvalidasi apakah unitnya mencukupi.
    //     // Anda bisa memanggil service (misal: $this->portfolioService->getAssetQuantity(...))
    //     // Untuk saat ini, kita lewati validasi stok.

    //     // Hitung total amount
    //     $total_amount = $validatedData['quantity'] * $validatedData['price_per_unit'];

    //     // Logika biaya: biaya menambah 'total_amount' saat Beli,
    //     // dan mengurangi 'total_amount' (hasil penjualan) saat Jual.
    //     $fees = $validatedData['fees'] ?? 0;
    //     if ($validatedData['transaction_type'] === 'buy') {
    //         $total_amount += $fees;
    //     } else {
    //         $total_amount -= $fees;
    //     }

    //     // Simpan transaksi
    //     $portfolio->investmentTransactions()->create([
    //         'asset_id' => $validatedData['asset_id'],
    //         'transaction_type' => $validatedData['transaction_type'],
    //         'transaction_date' => $validatedData['transaction_date'],
    //         'quantity' => $validatedData['quantity'],
    //         'price_per_unit' => $validatedData['price_per_unit'],
    //         'fees' => $fees,
    //         'total_amount' => $total_amount,
    //     ]);

    //     // Redirect kembali ke halaman portofolio
    //     return redirect()->route('portfolios.show', $portfolio->id)
    //         ->with('success', 'Transaksi berhasil disimpan!');
    // }
}
