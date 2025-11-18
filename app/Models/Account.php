<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    // 1. Izinkan kolom ini diisi (Mass Assignment)
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'current_balance',
        'icon',
        'color',
    ];

    // 2. Casting tipe data (agar balance selalu dianggap angka/decimal)
    protected $casts = [
        'current_balance' => 'decimal:2',
    ];

    /**
     * Akun milik satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Akun bisa menjadi SUMBER (Source) di banyak transaksi (Expense/Transfer).
     */
    public function transactionsAsSource(): HasMany
    {
        return $this->hasMany(Transaction::class, 'source_account_id');
    }

    /**
     * Akun bisa menjadi TUJUAN (Destination) di banyak transaksi (Income/Transfer).
     */
    public function transactionsAsDestination(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_account_id');
    }
}
