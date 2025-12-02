<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('discount_bills', function (Blueprint $table) {
            $table->increments('id');  

            $table->string('name', 150);
            $table->unsignedInteger('min_subtotal'); // Ngưỡng tối thiểu
            $table->unsignedTinyInteger('rate'); // % giảm giá
            $table->unsignedTinyInteger('status')->default(1); // 1=active, 0=inactive

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_bills');
    }
};
