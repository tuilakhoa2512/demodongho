<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            // ===== THÔNG TIN NGƯỜI NHẬN =====
            $table->string('receiver_name', 150)
                  ->nullable()
                  ->after('user_id');

            $table->string('receiver_email', 150)
                  ->nullable()
                  ->after('receiver_name');

            $table->string('receiver_phone', 20)
                  ->nullable()
                  ->after('receiver_email');

            $table->string('receiver_address', 255)
                  ->nullable()
                  ->after('receiver_phone');

            // ===== ĐỊA CHỈ HÀNH CHÍNH =====
            $table->unsignedInteger('province_id')
                  ->nullable()
                  ->after('receiver_address');

            $table->unsignedInteger('district_id')
                  ->nullable()
                  ->after('province_id');

            $table->unsignedInteger('ward_id')
                  ->nullable()
                  ->after('district_id');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'receiver_name',
                'receiver_email',
                'receiver_phone',
                'receiver_address',
                'province_id',
                'district_id',
                'ward_id',
            ]);
        });
    }
};
