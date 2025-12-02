<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->increments('id');               // INT(10) UNSIGNED AUTO_INCREMENT (PK)
            $table->unsignedInteger('province_id'); // INT(10) UNSIGNED (FK → provinces.id)
            $table->string('code', 10)->unique();   // VARCHAR(10) UNIQUE - Mã quận/huyện
            $table->string('name', 255);            // VARCHAR(255) - Tên quận/huyện
            $table->string('type', 100)->nullable()
                  ->comment('Quận / Huyện / Thị xã...'); // VARCHAR(100) NULLABLE

            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('province_id')
                  ->references('id')->on('provinces')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
