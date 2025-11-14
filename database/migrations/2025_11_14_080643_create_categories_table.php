<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->string('name', 150); // Tên danh mục
            $table->string('image', 255)->nullable(); // Ảnh (có thể để trống)
            $table->text('description')->nullable(); // Mô tả (có thể để trống)
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}