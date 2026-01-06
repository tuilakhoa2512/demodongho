<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhansu', function (Blueprint $table) {
            // ===== PRIMARY KEY =====
            $table->increments('id'); // INT UNSIGNED AUTO_INCREMENT

            // ===== ROLE =====
            $table->unsignedInteger('role_id');   

            // ===== THÔNG TIN ĐĂNG NHẬP =====
            $table->string('fullname', 150);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('phone', 20)->nullable();

            // ===== TRẠNG THÁI =====
            $table->tinyInteger('status')
                  ->unsigned()
                  ->default(1);

            // ===== NGƯỜI TẠO =====
            $table->unsignedInteger('created_by')
                  ->nullable();

            // ===== TIME =====
            $table->timestamps();

            // ===== FOREIGN KEY =====
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('restrict');

            $table->foreign('created_by')
                  ->references('id')
                  ->on('nhansu')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhansu');
    }
};
