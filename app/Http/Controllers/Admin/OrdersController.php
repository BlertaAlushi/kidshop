<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrdersController extends Controller
{
    public const STATUSES = ['pending', 'confirmed', 'ready_for_delivery', 'completed', 'cancelled'];
    public const PAYMENT_STATUSES = ['pending', 'paid'];

    /**
     * Display a filterable, paginated listing of orders.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:100',
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'payment_status' => ['nullable', Rule::in(self::PAYMENT_STATUSES)],
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $orders = Order::query()
            ->withCount('items')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('admin/orders/OrderIndex', [
            'orders' => $orders,
            'filters' => (object) $filters,
            'statuses' => self::STATUSES,
            'payment_statuses' => self::PAYMENT_STATUSES,
            'status_counts' => (object) $statusCounts,
        ]);
    }

    /**
     * Display a single order with its items and address.
     */
    public function show(Order $order)
    {
        $order->load(['items.productVariant.product.images', 'addresses.countryDetails']);

        $order->items->each(function ($item) {
            $variant = $item->productVariant;
            $images = $variant?->product?->images
                ->sortBy([['is_primary', 'desc'], ['sort_order', 'asc']]) ?? collect();

            // Prefer an image matching the ordered color, fall back to the product's main image
            $image = $images->firstWhere('color_id', $variant?->color_id) ?? $images->first();

            $item->setAttribute('image_url', $image ? Storage::url($image->path) : null);
            $item->unsetRelation('productVariant');
        });

        return Inertia::render('admin/orders/OrderShow', [
            'order' => $order,
            'statuses' => self::STATUSES,
            'payment_statuses' => self::PAYMENT_STATUSES,
        ]);
    }

    /**
     * Update the order and payment status.
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUSES)],
        ]);

        $order->update($data);

        return back()->with('success', 'edited_success');
    }
}
