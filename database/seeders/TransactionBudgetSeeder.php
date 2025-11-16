<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionBudgetSeeder extends Seeder
{
    public function run(): void
    {
        $transactionFiles = [
            database_path('files/riwayat_transaksi_user1_balanced_6000.csv'),
            database_path('files/riwayat_transaksi_user2_balanced_6000.csv'),
        ];

        $budgetFiles = [
            database_path('files/budget_per_kategori_user1_balanced_6000.csv'),
            database_path('files/budget_per_kategori_user2_balanced_6000.csv'),
        ];

        $this->importCsv('transactions', $transactionFiles, 500);
        $this->importCsv('budgets', $budgetFiles, 500);
    }

    /**
     * Import data dari beberapa CSV dengan chunking dan batch insert.
     */
    private function importCsv(string $table, array $files, int $chunkSize = 500)
    {
        foreach ($files as $filePath) {
            if (!file_exists($filePath)) {
                $this->command->warn("⚠️ File tidak ditemukan: {$filePath}");
                continue;
            }

            $this->command->info("📂 Memproses file: {$filePath}");

            $rows = array_map('str_getcsv', file($filePath));
            $header = array_map('trim', array_shift($rows));
            $chunks = array_chunk($rows, $chunkSize);
            $totalInserted = 0;

            foreach ($chunks as $index => $chunk) {
                $data = [];

                foreach ($chunk as $row) {
                    $item = array_combine($header, $row);

                    if ($table === 'transactions') {
                        $data[] = [
                            'user_id' => (int) $item['user_id'],
                            'date' => $item['date'],
                            'description' => $item['description'],
                            'category_id' => $item['category_id'],
                            'type' => $item['type'],
                            'amount' => (float) $item['amount'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } elseif ($table === 'budgets') {
                        $data[] = [
                            'user_id' => (int) $item['user_id'],
                            'month' => $item['month'],
                            'limit' => (float) $item['limit'],
                            'spent' => (float) $item['spent'],
                            'category_id' => (int) $item['category_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                DB::table($table)->insert($data);
                $totalInserted += count($data);

                $this->command->info("✅ {$table}: Batch " . ($index + 1) . " berhasil, total: {$totalInserted} baris.");
            }

            $this->command->info("🎯 Selesai impor {$totalInserted} baris ke tabel {$table} dari {$filePath}");
        }
    }
}
