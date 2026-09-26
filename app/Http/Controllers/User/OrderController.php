<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Services\OrderService;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ){}
    public function order(CustomerRequest $request){
        $data = $request->validated();
        $order = $this->orderService->createOrder($data);
        return response()->json($order);
    }
}
