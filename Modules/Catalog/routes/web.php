<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Livewire\ProductCatalog;

// Public routes
Route::get('catalog', ProductCatalog::class)->name('catalog.index');
