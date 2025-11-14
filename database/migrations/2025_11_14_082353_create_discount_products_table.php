<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_products', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->string('name', 50); // Tên chương trình ưu đãi
            $table->unsignedInteger('rate'); // Phần trăm giảm
            $table->unsignedInteger('used_orders_count')->default(0); // Số đơn đã áp dụng
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_products');
    }
}