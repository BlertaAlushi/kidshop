<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReport;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-sales-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dailySales = OrderItem::select(
            'order_items.product_name as name',
            'order_items.unit_price as unit_price',
            DB::raw('SUM(order_items.quantity) as quantity'),
            DB::raw('SUM(order_items.total) as totalprice')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'confirmed')
            ->whereDate('orders.updated_at', Carbon::today())
            ->groupBy('order_items.product_name', 'order_items.unit_price')
            ->get();

        $email = "admin@test.com";
        $admin = User::where('admin', true)->first();
        if(!empty($admin)){
            $email = $admin->email;
        }
        Mail::to($email)->send(new DailySalesReport($dailySales));
    }
}
