<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\StorageDetail;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * DANH SÁCH SẢN PHẨM
     * Route: admin.products.index
     */
    public function index()
    {
        // Lấy sản phẩm kèm brand, category, lô hàng (qua storageDetail -> storage)
        $products = Product::with(['brand', 'category', 'storageDetail.storage'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with([
            'brand',
            'category',
            'productImage',
            'storageDetail.storage',
        ])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }


    /**
     * FORM TẠO SẢN PHẨM MỚI TỪ KHO
     * Route: admin.products.create
     */
   public function create()
    {
        // Chỉ lấy dòng kho:
        // - đang hiển thị (status = 1)
        // - stock_status = 'pending' (chờ bán)
        // - chưa có product nào gắn vào
        $storageDetails = StorageDetail::with('storage')
            ->where('status', 1)
            ->where('stock_status', 'pending')
            ->whereDoesntHave('product')
            ->orderByDesc('id')
            ->get();

        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.create', compact(
            'storageDetails',
            'categories',
            'brands'
        ));
    }



    /**
     * LƯU SẢN PHẨM MỚI
     * Route: admin.products.store
     */
      public function store(Request $request)
        {
            // 1. Validate (LOẠI BỎ quantity khỏi validate)
            $request->validate([
                'storage_detail_id' => 'required|exists:storage_details,id',
                'category_id'       => 'required|exists:categories,id',
                'brand_id'          => 'required|exists:brands,id',

                'name'              => 'nullable|string|max:255',
                'description'       => 'nullable|string',
                'strap_material'    => 'nullable|string|max:100',
                'dial_size'         => 'nullable|numeric|min:0|max:99.99',
                'gender'            => 'nullable|in:male,female,unisex',

                'price'             => 'required|numeric|min:0',

                'status'            => 'nullable|in:0,1',

                'image_1'           => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
                'image_2'           => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'image_3'           => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'image_4'           => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            ], [
                'storage_detail_id.required' => 'Vui lòng chọn sản phẩm trong kho.',
                'image_1.required'          => 'Cần ít nhất 1 ảnh chính cho sản phẩm.',
            ]);

            // 2. Lấy dòng kho
            $detail = StorageDetail::with('storage')->findOrFail($request->storage_detail_id);

            // Bảo vệ: chỉ cho đăng từ dòng kho còn pending
            if ($detail->stock_status !== 'pending') {
                return back()
                    ->withErrors([
                        'storage_detail_id' => 'Dòng kho này không còn trạng thái Chờ bán (pending).'
                    ])
                    ->withInput();
            }

            // 3. LẤY SỐ LƯỢNG TỪ KHO (KHÔNG CHO NHẬP TAY)
            $quantityFromStorage = (int) $detail->import_quantity;

            if ($quantityFromStorage <= 0) {
                return back()
                    ->withErrors([
                        'storage_detail_id' => 'Dòng kho này không còn số lượng khả dụng.'
                    ])
                    ->withInput();
            }

            // 4. Nếu không nhập tên -> dùng tên trong kho
            $name = $request->name ?: $detail->product_name;

            // 5. Tạo Product – quantity lấy từ kho
            $product = Product::create([
                'storage_detail_id' => $detail->id,
                'category_id'       => $request->category_id,
                'brand_id'          => $request->brand_id,
                'name'              => $name,
                'description'       => $request->description,
                'strap_material'    => $request->strap_material,
                'dial_size'         => $request->dial_size,
                'gender'            => $request->gender,
                'price'             => $request->price,

                // ✅ Số lượng bằng đúng số lượng trong kho
                'quantity'          => $quantityFromStorage,

                // Mới đăng sản phẩm => Đang bán
                'stock_status'      => 'selling',

                // Hiển thị hay không (mặc định 1 = hiển thị)
                'status'            => $request->status ?? 1,
            ]);

            // 6. Cập nhật dòng kho sang 'selling'
            $detail->stock_status = 'selling';
            $detail->save();

            // 7. Lưu ảnh
            $folder = "products/{$product->id}";
            $imagesData = [
                'product_id' => $product->id,
            ];

            for ($i = 1; $i <= 4; $i++) {
                $field = "image_{$i}";
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store($folder, 'public');
                    $imagesData["image_{$i}"] = $path;
                }
            }

            ProductImage::create($imagesData);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Đăng sản phẩm mới thành công.');
        }



    /**
     * FORM SỬA SẢN PHẨM
     * Route: admin.products.edit
     */
    public function edit($id)
    {
        $product = Product::with(['productImage', 'storageDetail.storage', 'brand', 'category'])
            ->findOrFail($id);

        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'brands'
        ));
    }

    
        // Cập nhật product
     public function update(Request $request, $id)
    {
        $product = Product::with('productImage', 'storageDetail')->findOrFail($id);

        $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'required|exists:brands,id',

            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'strap_material'  => 'nullable|string|max:100',
            'dial_size'       => 'nullable|numeric|min:0|max:99.99',
            'gender'          => 'nullable|in:male,female,unisex',

            'price'           => 'required|numeric|min:0',

            // Cho phép đổi trạng thái bán ở Product
            'stock_status'    => 'nullable|in:selling,sold_out,stopped',

            'status'          => 'nullable|in:0,1',

            'image_1'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_2'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_3'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_4'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product->update([
            'name'           => $request->name,
            'description'    => $request->description,
            'strap_material' => $request->strap_material,
            'dial_size'      => $request->dial_size,
            'gender'         => $request->gender,

            'category_id'    => $request->category_id,
            'brand_id'       => $request->brand_id,

            'price'          => $request->price,

            // Giữ hoặc cập nhật stock_status nếu có gửi lên
            'stock_status'   => $request->stock_status ?? $product->stock_status,
            'status'         => $request->status ?? $product->status,
        ]);

        if ($product->storageDetail) {
            $product->storageDetail->stock_status = $product->stock_status;
            $product->storageDetail->save();
        }

        // Cập nhật ảnh
        $folder = "products/{$product->id}";
        $images = $product->productImage;

        if (!$images) {
            $images = new ProductImage();
            $images->product_id = $product->id;
        }

        for ($i = 1; $i <= 4; $i++) {
            $field = "image_{$i}";
            if ($request->hasFile($field)) {
                // Xoá ảnh cũ nếu có
                if (!empty($images[$field])) {
                    Storage::disk('public')->delete($images[$field]);
                }
                // Lưu ảnh mới
                $path = $request->file($field)->store($folder, 'public');
                $images[$field] = $path;
            }
        }

        $images->save();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công.');
    }



    /**
     * ẨN / HIỆN SẢN PHẨM
     * Route: admin.products.toggle-status (PATCH)
     * CHỈ ĐỔI status, KHÔNG XOÁ CỨNG
     */
    public function toggleStatus($id)
    {
        // Lấy sản phẩm + dòng kho
        $product = Product::with('storageDetail')->findOrFail($id);
        $detail  = $product->storageDetail;

        // 1) SẢN PHẨM ĐANG HIỂN THỊ → BẤM LÀ ẨN
        if ($product->status == 1) {

            $product->status = 0;
            $product->stock_status = 'stopped'; 
            $product->save();

            // Đồng bộ kho → stopped
            if ($detail) {
                $detail->stock_status = 'stopped';
                $detail->save();
            }

            return redirect()
                ->back()
                ->with('success', 'Đã ẩn sản phẩm và ngừng bán.');
        }

        // 2) SẢN PHẨM ĐANG ẨN → BẤM LÀ HIỆN

        // Kho đang bị ẩn → KHÔNG CHO HIỂN
        if ($detail && $detail->status == 0) {
            return redirect()
                ->back()
                ->with('error', 'Sản phẩm trong kho đang bị ẩn, không thể hiển thị sản phẩm.');
        }

        $product->status = 1;

        // Cập nhật trạng thái bán theo tồn kho
        if ($product->quantity > 0) {
            $product->stock_status = 'selling';
        } else {
            $product->stock_status = 'sold_out';
        }

        $product->save();

        // Đồng bộ lại trạng thái kho
        if ($detail) {
            $detail->stock_status = $product->stock_status;
            $detail->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Đã hiển thị sản phẩm.');
    }

}
