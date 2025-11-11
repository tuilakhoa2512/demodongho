<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {  
            $table->unsignedInteger('id', 10)->autoIncrement(); // Tạo cột id là INT(10) UNSIGNED AUTO_INCREMENT
            $table->string('name'); // Thêm cột tên cho role
            $table->timestamps(); // Thêm cột created_at và updated_at
        });     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
