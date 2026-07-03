<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     */
    public function run(): void
    {
        $categories = [
            'Elektronik',
            'Furniture',
            'ATK (Alat Tulis Kantor)',
            'Perangkat Jaringan',
            'Kendaraan',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}
