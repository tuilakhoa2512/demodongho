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
        Schema::create('storages', function (Blueprint $table) {
            $table->increments('id');   // INT(10) UNSIGNED AUTO_INCREMENT - Primary Key

            $table->string('batch_code', 50)->unique(); // VARCHAR(50) UNIQUE

            $table->string('supplier_name', 255)->nullable(); // VARCHAR(255)
            $table->string('supplier_email', 100)->nullable(); // VARCHAR(100)

            $table->timestamp('import_date')->useCurrent(); // TIMESTAMP DEFAULT CURRENT_TIMESTAMP

            $table->text('note')->nullable(); // TEXT

            $table->tinyInteger('status')->unsigned()->default(1); // TINYINT(3) UNSIGNED (1=active,0=inactive)

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
};
