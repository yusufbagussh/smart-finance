<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Liability extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'original_amount',
        'current_balance',
        'creditor_name',
        'start_date',
        'due_date',
        'tenor_months',
        'interest_rate',
        'type',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    // Helper untuk cek tipe
    public function isPayable()
    {
        return $this->type === 'payable';
    }

    public function isReceivable()
    {
        return $this->type === 'receivable';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hutang memiliki banyak transaksi pembayaran yang terkait.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
