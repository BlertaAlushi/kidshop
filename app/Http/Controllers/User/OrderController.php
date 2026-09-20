<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Services\OrderService;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ){}
    public function order(CustomerRequest $request){
        $data = $request->validated();
        $address_id = $this->orderService->saveCustomerAddress($data);
        $order = $this->orderService->createOrder($address_id);
        return response()->json($order);
    }
}
