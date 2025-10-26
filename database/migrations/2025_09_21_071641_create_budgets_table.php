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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id('budget_id');
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->string('month', 7); // Format: YYYY-MM
            $table->decimal('limit', 12, 2);
            $table->decimal('spent', 12, 2)->default(0);
            $table->foreignId('category_id')->constrained('categories', 'id')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
