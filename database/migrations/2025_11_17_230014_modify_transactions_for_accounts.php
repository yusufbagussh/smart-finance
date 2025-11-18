<?php
// database/migrations/..._modify_transactions_for_accounts.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- 1. Import DB
use App\Models\User; // <-- 2. Import User
use App\Models\Account; // <-- 3. Import Account

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // 4. Ubah Tipe Transaksi
            // $table->string('type', 20)->change(); // Ubah dari ENUM ke STRING
            // DB::statement("ALTER TABLE transactions ALTER COLUMN type TYPE VARCHAR(20) USING type::VARCHAR(20)"); // Khusus Postgres
            // DB::statement("UPDATE transactions SET type = 'expense' WHERE type = '0'"); // Jika Anda pakai 0/1
            // DB::statement("UPDATE transactions SET type = 'income' WHERE type = '1'"); // Jika Anda pakai 0/1


            // 5. Tambahkan Relasi Akun (bisa null dulu)
            $table->foreignId('source_account_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('set null');

            $table->foreignId('destination_account_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('set null');

            // 6. Buat category_id bisa null (untuk transfer)
            $table->foreignId('category_id')->nullable()->change();
        });

        // --- 7. BAGIAN AJAIB (MIGRASI DATA LAMA) ---
        // Kita harus keluar dari Schema::table untuk menjalankan query

        // Ambil semua user yang punya transaksi
        $usersWithTransactions = DB::table('transactions')->distinct()->pluck('user_id');

        foreach ($usersWithTransactions as $userId) {
            // Buat 1 Akun Default (Dompet Utama) untuk user ini
            $defaultAccount = Account::create([
                'user_id' => $userId,
                'name' => 'Dompet Utama',
                'type' => 'cash',
                'current_balance' => 0, // Kita akan hitung saldonya nanti
                'icon' => 'fas fa-wallet',
                'color' => '#6B7280'
            ]);

            // Hubungkan semua transaksi LAMA ke Dompet Utama ini
            // Pemasukan lama:
            DB::table('transactions')
                ->where('user_id', $userId)
                ->where('type', 'income')
                ->update(['destination_account_id' => $defaultAccount->id]);

            // Pengeluaran lama:
            DB::table('transactions')
                ->where('user_id', $userId)
                ->where('type', 'expense')
                ->update(['source_account_id' => $defaultAccount->id]);

            // Hitung ulang saldo Dompet Utama (opsional tapi bagus)
            $totalIncome = DB::table('transactions')->where('destination_account_id', $defaultAccount->id)->sum('amount');
            $totalExpense = DB::table('transactions')->where('source_account_id', $defaultAccount->id)->sum('amount');
            $defaultAccount->update(['current_balance' => $totalIncome - $totalExpense]);
        }

        // Ubah 'type' menjadi ENUM lagi (lebih efisien)
        // DB::statement("ALTER TABLE transactions MODIFY type ENUM('income', 'expense', 'transfer') NOT NULL"); // MySQL
        // DB::statement("ALTER TABLE transactions ALTER COLUMN type TYPE type_enum USING type::type_enum"); // Postgres (jika Anda buat ENUM type)
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Hapus kolom akun
            $table->dropForeign(['source_account_id']);
            $table->dropColumn('source_account_id');

            $table->dropForeign(['destination_account_id']);
            $table->dropColumn('destination_account_id');

            // Buat category_id NOT NULL lagi
            $table->foreignId('category_id')->nullable(false)->change();
        });

        // Catatan: Data akun dan relasi yang dibuat di migrasi ini TIDAK akan dihapus.
        // Jika
    }
};
