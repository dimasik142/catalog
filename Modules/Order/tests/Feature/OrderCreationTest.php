<?php

use Livewire\Livewire;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Order\Livewire\CreateOrder;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

uses()->group('order', 'order-creation');

beforeEach(function () {
    $this->category = Category::factory()->create(['name' => 'Electronics']);
    $this->product1 = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Laptop',
        'price' => 999.99,
        'stock' => 10,
    ]);
    $this->product2 = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Mouse',
        'price' => 29.99,
        'stock' => 50,
    ]);
});

test('create order page loads successfully', function () {
    $response = $this->get(route('order.create'));

    $response->assertStatus(200);
});

test('can submit order with valid data', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 2,
            'stock' => $this->product1->stock,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '+1234567890')
        ->call('submitOrder')
        ->assertSessionHas('success');

    $this->assertDatabaseHas('orders', [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+1234567890',
        'total' => 1999.98,
        'status' => Order::STATUS_PENDING,
    ]);
});

test('order creation creates order items', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
        [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'price' => $this->product2->price,
            'quantity' => 3,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', 'Jane Smith')
        ->set('customer_email', 'jane@example.com')
        ->set('customer_phone', '+9876543210')
        ->call('submitOrder');

    $order = Order::where('customer_email', 'jane@example.com')->first();

    expect($order->orderItems)->toHaveCount(2);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $this->product1->id,
        'product_name' => $this->product1->name,
        'quantity' => 1,
        'subtotal' => 999.99,
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $this->product2->id,
        'product_name' => $this->product2->name,
        'quantity' => 3,
        'subtotal' => 89.97,
    ]);
});

test('cannot submit order with empty cart', function () {
    Livewire::test(CreateOrder::class)
        ->set('cart', [])
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '+1234567890')
        ->call('submitOrder');

    expect(Order::count())->toBe(0);
});

test('validates customer name is required', function () {
    session(['cart' => [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
    ]]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', '')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '+1234567890')
        ->call('submitOrder')
        ->assertHasErrors(['customer_name' => 'required']);
});

test('validates customer email format', function () {
    session(['cart' => [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
    ]]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'invalid-email')
        ->set('customer_phone', '+1234567890')
        ->call('submitOrder')
        ->assertHasErrors(['customer_email' => 'email']);
});

test('validates customer phone is required', function () {
    session(['cart' => [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
    ]]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '')
        ->call('submitOrder')
        ->assertHasErrors(['customer_phone' => 'required']);
});

test('can search for products', function () {
    Livewire::test(CreateOrder::class)
        ->set('search', 'Laptop')
        ->assertSet('searchResults', function ($results) {
            return count($results) > 0 && $results[0]['name'] === 'Laptop';
        });
});

test('search requires at least 2 characters', function () {
    Livewire::test(CreateOrder::class)
        ->set('search', 'L')
        ->assertSet('searchResults', []);
});

test('can add product to cart from search', function () {
    Livewire::test(CreateOrder::class)
        ->call('addToCart', $this->product1->id);

    expect(session('cart'))->not->toBeEmpty()
        ->and(session('cart')[0]['product_id'])->toBe($this->product1->id);
});

test('can update product quantity in cart', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 2,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->call('updateQuantity', 0, 5);

    expect(session('cart')[0]['quantity'])->toBe(5);
});

test('can remove product from cart', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
        [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'price' => $this->product2->price,
            'quantity' => 1,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->call('removeFromCart', 0);

    $updatedCart = session('cart');
    expect($updatedCart)->toHaveCount(1)
        ->and($updatedCart[0]['product_id'])->toBe($this->product2->id);
});

test('can clear entire cart', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->call('clearCart');

    expect(session('cart', []))->toBeEmpty();
});

test('order total is calculated correctly', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => 999.99,
            'quantity' => 2,
        ],
        [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'price' => 29.99,
            'quantity' => 3,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '+1234567890')
        ->call('submitOrder');

    $order = Order::where('customer_email', 'john@example.com')->first();

    expect($order->total)->toBe('2089.95');
});

test('redirects to order view after successful creation', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '+1234567890')
        ->call('submitOrder')
        ->assertRedirect();
});

test('clears cart after successful order submission', function () {
    $cart = [
        [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'price' => $this->product1->price,
            'quantity' => 1,
        ],
    ];

    session(['cart' => $cart]);

    Livewire::test(CreateOrder::class)
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '+1234567890')
        ->call('submitOrder');

    expect(session('cart', []))->toBeEmpty();
});
