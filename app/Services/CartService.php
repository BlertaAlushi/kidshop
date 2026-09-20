<?php

namespace App\Services;
use App\Models\CartProduct;
use App\Models\Product;
use App\Resources\CartResource;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public static function cartProductCount(){
        if(empty(Auth::user())){
            return 0;
        }
       return Auth::user()->cartProducts()->sum('quantity');
    }

    public static function cartTotal(){
        if(empty(Auth::user())){
            return 0;
        }
        $sum = 0;
        Auth::user()->cartProducts()->each(function($cartProduct) use (&$sum){
            $sum += $cartProduct->product->price * $cartProduct->quantity;
        });
        return $sum;
    }

    public function index(){

        $cartProducts = Auth::user()->cartProducts()->with('product.translation')->get();
        return CartResource::collection($cartProducts);

    }

    public function addToCart($data)
    {
        $cartProduct = CartProduct::where('product_id', $data['product_id'])
            ->where('user_id', Auth::id())
            ->first();

        if ($cartProduct) {
            $cartProduct->increment('quantity', $data['quantity']);
        } else {
            CartProduct::create([
                'product_id' => $data['product_id'],
                'user_id'    => Auth::id(),
                'quantity'   => $data['quantity'],
            ]);
        }
    }

    public function updateCart($data,$cartProduct){
        $cartProduct->update([
            'quantity' => $data['quantity'],
        ]);
    }


}
