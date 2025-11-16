<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Tambahkan 'current_price' ke casts
    protected $casts = [
        'current_price' => 'decimal:2',
    ];

    /**
     * Satu Aset (misal: Emas Antam) bisa muncul di banyak transaksi.
     */
    public function investmentTransactions(): HasMany
    {
        return $this->hasMany(InvestmentTransaction::class);
    }
}
