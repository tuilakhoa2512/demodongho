<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');  

            $table->string('order_code', 50)->unique();  // Mã đơn hàng

            $table->unsignedInteger('user_id');      // Khóa ngoại users
            $table->string('status', 30);               // pending/paid/shipped...
            $table->string('payment_method', 30);       // COD/BANK
            $table->float('total_price');               // Tổng giá sau giảm

            $table->unsignedInteger('discount_bill_id')->nullable(); // FK -> discount_bills.id
            $table->unsignedTinyInteger('discount_bill_rate')->nullable();  // %
            $table->unsignedInteger('discount_bill_value')->nullable();     // Số tiền giảm

            $table->timestamps(); // created_at, updated_at

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('discount_bill_id')->references('id')->on('discount_bills')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
