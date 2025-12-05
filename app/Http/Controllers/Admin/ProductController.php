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

    /**
     * FORM TẠO SẢN PHẨM MỚI TỪ KHO
     * Route: admin.products.create
     */
    public function create()
        {
            // Chỉ lấy dòng kho:
            // - status = 1
            // - stock_status = pending
            // - chưa có product nào
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
        // Validate dữ liệu
        $request->validate([
            'storage_detail_id' => 'required|exists:storage_details,id',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'required|exists:brands,id',

            // name: có thể để trống, nếu trống sẽ dùng tên trong kho
            'name'              => 'nullable|string|max:255',

            'description'       => 'nullable|string',
            'strap_material'    => 'nullable|string|max:100',

            // decimal(5,2) => dùng numeric
            'dial_size'         => 'nullable|numeric|min:0|max:99.99',

            'gender'            => 'nullable|in:male,female,unisex',

            'price'             => 'required|numeric|min:0',
            'quantity'          => 'required|integer|min:1',

            // Ảnh: ảnh 1 bắt buộc, 2-4 tùy chọn
            'image_1'           => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_2'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_3'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_4'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'storage_detail_id.required' => 'Vui lòng chọn sản phẩm trong kho.',
            'storage_detail_id.exists'   => 'Dòng kho không tồn tại.',
            'image_1.required'          => 'Cần ít nhất 1 ảnh chính cho sản phẩm.',
        ]);

        // Lấy dòng kho
        $detail = StorageDetail::with('storage')->findOrFail($request->storage_detail_id);

        // Không cho đăng số lượng lớn hơn số lượng trong kho
        if ($request->quantity > $detail->import_quantity) {
            return back()->withErrors([
                'quantity' => 'Số lượng bán không được lớn hơn số lượng trong kho ('
                              . $detail->import_quantity . ').'
            ])->withInput();
        }

        // Nếu không nhập name -> dùng product_name trong kho
        $finalName = $request->name ?: $detail->product_name;

        // Phòng trường hợp product_name trong kho cũng null
        if (!$finalName) {
            $finalName = 'Sản phẩm từ lô ' . ($detail->storage->batch_code ?? 'N/A');
        }

        // Tạo Product mới
        $product = Product::create([
            'name'              => $finalName,
            'description'       => $request->description,
            'strap_material'    => $request->strap_material,
            'dial_size'         => $request->dial_size,
            'gender'            => $request->gender,

            'category_id'       => $request->category_id,
            'brand_id'          => $request->brand_id,
            'storage_detail_id' => $detail->id,

            'price'             => $request->price,
            'quantity'          => $request->quantity,

            // Khi vừa đăng: đang bán & hiển thị
            'stock_status'      => 'selling',
            'status'            => 1,
        ]);

        /**
         * LƯU ẢNH VÀO BẢNG product_images
         * folder: storage/app/public/products/{id}/...
         */
        $folder = "products/{$product->id}";

        $imagesData = [
            'product_id' => $product->id,
        ];

        for ($i = 1; $i <= 4; $i++) {
            $field = "image_{$i}";
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store($folder, 'public');
                $imagesData[$field] = $path;
            }
        }

        ProductImage::create($imagesData);

        /**
         * CẬP NHẬT TRẠNG THÁI DÒNG KHO
         * Nếu đang pending (chưa bán) -> chuyển sang selling
         */
        if ($detail->stock_status === 'pending') {
            $detail->stock_status = 'selling';
            $detail->save();
        }

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

    /**
     * CẬP NHẬT SẢN PHẨM
     * Route: admin.products.update (PUT)
     */
    public function update(Request $request, $id)
    {
        $product = Product::with('productImage')->findOrFail($id);

        $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'required|exists:brands,id',

            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'strap_material'  => 'nullable|string|max:100',
            'dial_size'       => 'nullable|numeric|min:0|max:99.99',
            'gender'          => 'nullable|in:male,female,unisex',

            'price'           => 'required|numeric|min:0',
            'quantity'        => 'required|integer|min:1',

            'stock_status'    => 'nullable|in:selling,sold_out,stopped',

            'status'          => 'nullable|in:0,1',

            'image_1'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_2'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_3'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_4'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Cập nhật thông tin cơ bản
        $product->update([
            'name'           => $request->name,
            'description'    => $request->description,
            'strap_material' => $request->strap_material,
            'dial_size'      => $request->dial_size,
            'gender'         => $request->gender,

            'category_id'    => $request->category_id,
            'brand_id'       => $request->brand_id,

            'price'          => $request->price,
            'quantity'       => $request->quantity,

            'stock_status'   => $request->stock_status ?? $product->stock_status,
            'status'         => $request->status ?? $product->status,
        ]);

        /**
         * CẬP NHẬT ẢNH (NẾU CÓ UPLOAD MỚI)
         */
        $folder = "products/{$product->id}";
        $images = $product->productImage;

        // Nếu chưa có record product_images, tạo mới
        if (!$images) {
            $images = new ProductImage();
            $images->product_id = $product->id;
        }

        for ($i = 1; $i <= 4; $i++) {
            $field = "image_{$i}";
            if ($request->hasFile($field)) {
                // Xóa file cũ nếu có
                if (!empty($images[$field])) {
                    Storage::disk('public')->delete($images[$field]);
                }
                // Lưu file mới
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
        // Lấy product kèm theo dòng kho
        $product = Product::with('storageDetail')->findOrFail($id);

        // Đảo trạng thái 1 <-> 0
        $product->status = $product->status ? 0 : 1;

        // Lấy dòng kho liên quan (có thể null nếu dữ liệu cũ)
        $detail = $product->storageDetail;

        if ($product->status == 0) {
            // === ĐANG HIỂN -> ẨN ===
            // Khi ẩn sản phẩm: ngừng bán
            $product->stock_status = 'stopped';

            if ($detail) {
                $detail->stock_status = 'stopped';
                $detail->save();
            }
        } else {
            // === ĐANG ẨN -> HIỂN LẠI ===
            // Nếu còn hàng -> selling, hết hàng -> sold_out
            if ($product->quantity > 0) {
                $product->stock_status = 'selling';

                if ($detail) {
                    $detail->stock_status = 'selling';
                    $detail->save();
                }
            } else {
                $product->stock_status = 'sold_out';

                if ($detail) {
                    $detail->stock_status = 'sold_out';
                    $detail->save();
                }
            }
        }

        $product->save();

        return back()->with('success', 'Cập nhật trạng thái sản phẩm thành công.');
    }

}
