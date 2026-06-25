<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [

            // Pizza
            [
                'category_id' => 1,
                'name' => 'Chicken Tikka Pizza',
                'slug' => 'chicken-tikka-pizza',
                'variants' => [
                    ['name' => 'Small', 'price' => 550, 'is_default' => true],
                    ['name' => 'Medium', 'price' => 850],
                    ['name' => 'Large', 'price' => 1200],
                ]
            ],

            [
                'category_id' => 1,
                'name' => 'Fajita Pizza',
                'slug' => 'fajita-pizza',
                'variants' => [
                    ['name' => 'Small', 'price' => 600, 'is_default' => true],
                    ['name' => 'Medium', 'price' => 900],
                    ['name' => 'Large', 'price' => 1250],
                ]
            ],

            // Burgers
            [
                'category_id' => 2,
                'name' => 'Zinger Burger',
                'slug' => 'zinger-burger',
                'variants' => [
                    ['name' => 'Regular', 'price' => 350, 'is_default' => true],
                    ['name' => 'Meal', 'price' => 550],
                ]
            ],

            [
                'category_id' => 2,
                'name' => 'Beef Burger',
                'slug' => 'beef-burger',
                'variants' => [
                    ['name' => 'Regular', 'price' => 450, 'is_default' => true],
                    ['name' => 'Meal', 'price' => 650],
                ]
            ],

            // Shawarma
            [
                'category_id' => 3,
                'name' => 'Chicken Shawarma',
                'slug' => 'chicken-shawarma',
                'variants' => [
                    ['name' => 'Regular', 'price' => 250, 'is_default' => true],
                    ['name' => 'Jumbo', 'price' => 400],
                ]
            ],

            [
                'category_id' => 3,
                'name' => 'Arabian Shawarma',
                'slug' => 'arabian-shawarma',
                'variants' => [
                    ['name' => 'Regular', 'price' => 300, 'is_default' => true],
                    ['name' => 'Jumbo', 'price' => 450],
                ]
            ],

            // Fries
            [
                'category_id' => 4,
                'name' => 'Masala Fries',
                'slug' => 'masala-fries',
                'variants' => [
                    ['name' => 'Regular', 'price' => 180, 'is_default' => true],
                    ['name' => 'Large', 'price' => 300],
                ]
            ],

            [
                'category_id' => 4,
                'name' => 'Loaded Fries',
                'slug' => 'loaded-fries',
                'variants' => [
                    ['name' => 'Regular', 'price' => 350, 'is_default' => true],
                    ['name' => 'Large', 'price' => 550],
                ]
            ],

            // Pasta
            [
                'category_id' => 5,
                'name' => 'Alfredo Pasta',
                'slug' => 'alfredo-pasta',
                'variants' => [
                    ['name' => 'Regular', 'price' => 450, 'is_default' => true],
                    ['name' => 'Large', 'price' => 700],
                ]
            ],

            [
                'category_id' => 5,
                'name' => 'Creamy Pasta',
                'slug' => 'creamy-pasta',
                'variants' => [
                    ['name' => 'Regular', 'price' => 500, 'is_default' => true],
                    ['name' => 'Large', 'price' => 750],
                ]
            ],
        ];

        foreach ($products as $item) {

            $variants = $item['variants'];
            unset($item['variants']);

            $product = Product::create($item);

            foreach ($variants as $variant) {

                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variant['name'],
                    'price' => $variant['price'],
                    'is_default' => $variant['is_default'] ?? false,
                    'is_active' => true,
                ]);
            }
        }
    }
}
