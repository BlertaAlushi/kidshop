<?php

use App\Http\Controllers\Collections\ProductsController;
use App\Http\Controllers\Settings\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('/collection')->group(function(){
    Route::get('/all',[ProductsController::class, 'all'])->name('collection.all');
    Route::get('/type/{bodyPart:slug}',[ProductsController::class, 'filterByBodyPart'])->name('collection.types');
    Route::get('/mark/{mark:slug}',[ProductsController::class, 'filterByMark'])->name('collection.marks');
    Route::get('/skin-type/{skinType:slug}',[ProductsController::class, 'filterBySkinType'])->name('collection.skin.types');
    Route::get('/skin-concerns/{skinConcern:slug}',[ProductsController::class, 'filterBySkinConcern'])->name('collection.skin.concerns');
    Route::get('/product-type/{productType:slug}',[ProductsController::class, 'filterByProductType'])->name('collection.product.types');
    Route::get('/extra/{extra:slug}',[ProductsController::class, 'filterByExtra'])->name('collection.extras');
    Route::get('/product/{product:slug}',[ProductsController::class, 'product'])->name('collection.product');
});
