<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- Jangan lupa import DB
use App\Models\Asset; // <-- Import Model Asset
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = 'database/files/fix_assets.xlsx';
        $this->command->info("📂 Membaca file Excel dari: {$filePath}");

        /** @var Collection $collection */ // <-- 2. FIX UNTUK (Ln 22)
        $collection = Excel::toCollection(null, $filePath)[0];

        /** @var Collection $headers */
        $headers = $collection->shift()->map(function ($item) {
            return Str::slug($item, '_');
        });

        $totalRows = $collection->count();
        if ($totalRows === 0) {
            $this->command->warn("⚠️ File Excel kosong, tidak ada data untuk di-seed.");
            return;
        }

        $chunkSize = 500;

        /** @var Collection $chunks */ // <-- 3. FIX UNTUK (Ln 29)
        $chunks = $collection->chunk($chunkSize);

        $this->command->info("Total {$totalRows} baris data ditemukan. Dibagi menjadi {$chunks->count()} chunk (batch)...");
        $this->command->getOutput()->progressStart($totalRows);

        /** @var Collection $chunk */ // <-- 4. FIX UNTUK (Ln 36)
        foreach ($chunks as $index => $chunk) {
            $batchData = [];

            /** @var Collection $row */
            foreach ($chunk as $row) {
                /** @var Collection $data */
                $data = $headers->combine($row);

                if (empty($data->get('kode'))) {
                    continue;
                }

                $price = 0;
                if ($data->has('harga_saat_ini')) {
                    $priceString = (string) $data->get('harga_saat_ini');
                    $priceString = Str::remove('.', $priceString);
                    $priceString = Str::replace(',', '.', $priceString);
                    $price = (float) $priceString;
                }

                $batchData[] = [
                    'code' => $data->get('kode'),
                    'name' => $data->get('nama_aset'),
                    'asset_type' => $data->get('tipe_aset'),
                    'price_unit' => $data->get('satuan_harga'),
                    'issuer' => $data->get('penerbit'),
                    'current_price' => $price,
                    'price_last_updated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($batchData)) {
                Asset::upsert($batchData, ['code'], [
                    'name',
                    'asset_type',
                    'price_unit',
                    'issuer',
                    'current_price',
                    'price_last_updated_at',
                    'updated_at'
                ]);

                $this->command->getOutput()->progressAdvance(count($batchData));
            }
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("\n🎉 Selesai! Total {$totalRows} data aset telah diproses.");


        // // 1. Aset Reksadana Saham
        // Asset::create([
        //     'asset_type' => 'mutual_fund',
        //     'code' => 'BDS',
        //     'name' => 'Batavia Dana Saham',
        //     'issuer' => 'Batavia Prosperindo Aset Manajemen',
        //     'price_unit' => 'unit',
        //     'current_price' => 1750.50 // Harga NAV terkini (contoh)
        // ]);

        // // 2. Aset Reksadana Pasar Uang
        // Asset::create([
        //     'asset_type' => 'mutual_fund',
        //     'code' => 'SPU',
        //     'name' => 'Sucorinvest Pasar Uang',
        //     'issuer' => 'Sucorinvest Asset Management',
        //     'price_unit' => 'unit',
        //     'current_price' => 1200.00 // Harga NAV terkini (contoh)
        // ]);

        // // 3. Aset Emas
        // Asset::create([
        //     'asset_type' => 'gold',
        //     'code' => 'ANTAM01',
        //     'name' => 'Emas Batangan Antam',
        //     'issuer' => 'PT Antam Tbk',
        //     'price_unit' => 'gram',
        //     'current_price' => 1350000.00 // Harga per gram terkini (contoh)
        // ]);
    }
}
