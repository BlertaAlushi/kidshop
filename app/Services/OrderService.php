<?php

namespace App\Services;

use App\Jobs\ConfirmationEmail;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
    ){}

    public function createOrder($customer){
        $cart = $this->cartService->currentCart();
        $cartItems = $cart->items()->with('productVariant.product', 'productVariant.size', 'productVariant.color')->get();

        try {
            $order = DB::transaction(function () use ($cartItems, $customer) {

                if ($cartItems->isEmpty()) {
                    throw new \Exception('Cart is empty.');
                }

                $country = Country::findOrFail($customer['country']);
                $deliveryFee = $country->delivery_fee;

                $order = Order::create([
                    'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                    'customer_name' => $customer['name'],
                    'customer_phone' => $customer['phone'],
                    'customer_email' => $customer['email'] ?? null,
                    'status' => 'pending',
                    'payment_method' => 'cash_on_delivery',
                    'payment_status' => 'pending',
                    'total_amount' => 0,
                    'delivery_fee' => $deliveryFee,
                ]);

                $total = 0;

                foreach ($cartItems as $cartItem) {
                    $variant = ProductVariant::lockForUpdate()->find($cartItem->product_variant_id);

                    if (!$variant || $cartItem->quantity > $variant->stock_quantity) {
                        throw new \Exception('Product '.$cartItem->productVariant->product->name.' is out of stock.');
                    }

                    $lineTotal = $variant->price * $cartItem->quantity;
                    $total += $lineTotal;

                    $order->items()->create([
                        'product_variant_id' => $variant->id,
                        'product_name' => $cartItem->productVariant->product->name,
                        'size_name' => $cartItem->productVariant->size?->name,
                        'color_name' => $cartItem->productVariant->color?->name,
                        'original_unit_price' => $variant->price,
                        'discount_amount' => 0,
                        'unit_price' => $variant->price,
                        'quantity' => $cartItem->quantity,
                        'total' => $lineTotal,
                    ]);

                    $variant->decrement('stock_quantity', $cartItem->quantity);
                }

                $order->update([
                    'status' => 'confirmed',
                    'total_amount' => $total + $deliveryFee,
                ]);

                OrderAddress::create([
                    'order_id' => $order->id,
                    'type' => 'shipping',
                    'first_name' => $customer['name'],
                    'last_name' => '',
                    'phone' => $customer['phone'],
                    'address' => $customer['address'],
                    'city' => $customer['city'],
                    'postal_code' => $customer['zip'],
                    'country' => $customer['country'],
                ]);

                $cartItems->each->delete();

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
