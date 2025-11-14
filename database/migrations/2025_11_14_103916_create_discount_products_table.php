<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_products', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 50);
            $table->unsignedInteger('rate');              // % giảm
            $table->unsignedInteger('used_orders_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_products');
    }
};
