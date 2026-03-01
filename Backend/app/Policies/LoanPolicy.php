<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'librarian';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loan $loan): bool
    {
        return $user->role === 'librarian' || $loan->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $_user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Loan $_loan): bool
    {
        return $user->role === 'librarian';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Loan $_loan): bool
    {
        return $user->role === 'librarian';
    }
}
