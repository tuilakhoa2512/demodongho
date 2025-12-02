<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');                        // INT(10) UNSIGNED AI (PK)
            $table->unsignedInteger('role_id');              // INT(10) UNSIGNED (FK → roles.id)
                  // INT(10) UNSIGNED (FK → wards.id)

            $table->string('fullname', 150);                 // VARCHAR(150)
            $table->string('email', 100)->unique();          // VARCHAR(100) UNIQUE
            $table->string('password', 255);                 // mật khẩu băm
            $table->string('phone', 20)->nullable();         // NULLABLE
            $table->string('address', 255)->nullable();      // NULLABLE
            $table->unsignedInteger('province_id')->nullable();        // INT(10) UNSIGNED (FK → provinces.id)
            $table->unsignedInteger('district_id')->nullable();        // INT(10) UNSIGNED (FK → districts.id)
            $table->unsignedInteger('ward_id')->nullable(); 
            $table->tinyInteger('status')->unsigned()->default(1)
                  ->comment('1=active, 0=inactive');         // TINYINT(3) UNSIGNED DEFAULT 1
            $table->string('image', 255)->nullable();        // Ảnh đại diện - NULLABLE

            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onDelete('cascade');

            $table->foreign('province_id')
                  ->references('id')->on('provinces')
                  ->onDelete('cascade');

            $table->foreign('district_id')
                  ->references('id')->on('districts')
                  ->onDelete('cascade');

            $table->foreign('ward_id')
                  ->references('id')->on('wards')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
