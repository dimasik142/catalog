<?php

use App\Contracts\Manager\OrderItemManagerInterface;
use App\Contracts\Manager\OrderManagerInterface;
use App\Contracts\Repository\OrderItemRepositoryInterface;
use App\Contracts\Repository\OrderRepositoryInterface;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

uses()->group('order', 'order-management');

beforeEach(function () {
    $this->orderManager = app(OrderManagerInterface::class);
    $this->orderRepository = app(OrderRepositoryInterface::class);
    $this->orderItemManager = app(OrderItemManagerInterface::class);
    $this->orderItemRepository = app(OrderItemRepositoryInterface::class);

    $this->category = Category::factory()->create();
    $this->product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 99.99,
    ]);
});

test('can create an order using OrderManager', function () {
    $customerData = [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+1234567890',
    ];

    $cartItems = [
        [
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'price' => $this->product->price,
            'quantity' => 2,
        ],
    ];

    $order = $this->orderManager->createOrder($customerData, $cartItems);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->customer_name)->toBe('John Doe')
        ->and($order->status)->toBe(Order::STATUS_PENDING)
        ->and($order->orderItems)->toHaveCount(1);

    $this->assertDatabaseHas('orders', [
        'customer_email' => 'john@example.com',
        'status' => Order::STATUS_PENDING,
    ]);
});

test('can update order status using OrderManager', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_PENDING,
    ]);

    $updated = $this->orderManager->updateStatus($order->id, Order::STATUS_CONFIRMED);

    expect($updated)->toBeTrue();

    $updatedOrder = $this->orderRepository->find($order->id);
    expect($updatedOrder->status)->toBe(Order::STATUS_CONFIRMED);
});

test('can update order using OrderManager', function () {
    $order = Order::factory()->create([
        'customer_name' => 'John Doe',
    ]);

    $updated = $this->orderManager->update($order->id, [
        'customer_name' => 'Jane Doe',
    ]);

    expect($updated)->toBeTrue();

    $updatedOrder = $this->orderRepository->find($order->id);
    expect($updatedOrder->customer_name)->toBe('Jane Doe');
});

test('can delete order using OrderManager', function () {
    $order = Order::factory()->create();
    $orderId = $order->id;

    $deleted = $this->orderManager->delete($orderId);

    expect($deleted)->toBeTrue();
    $this->assertDatabaseMissing('orders', ['id' => $orderId]);
});

test('order has many order items', function () {
    $order = Order::factory()->create();

    OrderItem::factory()->count(3)->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);

    expect($order->orderItems)->toHaveCount(3);
});

test('can create order item using OrderItemManager', function () {
    $order = Order::factory()->create();

    $orderItem = $this->orderItemManager->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'product_price' => $this->product->price,
        'quantity' => 2,
        'subtotal' => $this->product->price * 2,
    ]);

    expect($orderItem)->toBeInstanceOf(OrderItem::class)
        ->and($orderItem->quantity)->toBe(2);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);
});

test('order item stores product snapshot', function () {
    $order = Order::factory()->create();

    $originalProductName = $this->product->name;
    $originalProductPrice = $this->product->price;

    $orderItem = $this->orderItemManager->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'product_price' => $this->product->price,
        'quantity' => 2,
        'subtotal' => $this->product->price * 2,
    ]);

    expect($orderItem->product_name)->toBe($originalProductName)
        ->and($orderItem->product_price)->toBe('99.99');

    // Verify product snapshot is preserved even if product changes
    $this->product->update(['name' => 'Changed Name', 'price' => 199.99]);

    expect($orderItem->fresh()->product_name)->toBe($originalProductName)
        ->and($orderItem->fresh()->product_price)->toBe('99.99');
});

test('order total is cast to decimal', function () {
    $order = Order::factory()->create([
        'total' => 123.45,
    ]);

    expect($order->total)->toBe('123.45');
});

test('order statuses are available', function () {
    $statuses = Order::getStatuses();

    expect($statuses)->toHaveKeys([
        Order::STATUS_PENDING,
        Order::STATUS_CONFIRMED,
        Order::STATUS_SHIPPED,
        Order::STATUS_DELIVERED,
    ])->and($statuses[Order::STATUS_PENDING])->toBe('Pending')
        ->and($statuses[Order::STATUS_CONFIRMED])->toBe('Confirmed')
        ->and($statuses[Order::STATUS_SHIPPED])->toBe('Shipped')
        ->and($statuses[Order::STATUS_DELIVERED])->toBe('Delivered');
});

test('can calculate order total from items', function () {
    $order = Order::factory()->create([
        'total' => 0,
    ]);

    $this->orderItemManager->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'product_price' => 50.00,
        'quantity' => 2,
        'subtotal' => 100.00,
    ]);

    $this->orderItemManager->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'product_price' => 30.00,
        'quantity' => 3,
        'subtotal' => 90.00,
    ]);

    $order->calculateTotal();

    expect($order->fresh()->total)->toBe('190.00');
});

test('order item subtotal is calculated correctly', function () {
    $order = Order::factory()->create();

    $orderItem = $this->orderItemManager->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'product_price' => 25.50,
        'quantity' => 4,
        'subtotal' => 25.50 * 4,
    ]);

    expect($orderItem->subtotal)->toBe('102.00');
});

test('order workflow follows correct status progression using OrderManager', function () {
    $order = Order::factory()->create([
        'status' => Order::STATUS_PENDING,
    ]);

    // Pending -> Confirmed
    $this->orderManager->updateStatus($order->id, Order::STATUS_CONFIRMED);
    expect($this->orderRepository->find($order->id)->status)->toBe(Order::STATUS_CONFIRMED);

    // Confirmed -> Shipped
    $this->orderManager->updateStatus($order->id, Order::STATUS_SHIPPED);
    expect($this->orderRepository->find($order->id)->status)->toBe(Order::STATUS_SHIPPED);

    // Shipped -> Delivered
    $this->orderManager->updateStatus($order->id, Order::STATUS_DELIVERED);
    expect($this->orderRepository->find($order->id)->status)->toBe(Order::STATUS_DELIVERED);
});

test('order with items can be viewed', function () {
    $order = Order::factory()->create([
        'customer_name' => 'John Doe',
    ]);

    OrderItem::factory()->count(2)->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);

    $response = $this->get(route('order.view', ['id' => $order->id]));

    $response->assertStatus(200)
        ->assertSee('John Doe');
});

test('multiple orders can be created for same customer', function () {
    $email = 'john@example.com';

    Order::factory()->create([
        'customer_email' => $email,
        'total' => 100,
    ]);

    Order::factory()->create([
        'customer_email' => $email,
        'total' => 200,
    ]);

    $orders = Order::where('customer_email', $email)->get();

    expect($orders)->toHaveCount(2);
});

test('order stores complete customer information', function () {
    $customerData = [
        'customer_name' => 'Jane Smith',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '+9876543210',
    ];

    $cartItems = [
        [
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'price' => 500.00,
            'quantity' => 1,
        ],
    ];

    $order = $this->orderManager->createOrder($customerData, $cartItems);

    expect($order->customer_name)->toBe('Jane Smith')
        ->and($order->customer_email)->toBe('jane@example.com')
        ->and($order->customer_phone)->toBe('+9876543210');
});

// OrderRepository Tests
test('OrderRepository can find order by id', function () {
    $order = Order::factory()->create();

    $found = $this->orderRepository->find($order->id);

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($order->id);
});

test('OrderRepository can find order with items', function () {
    $order = Order::factory()->create();
    OrderItem::factory()->count(3)->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);

    $found = $this->orderRepository->findWithItems($order->id);

    expect($found)->not->toBeNull()
        ->and($found->orderItems)->toHaveCount(3);
});

test('OrderRepository can get orders by status', function () {
    Order::factory()->count(3)->create(['status' => Order::STATUS_PENDING]);
    Order::factory()->count(2)->create(['status' => Order::STATUS_CONFIRMED]);

    $pending = $this->orderRepository->getByStatus(Order::STATUS_PENDING);

    expect($pending)->toHaveCount(3);
});

test('OrderRepository can get orders by customer email', function () {
    $email = 'john@example.com';

    Order::factory()->count(2)->create(['customer_email' => $email]);
    Order::factory()->create(['customer_email' => 'other@example.com']);

    $orders = $this->orderRepository->getByCustomerEmail($email);

    expect($orders)->toHaveCount(2);
});

// OrderItemRepository Tests
test('OrderItemRepository can find order item by id', function () {
    $order = Order::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);

    $found = $this->orderItemRepository->find($orderItem->id);

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($orderItem->id);
});

test('OrderItemRepository can get order items by order id', function () {
    $order = Order::factory()->create();
    OrderItem::factory()->count(3)->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
    ]);

    $items = $this->orderItemRepository->getByOrderId($order->id);

    expect($items)->toHaveCount(3);
});
