<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('storage_details', function (Blueprint $table) {
            $table->increments('id');  
            $table->unsignedInteger('storage_id');
            $table->string('product_name', 255)->nullable();
            $table->unsignedInteger('import_quantity')->nullable();
            $table->enum('stock_status', ['pending', 'selling', 'sold_out', 'stopped'])->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
            $table->timestamps();

            $table->foreign('storage_id')->references('id')->on('storages')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('storage_details');
    }
};
