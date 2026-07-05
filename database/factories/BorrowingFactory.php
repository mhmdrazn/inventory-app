<?php

namespace Database\Factories;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrowing>
 */
class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'borrower_name' => fake()->name(),
            'status' => 'dipinjam',
            'borrowed_at' => now()->subDays(1),
            'due_at' => now()->addDays(7),
            'returned_at' => null,
            'notes' => null,
        ];
    }

    public function returned(): self
    {
        return $this->state(fn () => [
            'status' => 'dikembalikan',
            'returned_at' => now(),
        ]);
    }

    public function overdue(): self
    {
        return $this->state(fn () => [
            'status' => 'dipinjam',
            'borrowed_at' => now()->subDays(30),
            'due_at' => now()->subDays(3),
        ]);
    }
}
