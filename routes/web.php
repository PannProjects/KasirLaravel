<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return redirect()->route('orders.create');
});

Route::resource('products', ProductController::class);
Route::resource('discounts', DiscountController::class);
Route::resource('orders', OrderController::class)->except(['edit', 'update', 'destroy']);
Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
