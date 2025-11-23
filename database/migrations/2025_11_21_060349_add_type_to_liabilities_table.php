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
        Schema::table('liabilities', function (Blueprint $table) {
            // 'payable' = Hutang (Kita pinjam uang)
            // 'receivable' = Piutang (Kita kasih pinjam)
            $table->enum('type', ['payable', 'receivable'])->default('payable')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liabilities', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
