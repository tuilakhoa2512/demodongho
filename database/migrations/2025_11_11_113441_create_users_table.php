<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedinteger('id'); // Tạo cột id
            $table->unsignedInteger('role_id'); // Tạo role_id là kiểu INT unsigned

            // Đặt khoá ngoại cho role_id
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            $table->string('fullname', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->string('phone', 20);
            $table->string('address', 255);
            $table->string('district', 100);
            $table->string('ward', 100);
            $table->string('province', 100);
            $table->tinyInteger('status')->unsigned();
            $table->string('image', 100);
            $table->timestamps(); // Thêm created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}