<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotion_campaigns', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id'); // UNSIGNED INT AUTO_INCREMENT

            $table->string('name', 150);
            $table->text('description')->nullable();

            // Priority: so sánh GIỮA các campaign (càng lớn càng ưu tiên)
            $table->unsignedInteger('priority')->default(100);

            // thời gian chạy campaign
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();

            // trạng thái bật/tắt campaign
            $table->unsignedTinyInteger('status')->default(1); // 1=active, 0=inactive

            $table->timestamps();

            $table->index(['status', 'start_at', 'end_at'], 'idx_campaign_active_time');
            $table->index(['priority'], 'idx_campaign_priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_campaigns');
    }
};
