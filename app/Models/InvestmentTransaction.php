<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Menentukan tipe data untuk casting otomatis
     */
    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'decimal:8',
        'price_per_unit' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'fees' => 'decimal:2',
    ];

    /**
     * Satu Transaksi pasti milik satu Portofolio.
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    /**
     * Satu Transaksi pasti terkait dengan satu Aset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
