<?php

namespace App\Jobs;

use App\Mail\ConfirmationMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ConfirmationEmail implements ShouldQueue
{
    use Queueable;
    private Order $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order->load(['addresses', 'items']);

        if (!$order->customer_email) {
            return;
        }

        Mail::to($order->customer_email)->send(new ConfirmationMail($order));
    }
}
