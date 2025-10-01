<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\CatalogController;
use Modules\Catalog\Livewire\ProductCatalog;

// Public routes
Route::get('catalog', ProductCatalog::class)->name('catalog.index');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('catalogs', CatalogController::class)->names('catalog');
});
