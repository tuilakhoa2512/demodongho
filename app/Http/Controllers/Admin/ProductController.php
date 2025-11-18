<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //ds sp
    public function index()
    {
        $products = Product::with('storage')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    // add sp
    public function create()
    {
        // lấy các lô hàng chưa có sản phẩm (chưa bị gán product)
        $storages = Storage::whereDoesntHave('product')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.products.create', compact('storages'));
    }

    // save sp
    public function store(Request $request)
    {
        $validated = $request->validate([
            'storage_id'     => 'required|exists:storages,id',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'gender'         => 'nullable|string|max:20',
            'dial_size'      => 'nullable|string|max:50',
            'strap_material' => 'nullable|string|max:100',
            'price'          => 'required|numeric|min:0',
            'status'         => 'required|in:0,1', // 1 = hiển thị, 0 = ẩn
        ]);

        Product::create($validated);

        return redirect()->to('/admin/products')
            ->with('success', 'Thêm sản phẩm mới thành công!');
    }
}
