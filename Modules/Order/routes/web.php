<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\Livewire\CreateOrder;
use Modules\Order\Livewire\ViewOrder;

// Public routes
Route::get('order/create', CreateOrder::class)->name('order.create');
Route::get('order/{id}', ViewOrder::class)->name('order.view');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('orders', OrderController::class)->names('order');
});
