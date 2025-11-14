<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->unsignedInteger('product_id'); // Khóa ngoại đến products.id
            $table->unsignedInteger('user_id'); // Khóa ngoại đến users.id
            $table->tinyInteger('rating')->unsigned(); // Đánh giá (1-5)
            $table->text('comment')->nullable(); // Bình luận
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
        Schema::dropIfExists('reviews');
    }
}