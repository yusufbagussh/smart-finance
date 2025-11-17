<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http; // <-- 1. Import HTTP Client
use Illuminate\Support\Facades\Log;  // <-- 2. Import Log
use App\Models\Asset;                // <-- 3. Import Model Asset
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateAssetPrices extends Command
{
    /**
     * Nama dan signature dari console command.
     * Ini adalah nama yang Anda panggil (misal: php artisan app:update-asset-prices)
     */
    protected $signature = 'app:update-asset-prices';

    /**
     * Deskripsi dari console command.
     */
    protected $description = 'Panggil API Python untuk scraping dan perbarui harga aset (Emas, dll.)';

    protected $pythonApiBaseUrl;

    public function __construct()
    {
        parent::__construct();
        $this->pythonApiBaseUrl = env('ML_BASE_URL', 'http://');
    }

    /**
     * Jalankan logika command.
     */
    public function handle()
    {
        $this->info('Memulai update harga aset...');
        Log::info('Cron Job: Memulai update harga aset...');

        // Menjalankan setiap tugas secara berurutan
        $this->updateGoldPrices();
        $this->updateYFinanceAssets();

        $this->info('Update harga aset selesai.');
        return 0;
    }

    /**
     * Mengambil dan memperbarui harga emas.
     */
    private function updateGoldPrices()
    {
        $this->line('Mengupdate harga Emas (via BeautifulSoup)...');
        $apiUrl = $this->pythonApiBaseUrl . '/scrape-gold-price';

        try {
            $response = Http::timeout(30)->get($apiUrl);

            // Cek jika panggilan sukses DAN ada data 'gold_price'
            if ($response->successful() && $response->json('gold_price')) {

                // --- INI ADALAH LOGIKA YANG DIPERBARUI ---

                $price = $response->json('gold_price');
                $updateString = $response->json('last_updated_at'); // Ambil string "17 Nov 2025, 19.34 WIB"

                $updateTimestamp = null;

                if ($updateString) {
                    try {
                        // 2. Gunakan Carbon untuk mem-parsing string tanggal
                        // 'id_ID' -> agar mengerti "Nov" (November)
                        // 'Asia/Jakarta' -> agar mengerti "WIB"
                        $updateTimestamp = Carbon::parseLocale('id_ID', $updateString, 'Asia/Jakarta')
                            ->tz('UTC'); // 3. Konversi ke UTC untuk disimpan di DB

                    } catch (\Exception $parseError) {
                        Log::warning("Cron Job: Gagal mem-parsing tanggal Emas: " . $updateString . ". Error: " . $parseError->getMessage());
                        $updateTimestamp = now(); // Fallback jika parsing gagal
                    }
                } else {
                    Log::warning("Cron Job: API Emas tidak mengembalikan 'last_updated_at'.");
                    $updateTimestamp = now(); // Fallback jika API tidak mengembalikan tanggal
                }

                // 4. Update database dengan harga DAN timestamp
                Asset::where('asset_type', 'gold')
                    ->update([
                        'current_price' => $price,
                        'price_last_updated_at' => $updateTimestamp // <-- Simpan tanggal
                    ]);

                $this->info(" -> Sukses: Harga Emas diperbarui ke Rp " . number_format($price) . " (per tgl: " . $updateString . ")");
                Log::info("Cron Job: Sukses update harga Emas ke Rp {$price} (per tgl: {$updateString}).");

                // --- AKHIR LOGIKA YANG DIPERBARUI ---

            } else {
                $this->error(' -> Gagal: API Python Emas mengembalikan data tidak valid.');
                Log::error('Cron Job: Gagal update harga Emas.', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error(' -> Gagal: Tidak dapat terhubung ke API Python Emas.');
            Log::error('Cron Job: Gagal koneksi ke API Python Emas.', ['message' => $e->getMessage()]);
        }
    }


    /**
     * !! FUNGSI GABUNGAN BARU !!
     * Mengambil dan memperbarui semua harga aset berbasis yfinance
     * (Saham, ETF, Reksadana).
     */
    private function updateYFinanceAssets()
    {
        $this->line('Mengupdate harga Saham, ETF, & Reksadana (via yfinance)...');

        // 1. Ambil SEMUA kode yang relevan (stock, etf, mutual_fund)
        $assetCodes = Asset::whereIn('asset_type', ['stock', 'etf', 'mutual_fund'])
            ->pluck('code')
            ->toArray();

        if (empty($assetCodes)) {
            $this->info(' -> Tidak ada aset Saham/ETF/Reksadana (yfinance) untuk diupdate.');
            return;
        }

        $apiUrl = $this->pythonApiBaseUrl . '/scrape-yfinance-batch'; // Endpoint GABUNGAN

        try {
            // 2. Kirim SEMUA kode dalam satu POST request
            $response = Http::timeout(120)->post($apiUrl, [ // Timeout lebih lama (2 menit)
                'codes' => $assetCodes
            ]);

            if ($response->successful() && $response->json('prices')) {
                $pricesData = $response->json('prices');
                $this->info(" -> Menerima " . count($pricesData) . " harga dari yfinance API.");

                // 3. Update ke DB
                DB::transaction(function () use ($pricesData) {
                    foreach ($pricesData as $code => $data) {
                        if (isset($data['price']) && isset($data['date'])) {
                            Asset::where('code', $code)
                                ->whereIn('asset_type', ['stock', 'etf', 'mutual_fund'])
                                ->update([
                                    'current_price' => $data['price'],
                                    'price_last_updated_at' => $data['date']
                                ]);
                        }
                    }
                });

                Log::info("Cron Job: Sukses update " . count($pricesData) . " harga yfinance.");
                $this->info(" -> Sukses: " . count($pricesData) . " harga aset yfinance diperbarui.");
            } else {
                $this->error(' -> Gagal: API Python yfinance mengembalikan data tidak valid.');
                Log::error('Cron Job: Gagal update harga yfinance.', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error(' -> Gagal: Tidak dapat terhubung ke API Python yfinance.');
            Log::error('Cron Job: Gagal koneksi ke API Python yfinance.', ['message' => $e->getMessage()]);
        }
    }

    //Single update asset stock
    // private function updateStockPrices()
    // {
    //     $this->line('Mengupdate harga Saham...');

    //     // 1. Ambil semua kode saham dari database
    //     $stockCodes = Asset::where('asset_type', 'stock')->pluck('code')->toArray();

    //     if (empty($stockCodes)) {
    //         $this->info(' -> Tidak ada aset Saham untuk diupdate.');
    //         return;
    //     }

    //     $pythonApiUrl = 'http://127.0.0.1:5000/scrape-stock-prices-batch'; // Endpoint Batch

    //     try {
    //         // 2. Kirim SEMUA kode dalam satu POST request
    //         $response = Http::timeout(60)->post($pythonApiUrl, [ // Timeout lebih lama (60s)
    //             'codes' => $stockCodes
    //         ]);

    //         if ($response->successful() && $response->json('prices')) {
    //             $prices = $response->json('prices');

    //             $this->info(" -> Menerima " . count($prices) . " harga saham dari API.");

    //             // 3. Gunakan Transaksi DB untuk efisiensi
    //             DB::transaction(function () use ($prices) {
    //                 foreach ($prices as $code => $price) {
    //                     Asset::where('code', $code)
    //                         ->where('asset_type', 'stock')
    //                         ->update(['current_price' => $price]);

    //                     $this->line("    - {$code} diperbarui ke Rp " . number_format($price));
    //                 }
    //             });

    //             Log::info("Cron Job: Sukses update " . count($prices) . " harga saham.");
    //         } else {
    //             $this->error(' -> Gagal: API Python Saham mengembalikan data tidak valid.');
    //             Log::error('Cron Job: Gagal update harga Saham.', ['response' => $response->body()]);
    //         }
    //     } catch (\Exception $e) {
    //         $this->error(' -> Gagal: Tidak dapat terhubung ke API Python Saham.');
    //         Log::error('Cron Job: Gagal koneksi ke API Python Saham.', ['message' => $e->getMessage()]);
    //     }
    // }
}
