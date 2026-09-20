<?php

namespace App\Providers;

use App\Http\Controllers\Admin\BodyPartsController;
use App\Http\Controllers\Admin\ExtrasController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MarksController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ProductTypesController;
use App\Http\Controllers\Admin\SkinConcernsController;
use App\Http\Controllers\Admin\SkinTypeController;
use App\Interfaces\Services\LookupInterface;
use App\Services\BodyPartsService;
use App\Services\ExtrasService;
use App\Services\LanguageService;
use App\Services\MarksService;
use App\Services\Products\ProductsService;
use App\Services\ProductTypesService;
use App\Services\SkinConcernsService;
use App\Services\SkinTypesService;
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
        $this->app->when(BodyPartsController::class)
            ->needs(LookupInterface::class)
            ->give(BodyPartsService::class);

        $this->app->when(SkinTypeController::class)
            ->needs(LookupInterface::class)
            ->give(SkinTypesService::class);

        $this->app->when(SkinConcernsController::class)
            ->needs(LookupInterface::class)
            ->give(SkinConcernsService::class);

        $this->app->when(ProductTypesController::class)
            ->needs(LookupInterface::class)
            ->give(ProductTypesService::class);

        $this->app->when(MarksController::class)
            ->needs(LookupInterface::class)
            ->give(MarksService::class);

        $this->app->when(ExtrasController::class)
            ->needs(LookupInterface::class)
            ->give(ExtrasService::class);

        $this->app->when(ProductsController::class)
            ->needs(LookupInterface::class)
            ->give(ProductsService::class);

        $this->app->when(LanguageController::class)
            ->needs(LookupInterface::class)
            ->give(LanguageService::class);
    }
}
