<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotion_targets', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->unsignedInteger('rule_id');

            // all: toàn shop
            // product: theo product_id
            // category: theo category_id
            // brand: theo brand_id
            $table->enum('target_type', ['all', 'product', 'category', 'brand'])->default('all');

            // nullable khi target_type=all
            $table->unsignedInteger('target_id')->nullable();

            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('rule_id')
                ->references('id')
                ->on('promotion_rules')
                ->onDelete('cascade');

            $table->index(['rule_id', 'status'], 'idx_target_rule_status');
            $table->index(['target_type', 'target_id', 'status'], 'idx_target_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_targets');
    }
};
