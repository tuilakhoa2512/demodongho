<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Storage;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
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

            'image_1'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_2'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_3'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_4'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
 
        $storage = Storage::findOrFail($request->storage_id);
        $validated['quantity'] = $storage->import_quantity;
      
        // Product::create($validated);

        $product = Product::create($validated);

        $productImage = new ProductImage();
        $productImage->product_id = $product->id;

            // tạo thư mục theo product_id
            // products/5/xxx.jpg
            if ($request->hasFile('image_1')) {
                $path1 = $request->file('image_1')->store('products/' . $product->id, 'public');
                $productImage->image_1 = $path1;
            }

            if ($request->hasFile('image_2')) {
                $path2 = $request->file('image_2')->store('products/' . $product->id, 'public');
                $productImage->image_2 = $path2;
            }

            if ($request->hasFile('image_3')) {
                $path3 = $request->file('image_3')->store('products/' . $product->id, 'public');
                $productImage->image_3 = $path3;
            }

            if ($request->hasFile('image_4')) {
                $path4 = $request->file('image_4')->store('products/' . $product->id, 'public');
                $productImage->image_4 = $path4;
            }

            // save khi có ít nhất 1 ảnh
            if ($productImage->image_1 || $productImage->image_2 || $productImage->image_3 || $productImage->image_4) {
                $productImage->save();
            }

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
