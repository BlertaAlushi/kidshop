<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReport;
use App\Models\OrderProduct;
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
        $dailySales = OrderProduct::select(
            'products.name as name',
            'order_products.unit_price as unit_price',
            'products.currency as currency',
            DB::raw('SUM(order_products.quantity) as quantity'),
            DB::raw('SUM(order_products.quantity * order_products.unit_price) as totalprice')
        )
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->where('orders.status', "confirmed")
            ->whereDate('orders.updated_at', Carbon::today())
            ->groupBy('products.id', 'products.name','order_products.unit_price','products.currency')
            ->get();

        $email = "admin@test.com";
        $admin = User::where('admin', true)->first();
        if(!empty($admin)){
            $email = $admin->email;
        }
        Mail::to($email)->send(new DailySalesReport($dailySales));
    }
}
