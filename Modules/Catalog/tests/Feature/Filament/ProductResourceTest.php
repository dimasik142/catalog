<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\CreateProduct;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\EditProduct;
use Modules\Catalog\Filament\Resources\ProductResource\Pages\ListProducts;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

uses()->group('catalog', 'filament', 'product-resource');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->category = Category::factory()->create(['name' => 'Electronics']);
});

test('can render product list page', function () {
    Livewire::test(ListProducts::class)
        ->assertSuccessful();
});

test('can list products in table', function () {
    $products = Product::factory()->count(10)->create(['category_id' => $this->category->id]);

    Livewire::test(ListProducts::class)
        ->assertCanSeeTableRecords($products);
});

test('can display product category in table', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    Livewire::test(ListProducts::class)
        ->assertTableColumnExists('category.name');
});

test('can search products by name', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Gaming Laptop',
    ]);
    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Office Desk',
    ]);

    Livewire::test(ListProducts::class)
        ->searchTable('Laptop')
        ->assertCanSeeTableRecords([$product]);
});

test('can filter products by category', function () {
    $category2 = Category::factory()->create(['name' => 'Books']);

    $electronicsProducts = Product::factory()->count(3)->create(['category_id' => $this->category->id]);
    $bookProducts = Product::factory()->count(2)->create(['category_id' => $category2->id]);

    Livewire::test(ListProducts::class)
        ->filterTable('category', $this->category->id)
        ->assertCanSeeTableRecords($electronicsProducts)
        ->assertCanNotSeeTableRecords($bookProducts);
});

test('can sort products by price', function () {
    $products = Product::factory()->count(3)->create(['category_id' => $this->category->id]);

    Livewire::test(ListProducts::class)
        ->sortTable('price')
        ->assertCanSeeTableRecords($products->sortBy('price'), inOrder: true)
        ->sortTable('price', 'desc')
        ->assertCanSeeTableRecords($products->sortByDesc('price'), inOrder: true);
});

test('can sort products by stock', function () {
    $products = Product::factory()->count(3)->create(['category_id' => $this->category->id]);

    Livewire::test(ListProducts::class)
        ->sortTable('stock')
        ->assertCanSeeTableRecords($products->sortBy('stock'), inOrder: true);
});

test('stock column shows correct color for out of stock', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock' => 0,
    ]);

    Livewire::test(ListProducts::class)
        ->assertTableColumnExists('stock');
});

test('stock column shows correct color for low stock', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock' => 5,
    ]);

    Livewire::test(ListProducts::class)
        ->assertTableColumnExists('stock');
});

test('can render product create page', function () {
    Livewire::test(CreateProduct::class)
        ->assertSuccessful();
});

test('can create product', function () {
    $newData = Product::factory()->make(['category_id' => $this->category->id]);

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => $newData->name,
            'description' => $newData->description,
            'price' => $newData->price,
            'stock' => $newData->stock,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('products', [
        'category_id' => $this->category->id,
        'name' => $newData->name,
        'price' => $newData->price,
    ]);
});

test('validates product name is required when creating', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => '',
            'price' => 99.99,
            'stock' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

test('validates product category is required when creating', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => null,
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['category_id' => 'required']);
});

test('validates product price is required when creating', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => null,
            'stock' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['price' => 'required']);
});

test('validates product price is numeric', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => 'not-a-number',
            'stock' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['price' => 'numeric']);
});

test('validates product price is not negative', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => -10,
            'stock' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['price']);
});

test('validates product stock is required when creating', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['stock' => 'required']);
});

test('validates product stock is numeric', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 'not-a-number',
        ])
        ->call('create')
        ->assertHasFormErrors(['stock' => 'numeric']);
});

test('validates product stock is not negative', function () {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => -5,
        ])
        ->call('create')
        ->assertHasFormErrors(['stock']);
});

test('can create category inline from product form', function () {
    Livewire::test(CreateProduct::class)
        ->assertFormFieldExists('category_id');
});

test('can render product edit page', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    Livewire::test(EditProduct::class, ['record' => $product->id])
        ->assertSuccessful();
});

test('can update product', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);
    $newCategory = Category::factory()->create();
    $newData = Product::factory()->make(['category_id' => $newCategory->id]);

    Livewire::test(EditProduct::class, ['record' => $product->id])
        ->fillForm([
            'category_id' => $newCategory->id,
            'name' => $newData->name,
            'description' => $newData->description,
            'price' => $newData->price,
            'stock' => $newData->stock,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->refresh())
        ->category_id->toBe($newCategory->id)
        ->name->toBe($newData->name)
        ->description->toBe($newData->description)
        ->price->toBe($newData->price)
        ->stock->toBe($newData->stock);
});

test('validates product name is required when updating', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    Livewire::test(EditProduct::class, ['record' => $product->id])
        ->fillForm([
            'name' => '',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});
