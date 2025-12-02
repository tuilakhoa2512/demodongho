<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');                 // INT(10) UNSIGNED AI

            $table->unsignedInteger('order_id');      // INT(10) UNSIGNED - FK -> orders.id
            $table->unsignedInteger('product_id');    // INT(10) UNSIGNED - FK -> products.id

            $table->unsignedInteger('quantity');      // Số lượng
            $table->float('price');                   // Đơn giá tại thời điểm đặt

            $table->timestamps();

            // Ràng buộc duy nhất cho (order_id, product_id)
            $table->unique(['order_id', 'product_id']);

            // Khóa ngoại
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
