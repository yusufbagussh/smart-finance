<?php

// database/migrations/..._create_accounts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name'); // Misal: "Dompet Tunai", "BCA", "OVO"

            // Tipe untuk membedakan (opsional tapi bagus)
            $table->string('type')->default('cash'); // Misal: 'cash', 'bank_account', 'e_wallet'

            // Saldo saat ini. Ini akan di-update oleh TransactionController
            $table->decimal('current_balance', 18, 2)->default(0);

            $table->string('icon')->nullable()->default('fas fa-wallet');
            $table->string('color')->nullable()->default('#6B7280'); // abu-abu

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
