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
        Schema::create('brands', function (Blueprint $table) {
            $table->increments('id');   // Id INT(10) UNSIGNED AUTO_INCREMENT
            $table->string('name', 150); // Tên thương hiệu
            $table->string('image', 255)->nullable(); // Ảnh, có thể null
            $table->text('description')->nullable(); // Mô tả, có thể null
            $table->tinyInteger('status')->unsigned()->default(1); // 1=active, 0=inactive
            $table->timestamps(); // created_at và updated_at
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
