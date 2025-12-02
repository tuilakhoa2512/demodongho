<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('social', function (Blueprint $table) {
            $table->increments('id');   // PK - Auto increment
            $table->string('provider_user_id', 255)->nullable(false);
            $table->string('provider_user_email', 255)->nullable();
            $table->string('provider', 255)->nullable(false);
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();

            // Khóa ngoại → users.id
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('social');
    }
};
