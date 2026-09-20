<?php

use App\Http\Controllers\Admin\BodyPartsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExtrasController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MarksController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ProductTypesController;
use App\Http\Controllers\Admin\SkinConcernsController;
use App\Http\Controllers\Admin\SkinTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')->middleware(['auth', 'isAdmin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/products/{product:slug}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::post('/products/{product}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('products.destroy');

    Route::get('/body-parts', [BodyPartsController::class, 'index'])->name('body-parts.index');
    Route::get('/body-parts/create', [BodyPartsController::class, 'create'])->name('body-parts.create');
    Route::post('/body-parts', [BodyPartsController::class, 'store'])->name('body-parts.store');
    Route::get('/body-parts/{bodyPart:slug}/edit', [BodyPartsController::class, 'edit'])->name('body-parts.edit');
    Route::put('/body-parts/{bodyPart}', [BodyPartsController::class, 'update'])->name('body-parts.update');
    Route::delete('/body-parts/{bodyPart}', [BodyPartsController::class, 'destroy'])->name('body-parts.destroy');

    Route::get('/skin-types',[SkinTypeController::class, 'index'])->name('skin-types.index');
    Route::get('/skin-types/create',[SkinTypeController::class, 'create'])->name('skin-types.create');
    Route::post('/skin-types',[SkinTypeController::class, 'store'])->name('skin-types.store');
    Route::get('/skin-types/{skinType:slug}/edit',[SkinTypeController::class, 'edit'])->name('skin-types.edit');
    Route::put('/skin-types/{skinType}',[SkinTypeController::class, 'update'])->name('skin-types.update');
    Route::delete('/skin-types/{skinType}', [SkinTypeController::class, 'destroy'])->name('skin-types.destroy');

    Route::get('/skin-concerns',[SkinConcernsController::class, 'index'])->name('skin-concerns.index');
    Route::get('/skin-concerns/create',[SkinConcernsController::class, 'create'])->name('skin-concerns.create');
    Route::post('/skin-concerns',[SkinConcernsController::class, 'store'])->name('skin-concerns.store');
    Route::get('/skin-concerns/{skinConcern:slug}/edit}', [SkinConcernsController::class, 'edit'])->name('skin-concerns.edit');
    Route::put('/skin-concerns/{skinConcern}', [SkinConcernsController::class, 'update'])->name('skin-concerns.update');
    Route::delete('/skin-concerns/{skinConcern}',[SkinConcernsController::class, 'destroy'])->name('skin-concerns.destroy');

    Route::get('/product-types',[ProductTypesController::class, 'index'])->name('product-types.index');
    Route::get('/product-types/create',[ProductTypesController::class, 'create'])->name('product-types.create');
    Route::post('/product-types',[ProductTypesController::class, 'store'])->name('product-types.store');
    Route::get('/product-types/{productType:slug}/edit', [ProductTypesController::class, 'edit'])->name('product-types.edit');
    Route::put('/product-types/{productType}', [ProductTypesController::class, 'update'])->name('product-types.update');
    Route::delete('/product-types/{productType}', [ProductTypesController::class, 'destroy'])->name('product-types.destroy');

    Route::get('/extras',[ExtrasController::class, 'index'])->name('extras.index');
    Route::get('/extras/create',[ExtrasController::class, 'create'])->name('extras.create');
    Route::post('/extras',[ExtrasController::class, 'store'])->name('extras.store');
    Route::get('/extras/{extra:slug}/edit', [ExtrasController::class, 'edit'])->name('extras.edit');
    Route::put('/extras/{extra}', [ExtrasController::class, 'update'])->name('extras.update');
    Route::delete('/extras/{extra}', [ExtrasController::class, 'destroy'])->name('extras.destroy');

    Route::get('/marks',[MarksController::class, 'index'])->name('marks.index');
    Route::get('/marks/create',[MarksController::class, 'create'])->name('marks.create');
    Route::post('/marks',[MarksController::class, 'store'])->name('marks.store');
    Route::get('/marks/{mark:slug}/edit', [MarksController::class, 'edit'])->name('marks.edit');
    Route::put('/marks/{mark}', [MarksController::class, 'update'])->name('marks.update');
    Route::delete('/marks/{mark}', [MarksController::class, 'destroy'])->name('marks.destroy');

    Route::get('/languages',[LanguageController::class, 'index'])->name('languages.index');
    Route::get('/language',[LanguageController::class, 'create'])->name('languages.create');
    Route::post('/language',[LanguageController::class, 'store'])->name('languages.store');
    Route::get('/language/{language:code}/edit', [LanguageController::class, 'edit'])->name('languages.edit');
    Route::put('/language/{language}', [LanguageController::class, 'update'])->name('languages.update');
    Route::delete('/language/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');

});
