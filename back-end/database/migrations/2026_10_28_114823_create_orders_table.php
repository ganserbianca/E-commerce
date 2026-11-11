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
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('address_id')->constrained('addresses');
            $table->string('status',30);
            $table->decimal('total_amount',10,2);
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamps();
            
            //cópia do endereço registrado na compra
            $table->string('order_street', 150);
            $table->string('order_number', 10);
            $table->string('order_cep', 9);
            $table->string('order_neighborhood', 100);
            $table->string('order_city', 150);
            $table->char('order_uf', 2);
            $table->text('order_complement')->nullable();
            
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
