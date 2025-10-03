<?php

use Livewire\Livewire;
use Modules\Catalog\Livewire\ProductCatalog;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

uses()->group('catalog', 'product-display');

beforeEach(function () {
    $this->category1 = Category::factory()->create(['name' => 'Electronics']);
    $this->category2 = Category::factory()->create(['name' => 'Books']);
});

test('product catalog page loads successfully', function () {
    $response = $this->get(route('catalog.index'));

    $response->assertStatus(200);
});

test('displays products in catalog', function () {
    $product1 = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Laptop',
        'price' => 999.99,
        'stock' => 5,
    ]);

    $product2 = Product::factory()->create([
        'category_id' => $this->category2->id,
        'name' => 'Book',
        'price' => 19.99,
        'stock' => 100,
    ]);

    Livewire::test(ProductCatalog::class)
        ->assertSee('Laptop')
        ->assertSee('Book')
        ->assertSee('999.99')
        ->assertSee('19.99');
});

test('can filter products by category', function () {
    $electronicsProduct = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Laptop',
    ]);

    $bookProduct = Product::factory()->create([
        'category_id' => $this->category2->id,
        'name' => 'Book',
    ]);

    Livewire::test(ProductCatalog::class)
        ->set('selectedCategoryId', $this->category1->id)
        ->assertViewHas('products', function ($products) use ($electronicsProduct, $bookProduct) {
            return $products->contains('id', $electronicsProduct->id)
                && ! $products->contains('id', $bookProduct->id);
        });
});

test('can search products by name', function () {
    $laptop = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Gaming Laptop',
    ]);

    $desk = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Office Desk',
    ]);

    Livewire::test(ProductCatalog::class)
        ->set('search', 'Laptop')
        ->assertViewHas('products', function ($products) use ($laptop, $desk) {
            return $products->contains('id', $laptop->id)
                && ! $products->contains('id', $desk->id);
        });
});

test('can search products by description', function () {
    $productA = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Product A',
        'description' => 'Contains special keyword',
    ]);

    $productB = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Product B',
        'description' => 'Regular description',
    ]);

    Livewire::test(ProductCatalog::class)
        ->set('search', 'special')
        ->assertViewHas('products', function ($products) use ($productA, $productB) {
            return $products->contains('id', $productA->id)
                && ! $products->contains('id', $productB->id);
        });
});

test('can clear filters', function () {
    Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Laptop',
    ]);

    Product::factory()->create([
        'category_id' => $this->category2->id,
        'name' => 'Book',
    ]);

    Livewire::test(ProductCatalog::class)
        ->set('selectedCategoryId', $this->category1->id)
        ->set('search', 'test')
        ->assertSet('selectedCategoryId', $this->category1->id)
        ->assertSet('search', 'test')
        ->call('clearFilters')
        ->assertSet('selectedCategoryId', null)
        ->assertSet('search', '');
});

test('can add product to cart', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Laptop',
        'price' => 999.99,
        'stock' => 5,
    ]);

    Livewire::test(ProductCatalog::class)
        ->call('addToCart', $product->id);

    expect(session('cart'))->toHaveKey($product->id)
        ->and(session('cart')[$product->id]['quantity'])->toBe(1);
});

test('cannot add out of stock product to cart', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category1->id,
        'name' => 'Out of Stock Item',
        'stock' => 0,
    ]);

    Livewire::test(ProductCatalog::class)
        ->call('addToCart', $product->id);

    expect(session('cart', []))->toBeEmpty();
});

test('cannot add more items than available stock', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category1->id,
        'stock' => 2,
    ]);

    // Add product to cart twice to reach stock limit
    $component = Livewire::test(ProductCatalog::class)
        ->call('addToCart', $product->id)
        ->call('addToCart', $product->id);

    // Verify cart has 2 items
    expect(session('cart')[$product->id]['quantity'])->toBe(2);

    // Try to add one more (should fail)
    $component->call('addToCart', $product->id);

    // Verify quantity is still 2 (didn't increase)
    expect(session('cart')[$product->id]['quantity'])->toBe(2);
});

test('products are paginated', function () {
    Product::factory()->count(15)->create([
        'category_id' => $this->category1->id,
    ]);

    Livewire::test(ProductCatalog::class)
        ->assertViewHas('products', function ($products) {
            return $products->count() === 12; // Default per page
        });
});

test('displays categories with product count', function () {
    Product::factory()->count(5)->create([
        'category_id' => $this->category1->id,
    ]);

    Product::factory()->count(3)->create([
        'category_id' => $this->category2->id,
    ]);

    Livewire::test(ProductCatalog::class)
        ->assertViewHas('categories', function ($categories) {
            $electronics = $categories->firstWhere('id', $this->category1->id);
            $books = $categories->firstWhere('id', $this->category2->id);

            return $electronics->products_count === 5
                && $books->products_count === 3;
        });
});
