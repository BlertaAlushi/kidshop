<?php

use App\Http\Controllers\Collections\ProductsController;
use App\Http\Controllers\Settings\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('/collection')->group(function(){
    Route::get('/all',[ProductsController::class, 'all'])->name('collection.all');
    Route::get('/category/{category:slug}',[ProductsController::class, 'filterByCategory'])->name('collection.category');
    Route::get('/mark/{mark:slug}',[ProductsController::class, 'filterByMark'])->name('collection.marks');
    Route::get('/season/{season:slug}',[ProductsController::class, 'filterBySeason'])->name('collection.season');
    Route::get('/gender/{gender}',[ProductsController::class, 'filterByGender'])
        ->where('gender', 'boy|girl|unisex')
        ->name('collection.gender');
    Route::get('/product/{product:slug}',[ProductsController::class, 'product'])->name('collection.product');
});
