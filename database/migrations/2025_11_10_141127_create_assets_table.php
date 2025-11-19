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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            // Enum untuk membedakan Reksadana dan Emas
            $table->enum('asset_type', ['mutual_fund', 'gold', 'stock', 'etf']);
            $table->string('code')->unique()->comment('Contoh: BBCA, ANTM');
            $table->string('name')->comment('Contoh: Batavia Dana Saham, Emas Batangan Antam');
            $table->string('issuer')->nullable()->comment('Contoh: Batavia Prosperindo, PT Antam Tbk');
            // Enum untuk satuan
            $table->enum('price_unit', ['unit', 'gram']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
