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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Snapshot of product information at the time of purchase.
            $table->string('product_name');
            $table->string('size_name')->nullable();
            $table->string('color_name')->nullable();

            $table->decimal('original_unit_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2);

            $table->unsignedInteger('quantity');
            $table->decimal('total', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
