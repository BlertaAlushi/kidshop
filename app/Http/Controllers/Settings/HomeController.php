<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ProductsCollectionInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        protected  ProductsCollectionInterface $productsCollection,
    ){}
    public function index(){
        $new_arrivals = $this->productsCollection->newArrivals();
        $heroBackgroundPath = Setting::get('home_hero_background_image');

        return Inertia::render('Home', [
            'new_arrivals' => $new_arrivals,
            'hero_background_image' => $heroBackgroundPath ? Storage::url($heroBackgroundPath) : null,
            'hero_title' => Setting::get('home_hero_title', 'Style that keeps up with them'),
            'hero_description' => Setting::get(
                'home_hero_description',
                'Fresh drops for every adventure — from playground mornings to pajama nights. Playful, comfy, made to move.'
            ),
        ]);
    }
}
