<?php

use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update/{cartProduct}', [CartController::class, 'updateCartProduct'])->name('cart.update');
    Route::delete('/cart/remove/{cartProduct}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/order', [OrderController::class, 'order'])->name('order');
});
