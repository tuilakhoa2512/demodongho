<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Storage;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage as FileStorage;
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

    public function edit($id)
    {
        
        $product = Product::with(['brand', 'category', 'productImage'])->findOrFail($id);

        $brands = Brand::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }


    public function update(Request $request, $id)
    {
        
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category_id'    => 'required|exists:categories,id',
            'brand_id'       => 'required|exists:brands,id',
            'gender'         => 'nullable|in:male,female,unisex',
            'dial_size'      => 'nullable|numeric',
            'strap_material' => 'nullable|string|max:100',
            'price'          => 'required|numeric|min:0',

            'image_1'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_2'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_3'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_4'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);

        $product->update([
            'name'           => $request->name,
            'description'    => $request->description,
            'category_id'    => $request->category_id,
            'brand_id'       => $request->brand_id,
            'gender'         => $request->gender,
            'dial_size'      => $request->dial_size,
            'strap_material' => $request->strap_material,
            'price'          => $request->price,
        ]);

        $productImage = ProductImage::firstOrCreate(
            ['product_id' => $product->id],
            ['image_1' => null, 'image_2' => null, 'image_3' => null, 'image_4' => null]
        );

        // Lặp 4 ô ảnh: image_1..image_4
        for ($i = 1; $i <= 4; $i++) {
            $field = 'image_' . $i;

            if ($request->hasFile($field)) {
                // XÓA ẢNH CŨ nếu có
                $oldPath = $productImage->{$field};
                if ($oldPath) {
                    FileStorage::disk('public')->delete($oldPath);
                }

                // LƯU ẢNH MỚI
                $file  = $request->file($field);
                $path  = $file->store('products/' . $product->id, 'public'); // vd: products/5/xxx.jpg
                $productImage->{$field} = $path;
            }
            // nếu không upload file mới -> giữ nguyên ảnh cũ
        }

        $productImage->save();

        // 4. CHUYỂN HƯỚNG
        return redirect('/admin/products')->with('success', 'Cập nhật sản phẩm thành công!');
    }



}
