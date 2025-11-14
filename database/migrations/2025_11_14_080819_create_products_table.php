<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->string('name', 255); // Tên sản phẩm
            $table->text('description')->nullable(); // Mô tả
            $table->string('strap_material', 100)->nullable(); // Chất liệu dây
            $table->decimal('dial_size', 5, 2)->nullable(); // Kích thước mặt kính (mm)
            $table->enum('gender', ['male', 'female', 'unisex']); // Giới tính
            $table->unsignedInteger('category_id'); // Khóa ngoại categories.id
            $table->unsignedInteger('brand_id'); // Khóa ngoại brands.id
            $table->unsignedInteger('storage_id'); // Khóa ngoại storages.id
            $table->unsignedInteger('price'); // Giá bán (vnd)
            $table->unsignedInteger('quantity'); // Tồn kho
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}