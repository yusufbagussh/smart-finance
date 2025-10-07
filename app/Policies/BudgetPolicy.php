<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Budget $budget)
    {
        return $user->id === $budget->user_id;
    }

    public function delete(User $user, Budget $budget)
    {
        return $user->id === $budget->user_id;
    }
}
