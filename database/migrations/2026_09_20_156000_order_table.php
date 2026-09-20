<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();

            $table->text('notes')->nullable();

            $table->enum('status', [
                'pending',
                'confirmed',
                'ready_for_delivery',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->enum('payment_method', [
                'cash_on_delivery',
            ])->default('cash_on_delivery');

            $table->enum('payment_status', [
                'pending',
                'paid',
            ])->default('pending');

            $table->decimal('total_amount', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
