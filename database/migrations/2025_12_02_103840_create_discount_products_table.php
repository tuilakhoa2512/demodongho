<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_products', function (Blueprint $table) {
            $table->increments('id');                     // INT(10) UNSIGNED AUTO_INCREMENT (PK)
            $table->string('name', 255);                  // VARCHAR(255)
            $table->unsignedInteger('rate');              // INT(10) UNSIGNED (% giảm)
            $table->tinyInteger('status')->unsigned()->default(1)
                  ->comment('1=active, 0=inactive');       // TINYINT(3) UNSIGNED DEFAULT 1
            $table->timestamps();                         // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_products');
    }
};
