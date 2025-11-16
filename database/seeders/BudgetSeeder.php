<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('files/budget_per_kategori.csv');

        if (!file_exists($filePath)) {
            $this->command->error("❌ File CSV tidak ditemukan di: {$filePath}");
            return;
        }

        $this->command->info("📂 Membaca file CSV budget dari: {$filePath}");

        $rows = array_map('str_getcsv', file($filePath));
        $header = array_map('trim', array_shift($rows));

        $chunkSize = 500; // jumlah data per batch
        $chunks = array_chunk($rows, $chunkSize);

        $insertedCount = 0;

        foreach ($chunks as $index => $chunk) {
            $batchData = [];

            foreach ($chunk as $row) {
                $data = array_combine($header, $row);

                $batchData[] = [
                    'user_id' => (int) $data['user_id'],
                    'month' => $data['month'],
                    'limit' => (float) $data['limit'],
                    'spent' => (float) $data['spent'],
                    'category_id' => (int) $data['category_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($batchData)) {
                DB::table('budgets')->insert($batchData);
                $insertedCount += count($batchData);
            }

            $this->command->info("✅ Batch ke-" . ($index + 1) . " berhasil diimpor (" . count($batchData) . " baris)");
        }

        $this->command->info("🎯 Selesai! Total data budget diimpor: {$insertedCount} baris.");
    }
}
