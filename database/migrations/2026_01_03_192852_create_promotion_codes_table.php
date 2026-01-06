<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotion_codes', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->unsignedInteger('rule_id');

            $table->string('code', 50)->unique();

            // điều kiện riêng cho code (override/ bổ sung)
            $table->unsignedInteger('min_subtotal')->nullable();
            $table->unsignedInteger('max_discount')->nullable();

            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('max_uses_per_user')->nullable();

            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();

            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('rule_id')
                ->references('id')
                ->on('promotion_rules')
                ->onDelete('cascade');

            $table->index(['rule_id', 'status'], 'idx_code_rule_status');
            $table->index(['status', 'start_at', 'end_at'], 'idx_code_active_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_codes');
    }
};
