<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Catalog\Filament\Resources\CategoryResource\Pages\CreateCategory;
use Modules\Catalog\Filament\Resources\CategoryResource\Pages\EditCategory;
use Modules\Catalog\Filament\Resources\CategoryResource\Pages\ListCategories;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

uses()->group('catalog', 'filament', 'category-resource');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render category list page', function () {
    Livewire::test(ListCategories::class)
        ->assertSuccessful();
});

test('can list categories in table', function () {
    $categories = Category::factory()->count(10)->create();

    Livewire::test(ListCategories::class)
        ->assertCanSeeTableRecords($categories);
});

test('can display category products count in table', function () {
    $category = Category::factory()->create();
    Product::factory()->count(5)->create(['category_id' => $category->id]);

    Livewire::test(ListCategories::class)
        ->assertTableColumnExists('products_count');
});

test('can search categories by name', function () {
    $category = Category::factory()->create(['name' => 'Electronics']);
    Category::factory()->create(['name' => 'Books']);

    Livewire::test(ListCategories::class)
        ->searchTable('Electronics')
        ->assertCanSeeTableRecords([$category]);
});

test('can search categories by slug', function () {
    $category = Category::factory()->create(['slug' => 'electronics-123']);
    Category::factory()->create(['slug' => 'books-456']);

    Livewire::test(ListCategories::class)
        ->searchTable('electronics')
        ->assertCanSeeTableRecords([$category]);
});

test('can sort categories by name', function () {
    $categories = Category::factory()->count(3)->create();

    Livewire::test(ListCategories::class)
        ->sortTable('name')
        ->assertCanSeeTableRecords($categories->sortBy('name'), inOrder: true)
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords($categories->sortByDesc('name'), inOrder: true);
});

test('can render category create page', function () {
    Livewire::test(CreateCategory::class)
        ->assertSuccessful();
});

test('can create category', function () {
    $newData = Category::factory()->make();

    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => $newData->name,
            'slug' => $newData->slug,
            'description' => $newData->description,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('categories', [
        'name' => $newData->name,
        'slug' => $newData->slug,
    ]);
});

test('validates category name is required when creating', function () {
    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => '',
            'slug' => 'test-slug',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

test('validates category slug is required when creating', function () {
    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => 'Test Category',
            'slug' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'required']);
});

test('validates category slug is unique when creating', function () {
    $category = Category::factory()->create();

    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => 'Another Category',
            'slug' => $category->slug,
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

test('can render category edit page', function () {
    $category = Category::factory()->create();

    Livewire::test(EditCategory::class, ['record' => $category->id])
        ->assertSuccessful();
});

test('can update category', function () {
    $category = Category::factory()->create();
    $newData = Category::factory()->make();

    Livewire::test(EditCategory::class, ['record' => $category->id])
        ->fillForm([
            'name' => $newData->name,
            'slug' => $newData->slug,
            'description' => $newData->description,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($category->refresh())
        ->name->toBe($newData->name)
        ->slug->toBe($newData->slug)
        ->description->toBe($newData->description);
});

test('validates category name is required when updating', function () {
    $category = Category::factory()->create();

    Livewire::test(EditCategory::class, ['record' => $category->id])
        ->fillForm([
            'name' => '',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

test('validates category slug is unique when updating', function () {
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();

    Livewire::test(EditCategory::class, ['record' => $category1->id])
        ->fillForm([
            'slug' => $category2->slug,
        ])
        ->call('save')
        ->assertHasFormErrors(['slug' => 'unique']);
});
