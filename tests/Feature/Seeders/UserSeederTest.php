<?php

namespace Tests\Feature\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_updates_existing_demo_accounts_with_their_roles(): void
    {
        $adminRole = Role::create(['name' => Role::ADMIN]);
        Role::create(['name' => Role::STAFF]);
        Role::create(['name' => Role::MANAGER]);

        User::create([
            'name' => 'Admin Telkomsel',
            'email' => 'admin@telkomsel.test',
            'password' => 'password',
            'role_id' => null,
        ]);

        (new UserSeeder)->run();

        $user = User::where('email', 'admin@telkomsel.test')->with('role')->firstOrFail();

        $this->assertSame($adminRole->id, $user->role_id);
        $this->assertSame(Role::ADMIN, $user->role?->name);
    }
}
