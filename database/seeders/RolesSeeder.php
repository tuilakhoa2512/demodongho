<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo hoặc giữ nguyên role Admin
        DB::table('roles')->updateOrInsert(
            ['name' => 'Admin'],
            []
        );

        // Tạo hoặc giữ nguyên role Customer
        DB::table('roles')->updateOrInsert(
            ['name' => 'Customer'],
            []
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'Owner'],
            []
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'Store Staff'],
            []
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'Storage Staff'],
            []
        );
    }
}
