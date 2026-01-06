<?php

// database/migrations/xxxx_xx_xx_add_used_at_to_promotion_redemptions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('promotion_redemptions', function (Blueprint $table) {
            $table->timestamp('used_at')->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_redemptions', function (Blueprint $table) {
            $table->dropColumn('used_at');
        });
    }
};


