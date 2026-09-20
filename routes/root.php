<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/' . config('app.fallback_locale'));
})->name('root');
