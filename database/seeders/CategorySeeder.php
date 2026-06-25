<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Pizza',
                'slug' => 'pizza',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Burgers',
                'slug' => 'burgers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Drinks',
                'slug' => 'drinks',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shawarma',
                'slug' => 'shawarma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fries',
                'slug' => 'fries',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pasta',
                'slug' => 'pasta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
