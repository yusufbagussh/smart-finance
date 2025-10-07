<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $primaryKey = 'budget_id';

    protected $fillable = [
        'user_id',
        'month',
        'limit',
        'spent',
    ];

    protected $casts = [
        'limit' => 'decimal:2',
        'spent' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function remainingBudget()
    {
        return $this->limit - $this->spent;
    }

    public function progressPercentage()
    {
        if ($this->limit == 0) return 0;
        return min(($this->spent / $this->limit) * 100, 100);
    }

    public function isOverBudget()
    {
        return $this->spent > $this->limit;
    }

    // Update spent amount based on transactions
    public function updateSpentAmount()
    {
        $spent = $this->user->transactions()
            ->where('type', 'expense')
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$this->month])
            ->sum('amount');

        $this->update(['spent' => $spent]);
        return $this;
    }
}
