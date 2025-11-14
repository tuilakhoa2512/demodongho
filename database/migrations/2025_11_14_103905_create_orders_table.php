<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->string('status', 30);                // pending/paid/shipped…
            $table->string('payment_method', 30);        // COD / BANK
            $table->unsignedInteger('total');            // tổng giá sau giảm

            $table->unsignedInteger('discount_bill_id')->nullable();
            $table->tinyInteger('discount_bill_rate')->unsigned()->nullable();    // % được giảm
            $table->unsignedInteger('discount_bill_value')->nullable();           // số tiền được giảm

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('discount_bill_id')
                  ->references('id')->on('discount_bills')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
