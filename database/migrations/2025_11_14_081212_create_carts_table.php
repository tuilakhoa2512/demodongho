<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->unsignedInteger('product_id'); // Khóa ngoại đến products.id
            $table->unsignedInteger('user_id'); // Khóa ngoại đến users.id
            $table->unsignedInteger('quantity'); // Số lượng trong giỏ
            $table->timestamps(); // created_at và updated_at

            // Đặt unique key cho cặp user_id và product_id
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
}