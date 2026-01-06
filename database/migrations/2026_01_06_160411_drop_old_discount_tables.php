<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('discount_product_details');
        Schema::dropIfExists('discount_products');
        Schema::dropIfExists('discount_bills');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // ❌ Không cần rollback hệ cũ
        // Cố tình để trống
    }
};


