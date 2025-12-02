<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->increments('id');              // INT(10) UNSIGNED AUTO_INCREMENT (PK)
            $table->string('code', 10)->unique();  // VARCHAR(10) UNIQUE - Mã tỉnh/thành
            $table->string('name', 255);           // VARCHAR(255) - Tên tỉnh/thành
            $table->string('type', 100)->nullable() // VARCHAR(100) NULLABLE - Loại (Tỉnh/TP)
                  ->comment('Tỉnh / Thành phố');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
