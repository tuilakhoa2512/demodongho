<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->increments('id');               // INT(10) UNSIGNED AUTO_INCREMENT (PK)
            $table->unsignedInteger('district_id'); // INT(10) UNSIGNED (FK → districts.id)
            $table->string('code', 10)->unique();   // VARCHAR(10) UNIQUE - Mã phường/xã
            $table->string('name', 255);            // VARCHAR(255) - Tên phường/xã
            $table->string('type', 100)->nullable()
                  ->comment('Phường / Xã / Thị trấn'); // VARCHAR(100) NULLABLE - loại hành chính
            $table->timestamps();                   // created_at / updated_at
        });

        // Thêm ràng buộc khóa ngoại sau khi tạo bảng (đúng order chạy của schema builder)
        Schema::table('wards', function (Blueprint $table) {
            $table->foreign('district_id')
                  ->references('id')->on('districts')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
