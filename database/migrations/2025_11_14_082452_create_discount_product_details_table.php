<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountProductDetailsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_product_details', function (Blueprint $table) {
            $table->unsignedInteger('discount_product_id'); // Khóa ngoại đến discount_products.id
            $table->unsignedInteger('product_id'); // Khóa ngoại đến products.id
            $table->date('expiration_date')->nullable(); // Ngày hết hạn
            $table->timestamps(); // created_at và updated_at
            
            // Đặt unique key cho cặp discount_product_id và product_id
            $table->unique(['discount_product_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_product_details');
    }
}