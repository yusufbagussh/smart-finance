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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InvestmentTransactionController extends Controller
{
    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        // Ambil data untuk dropdown di form
        $portfolios = Auth::user()->portfolios;
        $assets = Asset::orderBy('name')->get(); // Ambil semua aset

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

        return view('investment-transactions.create', compact('portfolios', 'assets'));
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

            // 4. LOGIKA BARU: BUAT TRANSAKSI DI RIWAYAT UTAMA
            $this->createMainTransaction($investmentTransaction, $asset, $validatedData['transaction_type'], $total_amount);
        });

        $this->clearPortfolioCache($portfolio->id);

        // 5. Redirect (Kode sudah ada)
        return redirect()->route('portfolios.show', $portfolio->id)
            ->with('success', 'Transaksi investasi berhasil dicatat (dan ditambahkan ke riwayat utama)!');
    }

    /**
     * Helper function untuk membuat entri di tabel transactions (utama).
     */
    private function createMainTransaction($investmentTx, $asset, $type, $totalAmount)
    {
        $user = Auth::user();
        $description = "";
        $categoryName = "";
        $transactionType = "";

        if ($type === 'buy') {
            $description = "Beli Aset: " . $asset->name;
            $categoryName = "Investments"; // Nama Kategori Expense
            $transactionType = "expense";
        } else { // 'sell'
            $description = "Jual Aset: " . $asset->name;
            $categoryName = "Investment Incomes"; // Nama Kategori Income
            $transactionType = "income";
        }

        // Cari Kategori, atau buat jika tidak ada (untuk keamanan)
        $category = Category::firstOrCreate(
            ['name' => $categoryName, 'type' => $transactionType],
            ['icon' => "📉", 'color' => '#3B82F6'] // Default jika dibuat
        );

        // Buat Transaksi Utama
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->category_id,
            'amount' => $totalAmount,
            'type' => $transactionType,
            'description' => $description,
            'date' => $investmentTx->transaction_date,
            'investment_transaction_id' => $investmentTx->id
        ]);
    }

    public function destroy(InvestmentTransaction $investmentTransaction)
    {
        // 1. Otorisasi: Pastikan transaksi ini milik user
        $this->authorizeUser($investmentTransaction);

        // 2. Gunakan DB Transaction untuk keamanan data
        DB::transaction(function () use ($investmentTransaction) {

            // 3. Hapus transaksi utama (cash flow) yang terkait
            // Gunakan where() lalu delete() untuk keamanan
            Transaction::where('investment_transaction_id', $investmentTransaction->id)->delete();

            // 4. Hapus transaksi investasi itu sendiri
            $investmentTransaction->delete();
        });

        $this->clearPortfolioCache($investmentTransaction->portfolio_id);

        // 5. Kembali ke halaman portofolio
        return redirect()->route('portfolios.show', $investmentTransaction->portfolio_id)
            ->with('success', 'Transaksi investasi berhasil dihapus.');
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

        // 3. Tampilkan view
        return view('investment-transactions.edit', compact('investmentTransaction', 'portfolios', 'assets'));
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

            // 6. Update Transaksi Investasi
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

            // 7. Update Transaksi Utama (Cash Flow)
            $this->updateMainTransaction($investmentTransaction, $asset, $validatedData['transaction_type'], $total_amount);
        });

        $this->clearPortfolioCache($investmentTransaction->portfolio_id);

        // 8. Redirect
        return redirect()->route('portfolios.show', $investmentTransaction->portfolio_id)
            ->with('success', 'Transaksi investasi berhasil diperbarui.');
    }

    /**
     * Helper function untuk MENGUBAH entri di tabel transactions (utama).
     */
    private function updateMainTransaction($investmentTx, $asset, $type, $totalAmount)
    {
        // 1. Logika deskripsi & kategori (copy dari createMainTransaction)
        $user = Auth::user();
        $description = "";
        $categoryName = "";
        $transactionType = "";

        if ($type === 'buy') {
            $description = "Beli Aset: " . $asset->name;
            $categoryName = "Investments";
            $transactionType = "expense";
        } else { // 'sell'
            $description = "Jual Aset: " . $asset->name;
            $categoryName = "Investment Incomes";
            $transactionType = "income";
        }

        $category = Category::firstOrCreate(
            ['name' => $categoryName, 'type' => $transactionType],
            ['icon' => "📉", 'color' => '#3B82F6']
        );

        // 2. Cari Transaksi Utama yang terkait
        $mainTransaction = Transaction::where('investment_transaction_id', $investmentTx->id)->first();

        // 3. Update Transaksi Utama
        if ($mainTransaction) {
            $mainTransaction->update([
                'category_id' => $category->category_id,
                'amount' => $totalAmount,
                'type' => $transactionType,
                'description' => $description,
                'date' => $investmentTx->transaction_date,
            ]);
        } else {
            // Jika (karena alasan aneh) transaksi utama tidak ada, buatkan saja
            $this->createMainTransaction($investmentTx, $asset, $type, $totalAmount);
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
