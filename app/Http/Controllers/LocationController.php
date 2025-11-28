<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    // LẤY DANH SÁCH TỈNH / THÀNH PHỐ
    public function provinces()
    {
        // API cấu trúc cũ: /api/v1/provinces
        $response = Http::acceptJson()->get('https://tinhthanhpho.com/api/v1/provinces', [
            'limit' => 100,   // đủ 63 tỉnh
            'page'  => 1,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'API provinces error'], 500);
        }

        $data = $response->json();

        // API trả về { success: true, data: [...] }
        return response()->json($data['data'] ?? []);
    }

    // LẤY QUẬN/HUYỆN THEO MÃ TỈNH
    public function districts($provinceCode)
    {
        // /api/v1/provinces/{provinceCode}/districts
        $url = "https://tinhthanhpho.com/api/v1/provinces/{$provinceCode}/districts";

        $response = Http::acceptJson()->get($url, [
            'limit' => 100,
            'page'  => 1,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'API districts error'], 500);
        }

        $data = $response->json();

        return response()->json($data['data'] ?? []);
    }

    // LẤY PHƯỜNG/XÃ THEO MÃ QUẬN
    public function wards($districtCode)
    {
        // /api/v1/districts/{districtCode}/wards
        $url = "https://tinhthanhpho.com/api/v1/districts/{$districtCode}/wards";

        $response = Http::acceptJson()->get($url, [
            'limit' => 100,
            'page'  => 1,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'API wards error'], 500);
        }

        $data = $response->json();

        return response()->json($data['data'] ?? []);
    }
}
