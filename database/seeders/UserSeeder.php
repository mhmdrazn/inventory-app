<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with test accounts.
     */
    public function run(): void
    {
        $roleIds = Role::query()->pluck('id', 'name');

        $users = [
            [
                'name' => 'Admin Warehaus',
                'email' => 'admin@warehaus.test',
                'password' => 'password',
                'role' => Role::ADMIN,
            ],
            [
                'name' => 'Staff Inventaris',
                'email' => 'staff@warehaus.test',
                'password' => 'password',
                'role' => Role::STAFF,
            ],
            [
                'name' => 'Manager Warehaus',
                'email' => 'manager@warehaus.test',
                'password' => 'password',
                'role' => Role::MANAGER,
            ],
        ];

        foreach ($users as $userData) {
            $roleId = $roleIds->get($userData['role']) ?? Role::where('name', $userData['role'])->value('id');

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'role_id' => $roleId,
                ],
            );
        }
    }
}
