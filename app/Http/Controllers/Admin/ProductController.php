<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Storage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ds sp
    public function index()
    {
        $products = Product::with(['storage', 'category', 'brand'])
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    // add sp
    public function create()
    {
        // Lô kho chưa có sản phẩm
        $storages = Storage::whereDoesntHave('product')
            ->orderBy('id', 'desc')
            ->get();
      
        $productTypes = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();

        return view('admin.products.create', compact('storages', 'productTypes', 'brands'));
    }

    // lưu sp
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'storage_id'   => 'required|exists:storages,id',
            'category_id'  => 'required|exists:categories,id',
            'brand_id'     => 'required|exists:brands,id',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'gender'       => 'nullable|string|max:20',
            'dial_size'      => 'nullable|numeric|min:0',
            'strap_material' => 'nullable|string|max:100',
            'price'        => 'required|numeric|min:0',
        ]);
 
        $storage = Storage::findOrFail($request->storage_id);
        $validated['quantity'] = $storage->import_quantity;
      
        Product::create($validated);

        return redirect()->to('/admin/products')
                        ->with('success', 'Thêm sản phẩm thành công!');
    }


    //xoa sp
        public function destroy($id)
    {
        // tim, ko co thi bao 404
        $product = Product::findOrFail($id);

        $product->delete();

        // qlai dq
        return redirect()->to('/admin/products')
                        ->with('success', 'Xoá sản phẩm thành công!');
    }


}
