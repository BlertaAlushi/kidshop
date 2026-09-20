<?php

use App\Http\Middleware\HandleAdminRequests;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function() {
            Route::middleware('web')
                ->group(base_path('routes/root.php'));

            Route::middleware(['web', 'setLocale'])
                ->prefix('{locale}')
                ->group(function () {
                    require base_path('routes/web.php');
                    require base_path('routes/user.php');
                    require base_path('routes/admin.php');
                    require base_path('routes/settings.php');
                    require base_path('vendor/laravel/fortify/routes/routes.php');
                });
        },
        web: __DIR__.'/../routes/root.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'setLocale' => SetLocale::class,
            'isAdmin'=>HandleAdminRequests::class
        ]);

        $middleware->redirectUsersTo(function () {
            return '/' . app()->getLocale() ;
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
