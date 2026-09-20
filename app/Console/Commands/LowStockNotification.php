<?php

namespace App\Console\Commands;

use App\Mail\LowStockMail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class LowStockNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:low-stock-notification';

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
        $products = Product::where('stock_quantity','<',10 )->get();
        $email = "admin@test.com";
        $admin = User::where('admin', true)->first();
        if(!empty($admin)){
            $email = $admin->email;
        }
        Mail::to($email)->send(new LowStockMail($products));
    }
}
