<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->unsignedInteger('campaign_id');

            // scope của rule
            // product: giảm theo sản phẩm (hiển thị giá sale)
            // order: giảm theo hóa đơn (tính lúc checkout)
            $table->enum('scope', ['product', 'order']);

            // kiểu giảm
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->unsignedInteger('discount_value')->default(0);

            // --- Rule điều kiện (áp cho order rule) ---
            // subtotal tối thiểu của đơn để rule có hiệu lực
            $table->unsignedInteger('min_order_subtotal')->nullable();

            // giới hạn số tiền giảm tối đa (thường dùng cho percent)
            $table->unsignedInteger('max_discount_amount')->nullable();

            // giới hạn số lần rule được dùng (toàn hệ thống)
            $table->unsignedInteger('max_uses')->nullable();

            // số lần dùng rule / 1 user
            $table->unsignedInteger('max_uses_per_user')->nullable();

            // thời gian chạy riêng cho rule (nếu muốn override campaign)
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();

            $table->unsignedTinyInteger('status')->default(1); // 1=on,0=off

            // Priority giữa rules trong cùng 1 campaign (tùy bạn dùng hay không)
            // nếu chưa muốn dùng, vẫn để để mở rộng (default 100)
            $table->unsignedInteger('priority')->default(100);

            $table->timestamps();

            $table->foreign('campaign_id')
                ->references('id')
                ->on('promotion_campaigns')
                ->onDelete('cascade');

            $table->index(['campaign_id', 'scope', 'status'], 'idx_rule_campaign_scope_status');
            $table->index(['scope', 'status', 'start_at', 'end_at'], 'idx_rule_active_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_rules');
    }
};
