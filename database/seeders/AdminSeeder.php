<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanSu;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy id role theo tên
        $roles = DB::table('roles')
            ->whereIn('name', [
                'Admin',
                'Owner',
                'Store Staff',
                'Storage Staff',
            ])
            ->pluck('id', 'name'); // ['Admin' => 1, ...]

        if ($roles->isEmpty()) {
            return;
        }

        // Danh sách tài khoản nhân sự mặc định
        $nhanSus = [
            [
                'role'     => 'Admin',
                'fullname' => 'System Administrator',
                'email'    => 'admin@gmail.com',
                'password' => '123456',
            ],
            [
                'role'     => 'Owner',
                'fullname' => 'Store Owner',
                'email'    => 'owner@gmail.com',
                'password' => '123456',
            ],
            [
                'role'     => 'Store Staff',
                'fullname' => 'Store Staff',
                'email'    => 'staff@gmail.com',
                'password' => '123456',
            ],
            [
                'role'     => 'Storage Staff',
                'fullname' => 'Storage Staff',
                'email'    => 'storage@gmail.com',
                'password' => '123456',
            ],
        ];

        foreach ($nhanSus as $ns) {

            if (!isset($roles[$ns['role']])) {
                continue;
            }

            NhanSu::updateOrCreate(
                ['email' => $ns['email']], // điều kiện tìm
                [
                    'role_id'    => $roles[$ns['role']],
                    'fullname'   => $ns['fullname'],
                    'password'   => Hash::make($ns['password']),
                    'phone'      => null,
                    'status'     => 1,
                    'created_by' => null,
                ]
            );
        }
    }
}