<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    /**
     * All authenticated roles may list borrowings.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'staff', 'manager');
    }

    /**
     * All authenticated roles may view a borrowing.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->hasRole('admin', 'staff', 'manager');
    }

    /**
     * Only admin and staff may create a new borrowing.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    /**
     * Only admin and staff may update a borrowing.
     */
    public function update(User $user, Borrowing $borrowing): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    /**
     * Only admin and staff may delete a borrowing.
     */
    public function delete(User $user, Borrowing $borrowing): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    /**
     * Only admin and staff may mark items as returned.
     */
    public function return(User $user, Borrowing $borrowing): bool
    {
        return $user->hasRole('admin', 'staff');
    }
}
