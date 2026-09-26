<?php

namespace App\Providers;

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ColorsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CountriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\SizesController;
use App\Interfaces\Services\LookupInterface;
use App\Services\CategoriesService;
use App\Services\ColorsService;
use App\Services\CountriesService;
use App\Services\MarksService;
use App\Services\Products\ProductsService;
use App\Services\SizesService;
use Illuminate\Support\ServiceProvider;

class LookupServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->when(BrandController::class)
            ->needs(LookupInterface::class)
            ->give(MarksService::class);

        $this->app->when(ProductsController::class)
            ->needs(LookupInterface::class)
            ->give(ProductsService::class);

        $this->app->when(SizesController::class)
            ->needs(LookupInterface::class)
            ->give(SizesService::class);

        $this->app->when(CategoriesController::class)
            ->needs(LookupInterface::class)
            ->give(CategoriesService::class);

        $this->app->when(ColorsController::class)
            ->needs(LookupInterface::class)
            ->give(ColorsService::class);

        $this->app->when(CountriesController::class)
            ->needs(LookupInterface::class)
            ->give(CountriesService::class);
    }
}
