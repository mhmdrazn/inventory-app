<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * All authenticated roles may list categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'staff', 'manager');
    }

    /**
     * All authenticated roles may view a single category.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->hasRole('admin', 'staff', 'manager');
    }

    /**
     * Only admin may create categories.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admin may update categories.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admin may delete categories, and only when no products reference it.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole('admin');
    }
}
