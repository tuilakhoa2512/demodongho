<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run(): void
{

    $this->call(VietnamLocationSeeder::class);
    
    $this->call([
        RolesSeeder::class,   // chạy trước
        AdminSeeder::class,   // chạy sau
    ]);
}

}
