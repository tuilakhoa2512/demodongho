<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountBillsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_bills', function (Blueprint $table) {
            $table->unsignedInteger('id', 10)->autoIncrement(); // Khóa chính
            $table->string('name', 150)->nullable(); // Tên chương trình ưu đãi
            $table->unsignedInteger('min_subtotal')->nullable(); // Ngưỡng tối thiểu
            $table->tinyInteger('rate')->unsigned()->nullable(); // Phần trăm giảm (%)
            $table->tinyInteger('is_active')->unsigned()->default(1); // 1=active, 0=inactive
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_bills');
    }
}