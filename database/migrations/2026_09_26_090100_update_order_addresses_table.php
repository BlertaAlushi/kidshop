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
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('postal_code')->nullable()->change();
            $table->string('country', 2)->change();
        });

        Schema::table('order_addresses', function (Blueprint $table) {
            $table->foreign('country')->references('iso_2')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropForeign(['country']);
        });

        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('country')->change();
            $table->string('postal_code')->nullable(false)->change();
        });
    }
};
