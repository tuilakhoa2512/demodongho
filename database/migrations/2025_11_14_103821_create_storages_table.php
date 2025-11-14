<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('product_name', 255);      // tên hàng nhập
            $table->string('supplier_name', 255)->nullable();
            $table->date('import_date');
            $table->unsignedInteger('import_quantity');
            $table->unsignedInteger('unit_import_price');
            $table->unsignedInteger('total_import_price');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
};
