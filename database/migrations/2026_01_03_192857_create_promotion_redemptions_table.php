<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotion_redemptions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            // liên kết campaign / rule / code đã dùng
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('rule_id');
            $table->unsignedInteger('code_id')->nullable();

            // user + order
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('order_id')->nullable();

            // snapshot để audit
            $table->string('code', 50)->nullable(); // lưu lại text code phòng trường hợp code bị sửa
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('final_total')->default(0);

            // trạng thái nghiệp vụ
            // pending: tạo tạm khi bấm áp mã
            // applied: đã áp vào order
            // canceled: rollback / hủy
            $table->enum('status', ['pending', 'applied', 'canceled'])->default('applied');

            $table->timestamps();

            $table->foreign('campaign_id')
                ->references('id')
                ->on('promotion_campaigns')
                ->onDelete('cascade');

            $table->foreign('rule_id')
                ->references('id')
                ->on('promotion_rules')
                ->onDelete('cascade');

            $table->foreign('code_id')
                ->references('id')
                ->on('promotion_codes')
                ->onDelete('set null');

            // Nếu users/orders là int unsigned thì có thể bật FK.
            // Nếu bạn chưa chắc schema users/orders, có thể comment 2 FK này.
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('set null');

            $table->index(['campaign_id', 'rule_id'], 'idx_redemption_campaign_rule');
            $table->index(['user_id', 'rule_id'], 'idx_redemption_user_rule');
            $table->index(['code_id'], 'idx_redemption_code');
            $table->index(['order_id'], 'idx_redemption_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_redemptions');
    }
};
