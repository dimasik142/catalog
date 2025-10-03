<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Livewire\CreateOrder;
use Modules\Order\Livewire\ViewOrder;

// Public routes
Route::get('order/create', CreateOrder::class)->name('order.create');
Route::get('order/{id}', ViewOrder::class)->name('order.view');
