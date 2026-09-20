<?php

namespace App\Services;

use App\Jobs\ConfirmationEmail;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function saveCustomerAddress($data)
    {
        $address = OrderAddress::updateOrCreate(
            ['email' => $data['email']],
            $data
        );
        return $address->id;
    }

    public function createOrder($address_id){
        $cartProducts = Auth::user()
            ->cartProducts()
            ->with('product')
            ->get();

        try {
            $order = DB::transaction(function () use ($cartProducts, $address_id) {

                if ($cartProducts->isEmpty()) {
                    throw new \Exception("Cart is empty.");
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_address_id' => $address_id,
                    'status' => 'pending',
                    'total_price' => $cartProducts->sum(
                        fn($item) => $item->quantity * $item->product->price
                    ),
                ]);

                foreach ($cartProducts as $cartItem) {

                    $product = Product::lockForUpdate()->find($cartItem->product_id);

                    if (!$product || $cartItem->quantity > $product->stock_quantity) {
                        throw new \Exception("Product ".$cartItem->product->name." is out of stock.");
                    }

                    $order->products()->create([
                        'product_id' => $product->id,
                        'quantity'   => $cartItem->quantity,
                        'unit_price'      => $product->price,
                    ]);

                    $product->decrement('stock_quantity', $cartItem->quantity);
                }

                $order->update(['status' => 'confirmed']);

                Auth::user()->cartProducts()->delete();

                return $order;
            });

            ConfirmationEmail::dispatch($order);

            return [
                'success' => true,
            ];

        } catch (\Exception $e) {

            return[
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
