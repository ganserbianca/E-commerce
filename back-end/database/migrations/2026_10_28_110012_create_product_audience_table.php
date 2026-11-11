<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_audience', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('audience_id')->constrained('audiences');
            $table->primary(['product_id','audience_id']);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_audience');
    }
};
