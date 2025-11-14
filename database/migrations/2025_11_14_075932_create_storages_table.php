<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoragesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storages', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->string('product_name', 255); // Tên hàng nhập
            $table->string('supplier_name', 255)->nullable(); // Nhà cung ứng
            $table->date('import_date')->nullable(); // Ngày nhập
            $table->unsignedInteger('import_quantity'); // Số lượng nhập
            $table->unsignedInteger('unit_import_price'); // Giá nhập 1 món (vnd)
            $table->unsignedInteger('total_import_price'); // Tổng giá nhập (vnd)
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
}