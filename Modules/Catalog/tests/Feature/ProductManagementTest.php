<?php

use App\Contracts\Manager\CategoryManagerInterface;
use App\Contracts\Manager\ProductManagerInterface;
use App\Contracts\Repository\CategoryRepositoryInterface;
use App\Contracts\Repository\ProductRepositoryInterface;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

uses()->group('catalog', 'product-management');

beforeEach(function () {
    $this->categoryManager = app(CategoryManagerInterface::class);
    $this->categoryRepository = app(CategoryRepositoryInterface::class);
    $this->productManager = app(ProductManagerInterface::class);
    $this->productRepository = app(ProductRepositoryInterface::class);

    $this->category = Category::factory()->create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);
});

test('can create a product using ProductManager', function () {
    $productData = [
        'category_id' => $this->category->id,
        'name' => 'Laptop',
        'description' => 'High-performance laptop',
        'price' => 999.99,
        'stock' => 10,
    ];

    $product = $this->productManager->create($productData);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('Laptop')
        ->and($product->price)->toBe('999.99')
        ->and($product->stock)->toBe(10);

    $this->assertDatabaseHas('products', [
        'name' => 'Laptop',
        'category_id' => $this->category->id,
    ]);
});

test('can update a product using ProductManager', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Old Name',
        'price' => 100,
    ]);

    $updated = $this->productManager->update($product->id, [
        'name' => 'New Name',
        'price' => 150,
    ]);

    expect($updated)->toBeTrue();

    $updatedProduct = $this->productRepository->find($product->id);
    expect($updatedProduct->name)->toBe('New Name')
        ->and($updatedProduct->price)->toBe('150.00');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'New Name',
        'price' => 150,
    ]);
});

test('can delete a product using ProductManager', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
    ]);

    $productId = $product->id;
    $deleted = $this->productManager->delete($productId);

    expect($deleted)->toBeTrue();

    $this->assertDatabaseMissing('products', [
        'id' => $productId,
    ]);
});

test('product belongs to a category', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
    ]);

    expect($product->category)->toBeInstanceOf(Category::class)
        ->and($product->category->id)->toBe($this->category->id);
});

test('can update product stock using ProductManager', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'stock' => 50,
    ]);

    $updated = $this->productManager->updateStock($product->id, 25);

    expect($updated)->toBeTrue();

    $updatedProduct = $this->productRepository->find($product->id);
    expect($updatedProduct->stock)->toBe(25);
});

test('product price is cast to decimal', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'price' => 19.99,
    ]);

    expect($product->price)->toBe('19.99');
});

// ProductRepository Tests
test('ProductRepository can find product by id', function () {
    $product = Product::factory()->create([
        'category_id' => $this->category->id,
    ]);

    $found = $this->productRepository->find($product->id);

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($product->id);
});

test('ProductRepository can get paginated products', function () {
    Product::factory()->count(15)->create([
        'category_id' => $this->category->id,
    ]);

    $paginated = $this->productRepository->getPaginated(perPage: 10);

    expect($paginated->total())->toBe(15)
        ->and($paginated->perPage())->toBe(10)
        ->and($paginated->count())->toBe(10);
});

test('ProductRepository can filter by category', function () {
    $category2 = Category::factory()->create();

    Product::factory()->count(3)->create(['category_id' => $this->category->id]);
    Product::factory()->count(2)->create(['category_id' => $category2->id]);

    $paginated = $this->productRepository->getPaginated(categoryId: $this->category->id);

    expect($paginated->total())->toBe(3);
});

test('ProductRepository can search products', function () {
    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Gaming Laptop',
    ]);
    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Office Desk',
    ]);

    $results = $this->productRepository->search('Laptop');

    expect($results)->toHaveCount(1)
        ->and($results[0]['name'])->toBe('Gaming Laptop');
});

test('ProductRepository can find many products', function () {
    $product1 = Product::factory()->create(['category_id' => $this->category->id]);
    $product2 = Product::factory()->create(['category_id' => $this->category->id]);

    $results = $this->productRepository->findMany([$product1->id, $product2->id]);

    expect($results)->toHaveCount(2)
        ->and($results)->toHaveKeys([$product1->id, $product2->id]);
});

test('ProductRepository can get products by category id', function () {
    $category2 = Category::factory()->create();

    Product::factory()->count(3)->create(['category_id' => $this->category->id]);
    Product::factory()->count(2)->create(['category_id' => $category2->id]);

    $products = $this->productRepository->getByCategoryId($this->category->id);

    expect($products)->toHaveCount(3);
});

// CategoryManager Tests
test('CategoryManager can create category', function () {
    $category = $this->categoryManager->create([
        'name' => 'Books',
        'slug' => 'books',
        'description' => 'Book category',
    ]);

    expect($category)->not->toBeNull()
        ->and($category->name)->toBe('Books');

    $this->assertDatabaseHas('categories', ['name' => 'Books']);
});

test('CategoryManager can update category', function () {
    $updated = $this->categoryManager->update($this->category->id, [
        'name' => 'Updated Electronics',
    ]);

    expect($updated)->toBeTrue();

    $category = $this->categoryRepository->find($this->category->id);
    expect($category->name)->toBe('Updated Electronics');
});

test('CategoryManager can delete category', function () {
    $categoryId = $this->category->id;
    $deleted = $this->categoryManager->delete($categoryId);

    expect($deleted)->toBeTrue();
    $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
});

// CategoryRepository Tests
test('CategoryRepository can get all categories with product count', function () {
    Product::factory()->count(5)->create(['category_id' => $this->category->id]);

    $categories = $this->categoryRepository->getAllWithProductCount();

    $found = $categories->firstWhere('id', $this->category->id);
    expect($found->products_count)->toBe(5);
});

test('CategoryRepository can find category by id', function () {
    $found = $this->categoryRepository->find($this->category->id);

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($this->category->id);
});

test('CategoryRepository can find category by slug', function () {
    $found = $this->categoryRepository->findBySlug('electronics');

    expect($found)->not->toBeNull()
        ->and($found->slug)->toBe('electronics');
});
