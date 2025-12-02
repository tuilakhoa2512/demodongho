<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_product_details', function (Blueprint $table) {
            $table->unsignedInteger('discount_product_id'); // INT(10) UNSIGNED (PK, FK → discount_products.id)
            $table->unsignedInteger('product_id');          // INT(10) UNSIGNED (PK, FK → products.id)
            $table->date('expiration_date')->nullable();     // DATE
            $table->tinyInteger('status')->unsigned()->default(1)
                  ->comment('1=active, 0=inactive');         // TINYINT(3) UNSIGNED DEFAULT 1
            $table->timestamps();                           // created_at, updated_at

            $table->primary(['discount_product_id', 'product_id']);
        });

        Schema::table('discount_product_details', function (Blueprint $table) {
            $table->foreign('discount_product_id')
                  ->references('id')
                  ->on('discount_products')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_product_details');
    }
};
