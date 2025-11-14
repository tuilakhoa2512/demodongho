<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');                       // INT UNSIGNED AI
            $table->unsignedInteger('role_id');            // FK -> roles.id

            $table->string('fullname', 150);
            $table->string('email', 100)->unique();
            $table->string('password', 255);               // mật khẩu băm
            $table->string('phone', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('ward', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->string('image', 255)->nullable();

            $table->timestamps();

            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
