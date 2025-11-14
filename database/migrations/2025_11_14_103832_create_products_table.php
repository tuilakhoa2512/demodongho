<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('strap_material', 100)->nullable();
            $table->decimal('dial_size', 5, 2)->nullable(); // mm

            $table->enum('gender', ['male', 'female', 'unisex'])->nullable();

            $table->unsignedInteger('category_id');
            $table->unsignedInteger('brand_id');
            $table->unsignedInteger('storage_id');

            $table->unsignedInteger('price');       // vnd
            $table->unsignedInteger('quantity');    // tồn kho

            $table->timestamps();

            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade');

            $table->foreign('brand_id')
                  ->references('id')->on('brands')
                  ->onDelete('cascade');

            $table->foreign('storage_id')
                  ->references('id')->on('storages')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
