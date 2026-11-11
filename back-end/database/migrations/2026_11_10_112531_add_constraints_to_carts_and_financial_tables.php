<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->unsigned()->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->unsigned()->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->unsigned()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->unsigned(false)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->unsigned(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->unsigned(false)->change();
        });

       
    }
};
