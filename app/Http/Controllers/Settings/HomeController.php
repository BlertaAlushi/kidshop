<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ProductsCollectionInterface;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        protected  ProductsCollectionInterface $productsCollection,
    ){}
    public function index(){
        $new_arrivals = $this->productsCollection->newArrivals();
        return Inertia::render('Home', [
            'new_arrivals' => $new_arrivals,
        ]);
    }
}
