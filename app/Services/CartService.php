<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Resources\CartResource;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function currentCart(): Cart
    {
        return Cart::firstOrCreate(['session_id' => Session::getId()]);
    }

    protected function findCart(): ?Cart
    {
        return Cart::where('session_id', Session::getId())->first();
    }

    public static function cartProductCount(){
        return (int) ((new static())->findCart()?->items()->sum('quantity') ?? 0);
    }

    public static function cartTotal(){
        $cart = (new static())->findCart();
        if (!$cart) {
            return 0;
        }
        $items = $cart->items()->with('productVariant')->get();
        return $items->sum(fn($item) => $item->quantity * $item->productVariant->price);
    }

    public function index(){
        $items = $this->currentCart()
            ->items()
            ->with(['productVariant.product.images', 'productVariant.size', 'productVariant.color'])
            ->get();

        return CartResource::collection($items);
    }

    public function addToCart($data)
    {
        $cart = $this->currentCart();

        $item = $cart->items()->where('product_variant_id', $data['product_variant_id'])->first();

        if ($item) {
            $item->increment('quantity', $data['quantity']);
        } else {
            $cart->items()->create([
                'product_variant_id' => $data['product_variant_id'],
                'quantity' => $data['quantity'],
            ]);
        }
    }

    public function updateCart($data, CartItem $cartItem){
        $cartItem->update([
            'quantity' => $data['quantity'],
        ]);
    }
}
