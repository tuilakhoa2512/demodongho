<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // ❗ Drop FK trước nếu có (phòng trường hợp từng add foreign key)
            if (Schema::hasColumn('orders', 'discount_bill_id')) {
                try {
                    $table->dropForeign(['discount_bill_id']);
                } catch (\Throwable $e) {
                    // không có FK thì bỏ qua
                }
            }

            // Drop columns
            if (Schema::hasColumn('orders', 'discount_bill_id')) {
                $table->dropColumn('discount_bill_id');
            }

            if (Schema::hasColumn('orders', 'discount_bill_rate')) {
                $table->dropColumn('discount_bill_rate');
            }

            if (Schema::hasColumn('orders', 'discount_bill_value')) {
                $table->dropColumn('discount_bill_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Thêm lại nếu rollback
            if (!Schema::hasColumn('orders', 'discount_bill_id')) {
                $table->unsignedInteger('discount_bill_id')->nullable()->after('total_price');
            }

            if (!Schema::hasColumn('orders', 'discount_bill_rate')) {
                $table->unsignedTinyInteger('discount_bill_rate')->nullable()->after('discount_bill_id');
            }

            if (!Schema::hasColumn('orders', 'discount_bill_value')) {
                $table->unsignedInteger('discount_bill_value')->nullable()->after('discount_bill_rate');
            }
        });
    }
};
