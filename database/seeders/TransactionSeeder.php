<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path CSV untuk masing-masing user
        $files = [
            1 => database_path('files/riwayat_transaksi_indonesia_lengkap.csv'),
            2 => database_path('files/riwayat_transaksi_user2.csv'),
        ];

        $categoryMap = Category::pluck('category_id', 'name')->toArray();

        foreach ($files as $userId => $filePath) {

            if (!file_exists($filePath)) {
                $this->command->warn("⚠️ File untuk user_id {$userId} tidak ditemukan: {$filePath}");
                continue;
            }

            $this->command->info("📂 Mengimpor transaksi untuk user_id {$userId} dari: {$filePath}");

            $rows = array_map('str_getcsv', file($filePath));
            $header = array_map('trim', array_shift($rows));

            $chunkSize = 500;
            $chunks = array_chunk($rows, $chunkSize);
            $insertedCount = 0;

            foreach ($chunks as $index => $chunk) {
                $batchData = [];

                foreach ($chunk as $row) {
                    $data = array_combine($header, $row);
                    $categoryName = trim($data['category']);
                    $categoryId = $categoryMap[$categoryName] ?? null;

                    if (!$categoryId) {
                        continue;
                    }

                    $batchData[] = [
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                        'date' => Carbon::parse($data['date'])->format('Y-m-d'),
                        'amount' => (float) $data['amount'],
                        'type' => $data['type'],
                        'description' => $data['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($batchData)) {
                    DB::table('transactions')->insert($batchData);
                    $insertedCount += count($batchData);
                }

                $this->command->info("✅ User {$userId} — Batch ke-" . ($index + 1) . ": " . count($batchData) . " baris diimpor");
            }

            $this->command->info("🎉 Selesai untuk user_id {$userId}! Total: {$insertedCount} baris.\n");
        }

        $this->command->info("🔥 Semua data transaksi berhasil diimpor untuk seluruh user!");
    }
}
