<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http; // <-- 1. Import HTTP Client
use Illuminate\Support\Facades\Log;  // <-- 2. Import Log
use App\Models\Asset;                // <-- 3. Import Model Asset
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

    /**
     * Jalankan logika command.
     */
    public function handle()
    {
        $this->info('Memulai update harga aset...');
        Log::info('Cron Job: Memulai update harga aset...');

        // --- 1. PROSES HARGA EMAS (Tetap sama) ---
        $this->updateGoldPrices();

        // --- 2. PROSES HARGA SAHAM (Logika Baru) ---
        $this->updateStockPrices();

        $this->info('Update harga aset selesai.');
        return 0;
    }

    /**
     * Mengambil dan memperbarui harga emas.
     */
    private function updateGoldPrices()
    {
        $this->line('Mengupdate harga Emas...');
        $pythonApiUrl = 'http://127.0.0.1:5000/scrape-gold-price'; // Endpoint Emas

        try {
            $response = Http::timeout(30)->get($pythonApiUrl);

            if ($response->successful() && $response->json('gold_price')) {
                $price = $response->json('gold_price');
                Asset::where('asset_type', 'gold')->update(['current_price' => $price]);
                $this->info(" -> Sukses: Harga Emas diperbarui ke Rp " . number_format($price));
                Log::info("Cron Job: Sukses update harga Emas ke Rp {$price}.");
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
     * Mengambil dan memperbarui semua harga saham secara batch.
     */
    private function updateStockPrices()
    {
        $this->line('Mengupdate harga Saham...');

        $stockCodes = Asset::where('asset_type', 'stock')->pluck('code')->toArray();

        if (empty($stockCodes)) {
            $this->info(' -> Tidak ada aset Saham untuk diupdate.');
            return;
        }

        $pythonApiUrl = 'http://127.0.0.1:5000/scrape-stock-prices-batch';

        try {
            $response = Http::timeout(60)->post($pythonApiUrl, ['codes' => $stockCodes]);

            if ($response->successful() && $response->json('prices')) {
                $pricesData = $response->json('prices'); // Ini sekarang {"BBCA": {"price": ..., "date": ...}}

                $this->info(" -> Menerima " . count($pricesData) . " harga saham dari API.");

                DB::transaction(function () use ($pricesData) {
                    // !! INI PERUBAHANNYA !!
                    foreach ($pricesData as $code => $data) {
                        // Pastikan data yang diterima lengkap
                        if (isset($data['price']) && isset($data['date'])) {

                            Asset::where('code', $code)
                                ->where('asset_type', 'stock')
                                ->update([
                                    'current_price' => $data['price'],
                                    'price_last_updated_at' => $data['date'] // <-- SIMPAN TANGGAL
                                ]);

                            $this->line("    - {$code} diperbarui ke Rp " . number_format($data['price']) . " (per tgl {$data['date']})");
                        }
                    }
                });

                Log::info("Cron Job: Sukses update " . count($pricesData) . " harga saham.");
            } else {
                $this->error(' -> Gagal: API Python Saham mengembalikan data tidak valid.');
                Log::error('Cron Job: Gagal update harga Saham.', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error(' -> Gagal: Tidak dapat terhubung ke API Python Saham.');
            Log::error('Cron Job: Gagal koneksi ke API Python Saham.', ['message' => $e->getMessage()]);
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
