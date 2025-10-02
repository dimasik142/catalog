<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices, gadgets, and accessories',
            ],
            [
                'name' => 'Clothing',
                'slug' => 'clothing',
                'description' => 'Fashion and apparel for all ages',
            ],
            [
                'name' => 'Books',
                'slug' => 'books',
                'description' => 'Physical and digital books across all genres',
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'description' => 'Furniture, decor, and garden supplies',
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'description' => 'Sports equipment and outdoor gear',
            ],
            [
                'name' => 'Toys & Games',
                'slug' => 'toys-games',
                'description' => 'Toys, games, and entertainment for kids',
            ],
            [
                'name' => 'Health & Beauty',
                'slug' => 'health-beauty',
                'description' => 'Personal care and wellness products',
            ],
            [
                'name' => 'Food & Beverages',
                'slug' => 'food-beverages',
                'description' => 'Groceries, snacks, and drinks',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
