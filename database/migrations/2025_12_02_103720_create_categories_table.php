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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');   // INT(10) UNSIGNED AUTO_INCREMENT - Primary Key

            $table->string('name', 150)->unique(); // VARCHAR(150) UNIQUE - Tên danh mục
            $table->text('description')->nullable(); // TEXT - Mô tả

            $table->tinyInteger('status')->unsigned()->default(1); // TINYINT(3) UNSIGNED (1=active,0=inactive)

            $table->timestamps(); // created_at & updated_at (TIMESTAMP)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
