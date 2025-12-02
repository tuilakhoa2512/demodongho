<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');   // INT(10) UNSIGNED AUTO_INCREMENT - Primary Key

            $table->string('name', 255);                 // Tên sản phẩm
            $table->text('description')->nullable();     // Mô tả
            $table->string('strap_material', 100)->nullable(); // Chất liệu dây

            $table->decimal('dial_size', 5, 2)->nullable(); // Kích thước mặt kính

            $table->enum('gender', ['male', 'female', 'unisex'])->nullable(); // Giới tính

            // FOREIGN KEYS
            $table->unsignedInteger('category_id'); // FK -> categories.id
            $table->unsignedInteger('brand_id');    // FK -> brands.id
            $table->unsignedInteger('storage_detail_id'); // FK -> storage_details.id

            $table->float('price')->default(0);         // Giá bán
            $table->unsignedInteger('quantity')->default(0); // Tồn kho

            $table->enum('stock_status', ['selling', 'sold_out', 'stopped'])
                  ->default('selling'); // Trạng thái bán

            $table->tinyInteger('status')->unsigned()->default(1); // 1=active,0=inactive

            $table->timestamps();

            // FOREIGN KEY CONSTRAINTS
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade');

            $table->foreign('brand_id')
                  ->references('id')->on('brands')
                  ->onDelete('cascade');

            $table->foreign('storage_detail_id')
                  ->references('id')->on('storage_details')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
