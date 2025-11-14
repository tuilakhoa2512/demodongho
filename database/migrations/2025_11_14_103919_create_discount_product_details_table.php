<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_product_details', function (Blueprint $table) {

            $table->unsignedInteger('discount_product_id');
            $table->unsignedInteger('product_id');
            $table->date('expiration_date');

            $table->timestamps();

            // PK kép: mỗi product chỉ tham gia 1 dòng / 1 chương trình
            $table->primary(['discount_product_id', 'product_id']);

            $table->foreign('discount_product_id')
                  ->references('id')->on('discount_products')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_product_details');
    }
};
