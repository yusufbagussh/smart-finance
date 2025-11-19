<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'deactivated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deactivated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * User memiliki banyak Akun (Dompet, Bank, dll).
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function totalIncome()
    {
        return
            $this
            ->transactions()
            ->whereNull('investment_transaction_id')
            ->where('type', 'income')
            ->sum('amount');
    }

    public function totalExpense()
    {
        return
            $this
            ->transactions()
            ->where('type', 'expense')
            ->whereNull('investment_transaction_id')
            ->sum('amount');
    }

    public function currentBalance()
    {
        return $this->totalIncome() - $this->totalExpense();
    }

    public function isActive(): bool
    {
        return is_null($this->deactivated_at);
    }

    public function isDeactivated(): bool
    {
        return !$this->isActive();
    }

    /**
     * Seorang User bisa memiliki banyak Portofolio.
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    /**
     * Seorang User memiliki banyak Transaksi Investasi
     * MELALUI Portofolio miliknya.
     */
    public function investmentTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(InvestmentTransaction::class, Portfolio::class);
    }
}
