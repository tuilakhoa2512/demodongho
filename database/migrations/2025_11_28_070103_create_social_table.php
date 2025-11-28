<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social', function (Blueprint $table) {
            $table->bigIncrements('id'); // PK

            // ID của người dùng trên Google
            $table->string('provider_user_id');

            // Email Google
            $table->string('provider_user_email')->nullable();

            // Tên provider: google, facebook, github...
            $table->string('provider');

            // user_id liên kết với bảng users
            $table->unsignedBigInteger('user_id')->nullable();

            // Khóa ngoại
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social');
    }
};
