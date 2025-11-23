<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('liabilities', function (Blueprint $table) {
            $table->id();

            // Kolom Wajib (Mandatory Columns)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nama Pinjaman (e.g., KPR BCA, Cicilan Mobil)
            $table->text('description')->nullable(); // Deskripsi tambahan

            // Kolom Finansial (Financial Columns)
            $table->decimal('original_amount', 15, 2); // Jumlah pinjaman awal yang diterima
            $table->decimal('current_balance', 15, 2); // Saldo hutang yang tersisa saat ini

            // Kolom Pembayaran (Payment Details)
            $table->string('creditor_name')->nullable(); // Pihak yang memberi pinjaman (e.g., Bank XYZ, Budi)
            $table->date('start_date'); // Tanggal Pinjaman diterima
            $table->date('due_date')->nullable(); // Tanggal jatuh tempo keseluruhan pinjaman (Opsional)
            $table->unsignedInteger('tenor_months')->nullable(); // Jangka waktu (dalam bulan)
            $table->decimal('interest_rate', 5, 2)->nullable(); // Suku bunga (misalnya, 5.50)

            $table->timestamps();
        });

        // Tambahkan kolom foreign key di tabel 'transactions'
        // untuk mengaitkan pembayaran cicilan dengan hutang tertentu.
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('liability_id')
                ->nullable() // Boleh kosong, karena tidak semua transaksi adalah pembayaran hutang
                ->after('category_id') // Letakkan setelah category_id
                ->constrained('liabilities') // Terhubung ke tabel liabilities
                ->onDelete('set null'); // Jika hutang dihapus, set ID-nya menjadi NULL di transaksi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom 'liability_id' di tabel 'transactions' terlebih dahulu
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('liability_id');
        });

        // Kemudian hapus tabel 'liabilities'
        Schema::dropIfExists('liabilities');
    }
};
