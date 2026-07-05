<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([Role::ADMIN, Role::STAFF, Role::MANAGER]),
        ];
    }

    public function admin(): self
    {
        return $this->state(fn () => ['name' => Role::ADMIN]);
    }

    public function staff(): self
    {
        return $this->state(fn () => ['name' => Role::STAFF]);
    }

    public function manager(): self
    {
        return $this->state(fn () => ['name' => Role::MANAGER]);
    }
}
