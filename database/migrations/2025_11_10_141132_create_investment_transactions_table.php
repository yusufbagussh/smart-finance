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
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('portfolios')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('restrict');
            $table->enum('transaction_type', ['buy', 'sell']);
            $table->date('transaction_date');
            // Menggunakan 'decimal' untuk presisi tinggi pada data keuangan dan kuantitas
            $table->decimal('quantity', 18, 8); // Misal: 100.12345678 unit/gram
            $table->decimal('price_per_unit', 18, 2); // Misal: 1500.50 (harga per unit/gram)
            $table->decimal('total_amount', 18, 2); // Total nilai transaksi (quantity * price_per_unit)
            $table->decimal('fees', 18, 2)->default(0); // Biaya tambahan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_transactions');
    }
};
