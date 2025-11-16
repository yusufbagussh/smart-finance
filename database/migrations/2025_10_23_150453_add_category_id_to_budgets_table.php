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
        Schema::table('budgets', function (Blueprint $table) {
            // Tambahkan foreign key ke tabel categories
            $table->unsignedBigInteger('category_id')->after('month')->nullable();
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');

            // (Opsional) Hapus kolom 'amount' lama jika Anda mau,
            // tapi kita bisa mengubahnya di controller

            // (Sangat Direkomendasikan) Tambahkan unique index
            // Ini mencegah user membuat 2 budget untuk kategori yang sama di bulan yang sama
            $table->unique(['user_id', 'month', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Hapus unique index
            $table->dropUnique(['user_id', 'month', 'category_id']);

            // Hapus foreign key dan kolom category_id
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
