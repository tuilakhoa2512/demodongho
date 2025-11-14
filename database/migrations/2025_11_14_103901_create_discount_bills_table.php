<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_bills', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 150);
            $table->unsignedInteger('min_subtotal');      // ngưỡng tối thiểu
            $table->tinyInteger('rate')->unsigned();      // % giảm
            $table->tinyInteger('is_active')->unsigned()->default(1); // 1 active, 0 inactive

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_bills');
    }
};
