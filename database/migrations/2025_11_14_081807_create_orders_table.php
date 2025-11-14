<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->unsignedInteger('user_id'); // Khóa ngoại đến users.id
            $table->string('status', 30)->nullable(); // Trạng thái (pending/paid/shipped...)
            $table->string('payment_method', 30)->nullable(); // Phương thức thanh toán (COD/BANK...)
            $table->unsignedInteger('total'); // Tổng giá sau giảm
            $table->unsignedInteger('discount_bill_id')->nullable(); // Khóa ngoại đến discount_bills.id
            $table->tinyInteger('discount_bill_rate')->unsigned()->nullable(); // Phần trăm giảm từ chương trình ưu đãi
            $table->unsignedInteger('discount_bill_value')->unsigned()->nullable(); // Số tiền được giảm
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}