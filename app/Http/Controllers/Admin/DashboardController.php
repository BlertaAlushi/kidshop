<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(){
        $statusCounts = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Revenue excludes cancelled orders; "paid" is the portion already collected.
        $revenue = Order::query()
            ->where('status', '!=', 'cancelled')
            ->selectRaw('coalesce(sum(total_amount), 0) as total')
            ->selectRaw("coalesce(sum(case when payment_status = 'paid' then total_amount else 0 end), 0) as paid")
            ->first();

        // Sales per promotion, excluding cancelled orders. Lines whose promotion was
        // deleted keep their name snapshot but share a null id, so they group together.
        $promotionSales = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereNotNull('order_items.promotion_name')
            ->groupBy('order_items.promotion_id')
            ->selectRaw('order_items.promotion_id')
            ->selectRaw('max(order_items.promotion_name) as promotion_name')
            ->selectRaw('count(distinct order_items.order_id) as order_count')
            ->selectRaw('sum(order_items.quantity) as units_sold')
            ->selectRaw('sum(order_items.total) as revenue')
            ->selectRaw('sum(order_items.discount_amount * order_items.quantity) as discount_given')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'promotion_id' => $row->promotion_id,
                'promotion_name' => $row->promotion_name,
                'order_count' => (int) $row->order_count,
                'units_sold' => (int) $row->units_sold,
                'revenue' => round((float) $row->revenue, 2),
                'discount_given' => round((float) $row->discount_given, 2),
            ]);

        return Inertia::render('admin/Dashboard', [
            'order_count' => (int) $statusCounts->sum(),
            'revenue_total' => round((float) $revenue->total, 2),
            'revenue_paid' => round((float) $revenue->paid, 2),
            'statuses' => OrdersController::STATUSES,
            'status_counts' => (object) $statusCounts,
            'promotion_sales' => $promotionSales,
        ]);
    }
}
