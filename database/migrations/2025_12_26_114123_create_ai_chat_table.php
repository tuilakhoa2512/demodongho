<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('ai_chat', function (Blueprint $table) {
            $table->id();

            // Không dùng Auth → dùng session
            $table->string('session_id', 100)->index();

            // user | ai
            $table->enum('role', ['user', 'ai']);

            // nội dung chat
            $table->text('message');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat');
    }
};
