<?php

namespace App\Providers;

use App\Interfaces\Services\ProductsCollectionInterface;
use App\Services\Products\ProductsCollectionService;
use Illuminate\Support\ServiceProvider;

class ProductCollectionServiceProvider extends ServiceProvider
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
        $this->app->bind(ProductsCollectionInterface::class, ProductsCollectionService::class);
    }
}
