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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('risk_profile', ['conservative', 'moderate', 'aggressive'])
                ->nullable()
                ->after('description')
                ->comment('Profil risiko pengguna: konservatif, moderat, agresif');

            // 2. Tujuan Portofolio (String untuk diisi manual)
            $table->string('goal')->nullable()->after('risk_profile')
                ->comment('Tujuan utama: pensiun, dana pendidikan, dll');

            // 3. Horizon Waktu (Integer untuk jumlah tahun)
            $table->integer('time_horizon')->nullable()->after('goal')
                ->comment('Horizon waktu investasi dalam tahun (misal: 5, 10, 20)');

            // 4. Rencana Investasi (Text untuk deskripsi)
            $table->text('future_plans')->nullable()->after('time_horizon')
                ->comment('Rencana investasi pengguna ke depan');

            // 5. Preferensi Risiko (Text untuk catatan)
            $table->text('risk_tolerance_notes')->nullable()->after('future_plans')
                ->comment('Catatan pribadi pengguna tentang toleransi risiko');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
