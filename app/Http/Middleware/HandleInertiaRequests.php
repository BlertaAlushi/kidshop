<?php

namespace App\Http\Middleware;
use App\Services\CartService;
use App\Services\FilterOptionsService;
use App\Services\LanguageService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
//        $menu = Cache::remember('menu', 3600, function () {
//            return FilterOptionsService::menuOptions();
//        });
        $menu = FilterOptionsService::menuOptions();
        $languages = (new LanguageService())->index();
        $cart_products_count = CartService::cartProductCount();
        $cart_total_price = CartService::cartTotal();
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'menu' => $menu,
            'languages' => $languages,
            'app_domain'=>config('app.domain'),
            'flash' => [
                'success' => $request->session()->get('success'),
            ],
            'cartProductCount'=>$cart_products_count,
            'cartTotalPrice'=> $cart_total_price,
        ];
    }
}
