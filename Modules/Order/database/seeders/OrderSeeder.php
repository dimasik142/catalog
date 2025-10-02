<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Product;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');

            return;
        }

        $orders = [
            [
                'customer_name' => 'John Smith',
                'customer_email' => 'john.smith@example.com',
                'customer_phone' => '+1-555-0101',
                'status' => 'delivered',
                'items' => [
                    ['product_name' => 'iPhone 15 Pro', 'quantity' => 1],
                    ['product_name' => 'Apple Watch Series 9', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Sarah Johnson',
                'customer_email' => 'sarah.j@example.com',
                'customer_phone' => '+1-555-0102',
                'status' => 'delivered',
                'items' => [
                    ['product_name' => 'MacBook Pro 14"', 'quantity' => 1],
                    ['product_name' => 'Logitech MX Master 3S', 'quantity' => 2],
                ],
            ],
            [
                'customer_name' => 'Michael Brown',
                'customer_email' => 'mbrown@example.com',
                'customer_phone' => '+1-555-0103',
                'status' => 'shipped',
                'items' => [
                    ['product_name' => 'Samsung Galaxy S24', 'quantity' => 1],
                    ['product_name' => 'Sony WH-1000XM5', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Emily Davis',
                'customer_email' => 'emily.davis@example.com',
                'customer_phone' => '+1-555-0104',
                'status' => 'shipped',
                'items' => [
                    ['product_name' => 'Atomic Habits', 'quantity' => 2],
                    ['product_name' => 'The Midnight Library', 'quantity' => 1],
                    ['product_name' => 'Educated', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'David Wilson',
                'customer_email' => 'david.w@example.com',
                'customer_phone' => '+1-555-0105',
                'status' => 'confirmed',
                'items' => [
                    ['product_name' => 'Nike Air Max 270', 'quantity' => 1],
                    ['product_name' => 'Adidas Ultraboost', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Jessica Martinez',
                'customer_email' => 'j.martinez@example.com',
                'customer_phone' => '+1-555-0106',
                'status' => 'confirmed',
                'items' => [
                    ['product_name' => 'Dyson V15 Vacuum', 'quantity' => 1],
                    ['product_name' => 'KitchenAid Stand Mixer', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'James Anderson',
                'customer_email' => 'james.anderson@example.com',
                'customer_phone' => '+1-555-0107',
                'status' => 'confirmed',
                'items' => [
                    ['product_name' => 'Camping Tent 4-Person', 'quantity' => 1],
                    ['product_name' => 'Hiking Backpack 40L', 'quantity' => 2],
                    ['product_name' => 'Yeti Rambler 30oz', 'quantity' => 3],
                ],
            ],
            [
                'customer_name' => 'Linda Taylor',
                'customer_email' => 'linda.t@example.com',
                'customer_phone' => '+1-555-0108',
                'status' => 'pending',
                'items' => [
                    ['product_name' => 'Nintendo Switch OLED', 'quantity' => 1],
                    ['product_name' => 'LEGO Star Wars Set', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Robert Thomas',
                'customer_email' => 'rob.thomas@example.com',
                'customer_phone' => '+1-555-0109',
                'status' => 'pending',
                'items' => [
                    ['product_name' => 'Dell XPS 13', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Mary Jackson',
                'customer_email' => 'mary.jackson@example.com',
                'customer_phone' => '+1-555-0110',
                'status' => 'pending',
                'items' => [
                    ['product_name' => 'Patagonia Fleece Jacket', 'quantity' => 1],
                    ['product_name' => 'Columbia Rain Jacket', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Christopher White',
                'customer_email' => 'chris.white@example.com',
                'customer_phone' => '+1-555-0111',
                'status' => 'delivered',
                'items' => [
                    ['product_name' => 'Organic Coffee Beans 2lb', 'quantity' => 3],
                    ['product_name' => 'Green Tea Collection', 'quantity' => 2],
                ],
            ],
            [
                'customer_name' => 'Patricia Harris',
                'customer_email' => 'patricia.h@example.com',
                'customer_phone' => '+1-555-0112',
                'status' => 'delivered',
                'items' => [
                    ['product_name' => 'Yoga Mat Premium', 'quantity' => 1],
                    ['product_name' => 'Resistance Bands Set', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Daniel Martin',
                'customer_email' => 'daniel.martin@example.com',
                'customer_phone' => '+1-555-0113',
                'status' => 'shipped',
                'items' => [
                    ['product_name' => 'Fitbit Charge 6', 'quantity' => 1],
                    ['product_name' => 'Massage Gun', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Barbara Thompson',
                'customer_email' => 'barbara.t@example.com',
                'customer_phone' => '+1-555-0114',
                'status' => 'confirmed',
                'items' => [
                    ['product_name' => 'Instant Pot Duo', 'quantity' => 1],
                    ['product_name' => 'Nespresso Vertuo', 'quantity' => 1],
                    ['product_name' => 'Olive Oil Extra Virgin', 'quantity' => 2],
                ],
            ],
            [
                'customer_name' => 'Matthew Garcia',
                'customer_email' => 'matt.garcia@example.com',
                'customer_phone' => '+1-555-0115',
                'status' => 'pending',
                'items' => [
                    ['product_name' => 'iPad Air', 'quantity' => 1],
                    ['product_name' => 'Thinking, Fast and Slow', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Elizabeth Martinez',
                'customer_email' => 'liz.martinez@example.com',
                'customer_phone' => '+1-555-0116',
                'status' => 'delivered',
                'items' => [
                    ['product_name' => 'CeraVe Moisturizing Cream', 'quantity' => 2],
                    ['product_name' => 'Oral-B Electric Toothbrush', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Joseph Robinson',
                'customer_email' => 'joe.robinson@example.com',
                'customer_phone' => '+1-555-0117',
                'status' => 'shipped',
                'items' => [
                    ['product_name' => 'Mountain Bike Helmet', 'quantity' => 2],
                    ['product_name' => 'Garden Tool Set', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Susan Clark',
                'customer_email' => 'susan.clark@example.com',
                'customer_phone' => '+1-555-0118',
                'status' => 'confirmed',
                'items' => [
                    ['product_name' => 'Monopoly Board Game', 'quantity' => 1],
                    ['product_name' => 'Hot Wheels Track Set', 'quantity' => 1],
                    ['product_name' => "Rubik's Cube", 'quantity' => 3],
                ],
            ],
            [
                'customer_name' => 'Charles Rodriguez',
                'customer_email' => 'charles.r@example.com',
                'customer_phone' => '+1-555-0119',
                'status' => 'pending',
                'items' => [
                    ['product_name' => "Levi's 501 Original Jeans", 'quantity' => 2],
                    ['product_name' => 'Ralph Lauren Polo Shirt', 'quantity' => 3],
                ],
            ],
            [
                'customer_name' => 'Karen Lewis',
                'customer_email' => 'karen.lewis@example.com',
                'customer_phone' => '+1-555-0120',
                'status' => 'delivered',
                'items' => [
                    ['product_name' => 'The Psychology of Money', 'quantity' => 1],
                    ['product_name' => 'Atomic Habits', 'quantity' => 1],
                    ['product_name' => 'LED Desk Lamp', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Thomas Walker',
                'customer_email' => 'thomas.walker@example.com',
                'customer_phone' => '+1-555-0121',
                'status' => 'shipped',
                'items' => [
                    ['product_name' => 'Protein Powder 2lb', 'quantity' => 2],
                    ['product_name' => 'Himalayan Pink Salt', 'quantity' => 1],
                ],
            ],
            [
                'customer_name' => 'Nancy Hall',
                'customer_email' => 'nancy.hall@example.com',
                'customer_phone' => '+1-555-0122',
                'status' => 'confirmed',
                'items' => [
                    ['product_name' => 'Essential Oil Diffuser', 'quantity' => 2],
                    ['product_name' => 'Dark Chocolate Bar Pack', 'quantity' => 3],
                ],
            ],
            [
                'customer_name' => 'Paul Allen',
                'customer_email' => 'paul.allen@example.com',
                'customer_phone' => '+1-555-0123',
                'status' => 'pending',
                'items' => [
                    ['product_name' => 'Barbie Dreamhouse', 'quantity' => 1],
                    ['product_name' => 'H&M Cotton T-Shirt Pack', 'quantity' => 2],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $total = 0;
            $orderItems = [];

            // Calculate total and prepare order items
            foreach ($orderData['items'] as $itemData) {
                $product = $products->firstWhere('name', $itemData['product_name']);

                if (! $product) {
                    continue;
                }

                $subtotal = $product->price * $itemData['quantity'];
                $total += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            // Skip order if no valid items
            if (empty($orderItems)) {
                continue;
            }

            // Create order
            $order = Order::create([
                'customer_name' => $orderData['customer_name'],
                'customer_email' => $orderData['customer_email'],
                'customer_phone' => $orderData['customer_phone'],
                'total' => $total,
                'status' => $orderData['status'],
            ]);

            // Create order items
            foreach ($orderItems as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'product_name' => $itemData['product_name'],
                    'product_price' => $itemData['product_price'],
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemData['subtotal'],
                ]);
            }
        }

        $this->command->info('Created ' . Order::count() . ' orders with ' . OrderItem::count() . ' order items.');
    }
}
