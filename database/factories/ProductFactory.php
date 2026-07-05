<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'INV-'.strtoupper(fake()->unique()->bothify('???-###')),
            'name' => fake()->words(3, true),
            'category_id' => Category::factory(),
            'stock' => fake()->numberBetween(1, 100),
            'location' => fake()->words(2, true),
            'condition' => fake()->randomElement(['baik', 'rusak_ringan', 'rusak_berat']),
            'image' => null,
        ];
    }

    public function outOfStock(): self
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function withStock(int $qty): self
    {
        return $this->state(fn () => ['stock' => $qty]);
    }
}
