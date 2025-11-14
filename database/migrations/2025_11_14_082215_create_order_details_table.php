<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->unsignedInteger('order_id'); // Khóa ngoại đến orders.id
            $table->unsignedInteger('product_id'); // Khóa ngoại đến products.id
            $table->unsignedInteger('quantity'); // Số lượng mua
            $table->unsignedInteger('price'); // Đơn giá tại thời điểm đặt hàng
            $table->timestamps(); // created_at và updated_at
            
            // Đặt unique key cho cặp order_id và product_id
            $table->unique(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
}