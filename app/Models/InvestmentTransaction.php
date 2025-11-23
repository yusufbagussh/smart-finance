<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * Relasi ke transaksi cash flow utama (yang memotong/menambah saldo akun).
     */
    public function mainTransaction(): HasOne
    {
        // Foreign Key di tabel 'transactions' adalah 'investment_transaction_id'
        return $this->hasOne(Transaction::class, 'investment_transaction_id');
    }
}
