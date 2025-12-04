<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VietnamLocationSeeder extends Seeder
{
    private $autoCode = 1;
    private $autoName = 1;

    public function run()
    {
        $json = File::get(database_path('data/vn.json'));
        $data = json_decode($json, true);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('wards')->truncate();
        DB::table('districts')->truncate();
        DB::table('provinces')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($data as $province) {

            $province_id = DB::table('provinces')->insertGetId([
                'code' => $this->getValue($province, ['Id', 'id', 'code']) ?? $this->makeCode('P'),
                'name' => $this->getValue($province, ['Name', 'name']) ?? "Province " . $this->autoName++,
                'type' => $this->detectProvinceType(
                    $this->getValue($province, ['Name', 'name'])
                ),
            ]);

            foreach ($province['Districts'] as $district) {

                $district_id = DB::table('districts')->insertGetId([
                    'province_id' => $province_id,
                    'code' => $this->getValue($district, ['Id', 'id', 'code']) ?? $this->makeCode('D'),
                    'name' => $this->getValue($district, ['Name', 'name']) ?? "District " . $this->autoName++,
                    'type' => $this->detectDistrictType(
                        $this->getValue($district, ['Name', 'name'])
                    ),
                ]);

                foreach ($district['Wards'] as $ward) {

                    DB::table('wards')->insert([
                        'district_id' => $district_id,
                        'code' => $this->getValue($ward, ['Id', 'id', 'code']) 
                                   ?? $this->makeCode('W'),
                        'name' => $this->getValue($ward, ['Name', 'name']) 
                                   ?? "Ward " . $this->autoName++,
                        'type' => $this->convertWardType(
                            $this->getValue($ward, ['Level', 'level'])
                        ),
                    ]);
                }
            }
        }
    }

    private function getValue($array, $keys)
    {
        foreach ($keys as $key) {
            if (isset($array[$key]) && $array[$key] !== '') {
                return $array[$key];
            }
        }
        return null;
    }

    private function makeCode($prefix)
    {
        return $prefix . str_pad($this->autoCode++, 6, '0', STR_PAD_LEFT);
    }

    private function detectProvinceType($name)
    {
        if (!$name) return 'khac';
        return str_contains($name, 'Thành phố') ? 'thanh-pho' : 'tinh';
    }

    private function detectDistrictType($name)
    {
        if (!$name) return 'khac';

        return str_contains($name, 'Quận') ? 'quan'
            : (str_contains($name, 'Huyện') ? 'huyen'
            : (str_contains($name, 'Thị xã') ? 'thi-xa' : 'khac'));
    }

    private function convertWardType($level)
    {
        return match ($level) {
            'Phường' => 'phuong',
            'Xã' => 'xa',
            'Thị trấn' => 'thi-tran',
            default => 'khac',
        };
    }
}
