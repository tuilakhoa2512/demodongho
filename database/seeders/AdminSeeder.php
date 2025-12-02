<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy id của role Admin
        $adminRoleId = DB::table('roles')
            ->where('name', 'Admin')
            ->value('id');

        // Nếu chưa có role Admin thì dừng, tránh lỗi FK
        if (!$adminRoleId) {
            return;
        }

        // Tạo hoặc cập nhật tài khoản admin theo email
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // điều kiện tìm

            // dữ liệu cập nhật / tạo mới
            [
                'role_id'   => $adminRoleId,
                'fullname'  => 'Administrator',
                'password'  => Hash::make('admin123'),
                'status'    => 1,

                // Các cột mới trong bảng users (nếu nullable thì để null được)
                'phone'        => null,
                'address'      => null,
                'province_id'  => null,
                'district_id'  => null,
                'ward_id'      => null,
                'image'        => null,
            ]
        );
    }
}
