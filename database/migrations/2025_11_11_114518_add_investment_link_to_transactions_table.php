<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kolom ini bisa NULL, karena tidak semua transaksi adalah transaksi investasi
            $table->foreignId('investment_transaction_id')
                ->nullable()
                ->constrained('investment_transactions')
                ->onDelete('set null'); // Jika transaksi investasi dihapus, set kolom ini jadi NULL
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['investment_transaction_id']);
            $table->dropColumn('investment_transaction_id');
        });
    }
};
