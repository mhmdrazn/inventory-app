<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table.
     */
    public function run(): void
    {
        $roles = [
            Role::ADMIN,
            Role::STAFF,
            Role::MANAGER,
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
