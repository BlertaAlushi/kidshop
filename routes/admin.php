<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ColorsController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\SizesController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')->middleware(['auth', 'isAdmin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/products/{product:slug}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::post('/products/{product}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');

    Route::get('/marks',[BrandController::class, 'index'])->name('marks.index');
    Route::get('/marks/create',[BrandController::class, 'create'])->name('marks.create');
    Route::post('/marks',[BrandController::class, 'store'])->name('marks.store');
    Route::get('/marks/{mark:slug}/edit', [BrandController::class, 'edit'])->name('marks.edit');
    Route::put('/marks/{mark}', [BrandController::class, 'update'])->name('marks.update');
    Route::delete('/marks/{mark}', [BrandController::class, 'destroy'])->name('marks.destroy');

    Route::get('/sizes',[SizesController::class, 'index'])->name('sizes.index');
    Route::get('/sizes/create',[SizesController::class, 'create'])->name('sizes.create');
    Route::post('/sizes',[SizesController::class, 'store'])->name('sizes.store');
    Route::get('/sizes/{size}/edit', [SizesController::class, 'edit'])->name('sizes.edit');
    Route::put('/sizes/{size}', [SizesController::class, 'update'])->name('sizes.update');
    Route::delete('/sizes/{size}', [SizesController::class, 'destroy'])->name('sizes.destroy');

    Route::get('/categories',[CategoriesController::class, 'index'])->name('categories.index');
    Route::get('/categories/create',[CategoriesController::class, 'create'])->name('categories.create');
    Route::post('/categories',[CategoriesController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category:slug}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoriesController::class, 'destroy'])->name('categories.destroy');

    Route::get('/colors',[ColorsController::class, 'index'])->name('colors.index');
    Route::get('/colors/create',[ColorsController::class, 'create'])->name('colors.create');
    Route::post('/colors',[ColorsController::class, 'store'])->name('colors.store');
    Route::get('/colors/{color}/edit', [ColorsController::class, 'edit'])->name('colors.edit');
    Route::put('/colors/{color}', [ColorsController::class, 'update'])->name('colors.update');
    Route::delete('/colors/{color}', [ColorsController::class, 'destroy'])->name('colors.destroy');

    Route::get('/countries',[CountriesController::class, 'index'])->name('countries.index');
    Route::get('/countries/create',[CountriesController::class, 'create'])->name('countries.create');
    Route::post('/countries',[CountriesController::class, 'store'])->name('countries.store');
    Route::get('/countries/{country}/edit', [CountriesController::class, 'edit'])->name('countries.edit');
    Route::put('/countries/{country}', [CountriesController::class, 'update'])->name('countries.update');
    Route::delete('/countries/{country}', [CountriesController::class, 'destroy'])->name('countries.destroy');

});
