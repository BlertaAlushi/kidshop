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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('promotion_id')
                ->nullable()
                ->after('discount_amount')
                ->constrained()
                ->nullOnDelete();

            // Snapshot of the promotion name at the time of purchase.
            $table->string('promotion_name')->nullable()->after('promotion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promotion_id');
            $table->dropColumn('promotion_name');
        });
    }
};
