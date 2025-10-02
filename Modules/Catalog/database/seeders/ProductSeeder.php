<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');

            return;
        }

        $products = [
            // Electronics
            [
                'category' => 'Electronics',
                'items' => [
                    ['name' => 'iPhone 15 Pro', 'description' => 'Latest Apple smartphone with A17 Pro chip', 'price' => 999.99, 'stock' => 50],
                    ['name' => 'Samsung Galaxy S24', 'description' => 'Flagship Android phone with AI features', 'price' => 899.99, 'stock' => 45],
                    ['name' => 'MacBook Pro 14"', 'description' => 'M3 Pro chip, 18GB RAM, 512GB SSD', 'price' => 1999.99, 'stock' => 25],
                    ['name' => 'Sony WH-1000XM5', 'description' => 'Premium noise-cancelling wireless headphones', 'price' => 399.99, 'stock' => 60],
                    ['name' => 'iPad Air', 'description' => '10.9-inch Liquid Retina display', 'price' => 599.99, 'stock' => 40],
                    ['name' => 'Dell XPS 13', 'description' => 'Ultra-portable laptop with Intel i7', 'price' => 1299.99, 'stock' => 30],
                    ['name' => 'Apple Watch Series 9', 'description' => 'Smart watch with health tracking', 'price' => 429.99, 'stock' => 55],
                    ['name' => 'Logitech MX Master 3S', 'description' => 'Wireless ergonomic mouse', 'price' => 99.99, 'stock' => 100],
                ],
            ],
            // Clothing
            [
                'category' => 'Clothing',
                'items' => [
                    ['name' => 'Levi\'s 501 Original Jeans', 'description' => 'Classic straight fit denim', 'price' => 89.99, 'stock' => 75],
                    ['name' => 'Nike Air Max 270', 'description' => 'Comfortable running shoes', 'price' => 149.99, 'stock' => 60],
                    ['name' => 'Patagonia Fleece Jacket', 'description' => 'Warm outdoor jacket', 'price' => 129.99, 'stock' => 40],
                    ['name' => 'Adidas Ultraboost', 'description' => 'Performance running shoes', 'price' => 179.99, 'stock' => 50],
                    ['name' => 'Ralph Lauren Polo Shirt', 'description' => 'Classic fit cotton polo', 'price' => 69.99, 'stock' => 85],
                    ['name' => 'Columbia Rain Jacket', 'description' => 'Waterproof outdoor jacket', 'price' => 99.99, 'stock' => 45],
                    ['name' => 'H&M Cotton T-Shirt Pack', 'description' => 'Pack of 3 basic tees', 'price' => 24.99, 'stock' => 120],
                ],
            ],
            // Books
            [
                'category' => 'Books',
                'items' => [
                    ['name' => 'Atomic Habits', 'description' => 'By James Clear - Transform your habits', 'price' => 16.99, 'stock' => 95],
                    ['name' => 'The Midnight Library', 'description' => 'By Matt Haig - Fiction bestseller', 'price' => 14.99, 'stock' => 80],
                    ['name' => 'Educated', 'description' => 'By Tara Westover - Memoir', 'price' => 15.99, 'stock' => 70],
                    ['name' => 'Thinking, Fast and Slow', 'description' => 'By Daniel Kahneman - Psychology', 'price' => 18.99, 'stock' => 60],
                    ['name' => 'Where the Crawdads Sing', 'description' => 'By Delia Owens - Mystery novel', 'price' => 15.99, 'stock' => 0],
                    ['name' => 'The Psychology of Money', 'description' => 'By Morgan Housel - Finance', 'price' => 16.99, 'stock' => 75],
                ],
            ],
            // Home & Garden
            [
                'category' => 'Home & Garden',
                'items' => [
                    ['name' => 'Dyson V15 Vacuum', 'description' => 'Cordless vacuum with laser detection', 'price' => 649.99, 'stock' => 20],
                    ['name' => 'KitchenAid Stand Mixer', 'description' => '5-quart tilt-head mixer', 'price' => 379.99, 'stock' => 35],
                    ['name' => 'Instant Pot Duo', 'description' => '7-in-1 electric pressure cooker', 'price' => 89.99, 'stock' => 65],
                    ['name' => 'Nespresso Vertuo', 'description' => 'Coffee and espresso maker', 'price' => 179.99, 'stock' => 45],
                    ['name' => 'Garden Tool Set', 'description' => '10-piece stainless steel tools', 'price' => 49.99, 'stock' => 55],
                    ['name' => 'LED Desk Lamp', 'description' => 'Adjustable brightness with USB port', 'price' => 34.99, 'stock' => 80],
                ],
            ],
            // Sports & Outdoors
            [
                'category' => 'Sports & Outdoors',
                'items' => [
                    ['name' => 'Yeti Rambler 30oz', 'description' => 'Insulated stainless steel tumbler', 'price' => 39.99, 'stock' => 100],
                    ['name' => 'Yoga Mat Premium', 'description' => 'Non-slip exercise mat with strap', 'price' => 29.99, 'stock' => 75],
                    ['name' => 'Camping Tent 4-Person', 'description' => 'Waterproof family tent', 'price' => 149.99, 'stock' => 30],
                    ['name' => 'Hiking Backpack 40L', 'description' => 'Durable outdoor backpack', 'price' => 89.99, 'stock' => 45],
                    ['name' => 'Resistance Bands Set', 'description' => 'Set of 5 exercise bands', 'price' => 24.99, 'stock' => 90],
                    ['name' => 'Mountain Bike Helmet', 'description' => 'Adjustable safety helmet', 'price' => 59.99, 'stock' => 55],
                ],
            ],
            // Toys & Games
            [
                'category' => 'Toys & Games',
                'items' => [
                    ['name' => 'LEGO Star Wars Set', 'description' => 'Millennium Falcon building kit', 'price' => 159.99, 'stock' => 40],
                    ['name' => 'Nintendo Switch OLED', 'description' => 'Handheld gaming console', 'price' => 349.99, 'stock' => 25],
                    ['name' => 'Monopoly Board Game', 'description' => 'Classic family board game', 'price' => 24.99, 'stock' => 70],
                    ['name' => 'Barbie Dreamhouse', 'description' => '3-story dollhouse playset', 'price' => 199.99, 'stock' => 20],
                    ['name' => 'Hot Wheels Track Set', 'description' => 'Loop and launch track', 'price' => 49.99, 'stock' => 60],
                    ['name' => 'Rubik\'s Cube', 'description' => 'Classic 3x3 puzzle cube', 'price' => 9.99, 'stock' => 150],
                ],
            ],
            // Health & Beauty
            [
                'category' => 'Health & Beauty',
                'items' => [
                    ['name' => 'CeraVe Moisturizing Cream', 'description' => 'Daily face and body moisturizer', 'price' => 16.99, 'stock' => 85],
                    ['name' => 'Oral-B Electric Toothbrush', 'description' => 'Rechargeable sonic toothbrush', 'price' => 89.99, 'stock' => 50],
                    ['name' => 'Fitbit Charge 6', 'description' => 'Fitness tracker with GPS', 'price' => 159.99, 'stock' => 40],
                    ['name' => 'Essential Oil Diffuser', 'description' => 'Ultrasonic aromatherapy diffuser', 'price' => 29.99, 'stock' => 70],
                    ['name' => 'Massage Gun', 'description' => 'Deep tissue percussion massager', 'price' => 99.99, 'stock' => 35],
                    ['name' => 'Hair Dryer Ionic', 'description' => 'Professional salon dryer', 'price' => 79.99, 'stock' => 0],
                ],
            ],
            // Food & Beverages
            [
                'category' => 'Food & Beverages',
                'items' => [
                    ['name' => 'Organic Coffee Beans 2lb', 'description' => 'Medium roast Arabica beans', 'price' => 24.99, 'stock' => 100],
                    ['name' => 'Protein Powder 2lb', 'description' => 'Whey isolate vanilla flavor', 'price' => 39.99, 'stock' => 75],
                    ['name' => 'Green Tea Collection', 'description' => 'Organic tea variety pack', 'price' => 19.99, 'stock' => 90],
                    ['name' => 'Olive Oil Extra Virgin', 'description' => 'Cold-pressed Italian oil', 'price' => 16.99, 'stock' => 65],
                    ['name' => 'Himalayan Pink Salt', 'description' => 'Fine grain 1lb container', 'price' => 9.99, 'stock' => 120],
                    ['name' => 'Dark Chocolate Bar Pack', 'description' => 'Pack of 6 organic bars', 'price' => 14.99, 'stock' => 85],
                ],
            ],
        ];

        foreach ($products as $categoryData) {
            $category = $categories->firstWhere('name', $categoryData['category']);

            if (! $category) {
                continue;
            }

            foreach ($categoryData['items'] as $item) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                ]);
            }
        }

        $this->command->info('Created ' . Product::count() . ' products across ' . $categories->count() . ' categories.');
    }
}
