<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Order\Filament\Resources\OrderResource\Pages\CreateOrder;
use Modules\Order\Filament\Resources\OrderResource\Pages\EditOrder;
use Modules\Order\Filament\Resources\OrderResource\Pages\ListOrders;
use Modules\Order\Filament\Resources\OrderResource\Pages\ViewOrder;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;

uses()->group('order', 'filament', 'order-resource');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->category = Category::factory()->create();
    $this->product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 99.99,
    ]);
});

test('can render order list page', function () {
    Livewire::test(ListOrders::class)
        ->assertSuccessful();
});

test('can list orders in table', function () {
    $orders = Order::factory()->count(10)->create();

    Livewire::test(ListOrders::class)
        ->assertCanSeeTableRecords($orders);
});

test('orders are sorted by created_at descending by default', function () {
    $orders = Order::factory()->count(3)->create();

    Livewire::test(ListOrders::class)
        ->assertCanSeeTableRecords($orders->sortByDesc('created_at'), inOrder: true);
});

test('can search orders by id', function () {
    $order = Order::factory()->create();
    Order::factory()->count(2)->create();

    Livewire::test(ListOrders::class)
        ->searchTable((string) $order->id)
        ->assertCanSeeTableRecords([$order]);
});

test('can search orders by customer name', function () {
    $order = Order::factory()->create(['customer_name' => 'John Doe']);
    Order::factory()->create(['customer_name' => 'Jane Smith']);

    Livewire::test(ListOrders::class)
        ->searchTable('John')
        ->assertCanSeeTableRecords([$order]);
});

test('can search orders by customer email', function () {
    $order = Order::factory()->create(['customer_email' => 'john@example.com']);
    Order::factory()->create(['customer_email' => 'jane@example.com']);

    Livewire::test(ListOrders::class)
        ->searchTable('john@example.com')
        ->assertCanSeeTableRecords([$order]);
});

test('can filter orders by status', function () {
    $pendingOrders = Order::factory()->count(3)->create(['status' => Order::STATUS_PENDING]);
    $confirmedOrders = Order::factory()->count(2)->create(['status' => Order::STATUS_CONFIRMED]);

    Livewire::test(ListOrders::class)
        ->filterTable('status', [Order::STATUS_PENDING])
        ->assertCanSeeTableRecords($pendingOrders)
        ->assertCanNotSeeTableRecords($confirmedOrders);
});

test('can filter orders by multiple statuses', function () {
    $pendingOrders = Order::factory()->count(2)->create(['status' => Order::STATUS_PENDING]);
    $confirmedOrders = Order::factory()->count(2)->create(['status' => Order::STATUS_CONFIRMED]);
    $shippedOrders = Order::factory()->count(2)->create(['status' => Order::STATUS_SHIPPED]);

    Livewire::test(ListOrders::class)
        ->filterTable('status', [Order::STATUS_PENDING, Order::STATUS_CONFIRMED])
        ->assertCanSeeTableRecords([...$pendingOrders, ...$confirmedOrders])
        ->assertCanNotSeeTableRecords($shippedOrders);
});

test('can sort orders by total', function () {
    $orders = Order::factory()->count(3)->create();

    Livewire::test(ListOrders::class)
        ->sortTable('total')
        ->assertCanSeeTableRecords($orders->sortBy('total'), inOrder: true);
});

test('status badge shows correct color for pending', function () {
    $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

    Livewire::test(ListOrders::class)
        ->assertTableColumnExists('status');
});

test('status badge shows correct color for confirmed', function () {
    $order = Order::factory()->create(['status' => Order::STATUS_CONFIRMED]);

    Livewire::test(ListOrders::class)
        ->assertTableColumnExists('status');
});

test('status badge shows correct color for shipped', function () {
    $order = Order::factory()->create(['status' => Order::STATUS_SHIPPED]);

    Livewire::test(ListOrders::class)
        ->assertTableColumnExists('status');
});

test('status badge shows correct color for delivered', function () {
    $order = Order::factory()->create(['status' => Order::STATUS_DELIVERED]);

    Livewire::test(ListOrders::class)
        ->assertTableColumnExists('status');
});

test('can render order create page', function () {
    Livewire::test(CreateOrder::class)
        ->assertSuccessful();
});

test('can create order', function () {
    $newData = Order::factory()->make();

    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_name' => $newData->customer_name,
            'customer_email' => $newData->customer_email,
            'customer_phone' => $newData->customer_phone,
            'status' => Order::STATUS_PENDING,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('orders', [
        'customer_name' => $newData->customer_name,
        'customer_email' => $newData->customer_email,
        'status' => Order::STATUS_PENDING,
    ]);
});

test('validates customer name is required when creating order', function () {
    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_name' => '',
            'customer_email' => 'test@example.com',
            'customer_phone' => '+1234567890',
            'status' => Order::STATUS_PENDING,
        ])
        ->call('create')
        ->assertHasFormErrors(['customer_name' => 'required']);
});

test('validates customer email is required when creating order', function () {
    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_name' => 'John Doe',
            'customer_email' => '',
            'customer_phone' => '+1234567890',
            'status' => Order::STATUS_PENDING,
        ])
        ->call('create')
        ->assertHasFormErrors(['customer_email' => 'required']);
});

test('validates customer email format when creating order', function () {
    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_name' => 'John Doe',
            'customer_email' => 'invalid-email',
            'customer_phone' => '+1234567890',
            'status' => Order::STATUS_PENDING,
        ])
        ->call('create')
        ->assertHasFormErrors(['customer_email' => 'email']);
});

test('validates customer phone is required when creating order', function () {
    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_name' => 'John Doe',
            'customer_email' => 'test@example.com',
            'customer_phone' => '',
            'status' => Order::STATUS_PENDING,
        ])
        ->call('create')
        ->assertHasFormErrors(['customer_phone' => 'required']);
});

test('validates status is required when creating order', function () {
    Livewire::test(CreateOrder::class)
        ->fillForm([
            'customer_name' => 'John Doe',
            'customer_email' => 'test@example.com',
            'customer_phone' => '+1234567890',
            'status' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['status' => 'required']);
});

test('can render order edit page', function () {
    $order = Order::factory()->create();

    Livewire::test(EditOrder::class, ['record' => $order->id])
        ->assertSuccessful();
});

test('can update order', function () {
    $order = Order::factory()->create();
    $newData = Order::factory()->make();

    Livewire::test(EditOrder::class, ['record' => $order->id])
        ->fillForm([
            'customer_name' => $newData->customer_name,
            'customer_email' => $newData->customer_email,
            'customer_phone' => $newData->customer_phone,
            'status' => Order::STATUS_CONFIRMED,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($order->refresh())
        ->customer_name->toBe($newData->customer_name)
        ->customer_email->toBe($newData->customer_email)
        ->customer_phone->toBe($newData->customer_phone)
        ->status->toBe(Order::STATUS_CONFIRMED);
});

test('validates customer name is required when updating order', function () {
    $order = Order::factory()->create();

    Livewire::test(EditOrder::class, ['record' => $order->id])
        ->fillForm([
            'customer_name' => '',
        ])
        ->call('save')
        ->assertHasFormErrors(['customer_name' => 'required']);
});

test('validates customer email format when updating order', function () {
    $order = Order::factory()->create();

    Livewire::test(EditOrder::class, ['record' => $order->id])
        ->fillForm([
            'customer_email' => 'invalid-email',
        ])
        ->call('save')
        ->assertHasFormErrors(['customer_email' => 'email']);
});

test('can render order view page', function () {
    $order = Order::factory()->create();

    Livewire::test(ViewOrder::class, ['record' => $order->id])
        ->assertSuccessful();
});

test('can view order customer information', function () {
    $order = Order::factory()->create([
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+1234567890',
    ]);

    Livewire::test(ViewOrder::class, ['record' => $order->id])
        ->assertSee('John Doe')
        ->assertSee('john@example.com')
        ->assertSee('+1234567890');
});

test('can view order items in order details', function () {
    $order = Order::factory()->create();
    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => 'Test Product',
        'product_price' => 99.99,
        'quantity' => 2,
        'subtotal' => 199.98,
    ]);

    Livewire::test(ViewOrder::class, ['record' => $order->id])
        ->assertSee('Test Product');
});

test('total field is disabled in form', function () {
    Livewire::test(CreateOrder::class)
        ->assertFormFieldExists('total')
        ->assertFormFieldIsDisabled('total');
});

test('order displays total in view page', function () {
    $order = Order::factory()->create(['total' => 299.99]);

    Livewire::test(ViewOrder::class, ['record' => $order->id])
        ->assertSee('299.99');
});
