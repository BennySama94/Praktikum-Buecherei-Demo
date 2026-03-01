<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $_user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $_user, Book $_book): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'librarian';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Book $_book): bool
    {
        return $user->role === 'librarian';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Book $_book): bool
    {
        return $user->role === 'librarian';
    }
}
