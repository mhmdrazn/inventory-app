<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * All authenticated roles may list products.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'staff', 'manager');
    }

    /**
     * All authenticated roles may view a single product.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->hasRole('admin', 'staff', 'manager');
    }

    /**
     * Only admin and staff may create products.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    /**
     * Only admin and staff may update products.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('admin', 'staff');
    }

    /**
     * Only admin and staff may delete products.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('admin', 'staff');
    }
}
