<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCartRequest;
use App\Jobs\LowStockNotification;
use App\Models\CartProduct;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
    ){}
    public function index(){
        $cart = $this->cartService->index();
        return Inertia::render('user/Cart', ["cartProducts" => $cart]);
    }

    public function addToCart(UpdateCartRequest $request){
        $data = $request->validated();
        $this->cartService->addToCart($data);
        return response(null, 200);
    }

    public function updateCartProduct(UpdateCartRequest $request, CartProduct $cartProduct){
        $data = $request->validated();
        $this->cartService->updateCart($data,$cartProduct);
        return response(null, 200);
    }

    public function removeFromCart(CartProduct  $cartProduct){
        $cartProduct->delete();
        return redirect()->back();
    }

    public function checkout(){
        $cart = $this->cartService->index();
        if(!$cart->count()){
            return redirect()->route('home');
        }
        return Inertia::render('user/Checkout', ["cartProducts" => $cart]);
    }
}
