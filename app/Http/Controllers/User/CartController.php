<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCartRequest;
use App\Models\CartItem;
use App\Models\Country;
use App\Services\CartService;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
    ){}
    public function index(){
        $cart = $this->cartService->index();
        return Inertia::render('user/Cart', ["cartItems" => $cart]);
    }

    public function addToCart(UpdateCartRequest $request){
        $data = $request->validated();
        $this->cartService->addToCart($data);
        return response(null, 200);
    }

    public function updateCartItem(UpdateCartRequest $request, CartItem $cartItem){
        $data = $request->validated();
        $this->cartService->updateCart($data, $cartItem);
        return response(null, 200);
    }

    public function removeFromCart(CartItem $cartItem){
        $cartItem->delete();
        return redirect()->back();
    }

    public function checkout(){
        $cart = $this->cartService->index();
        if(!$cart->count()){
            return redirect()->route('home');
        }
        return Inertia::render('user/Checkout', [
            'cartItems' => $cart,
            'countries' => Country::orderBy('country')->get(['iso_2', 'country', 'delivery_fee']),
        ]);
    }
}
