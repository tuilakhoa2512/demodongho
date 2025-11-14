<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->unsignedInteger('product_id'); // Khóa ngoại đến products.id
            $table->string('image_1', 255)->nullable(); // Ảnh 1
            $table->string('image_2', 255)->nullable(); // Ảnh 2
            $table->string('image_3', 255)->nullable(); // Ảnh 3
            $table->string('image_4', 255)->nullable(); // Ảnh 4
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
}