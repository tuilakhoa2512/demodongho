<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\StorageDetail;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * DANH SÁCH SẢN PHẨM
     * Route: admin.products.index
     */
    public function index()
    {
        $today = now()->toDateString();

        // ✅ Lấy sản phẩm + join ưu đãi đang áp dụng (nếu có)
        // Điều kiện ưu đãi được tính là "đang áp dụng":
        // - dp.status = 1 (chương trình ưu đãi đang bật)
        // - dpd.status = 1 (chi tiết đang áp dụng)
        // - expiration_date null hoặc >= hôm nay
        $products = Product::query()
            ->with([
                'brand',
                'category',
                'storageDetail.storage',
                'productImage',
            ])
            ->leftJoin('discount_product_details as dpd', function ($join) use ($today) {
                $join->on('dpd.product_id', '=', 'products.id')
                    ->where('dpd.status', 1)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('dpd.expiration_date')
                          ->orWhere('dpd.expiration_date', '>=', $today);
                    });
            })
            ->leftJoin('discount_products as dp', function ($join) {
                $join->on('dp.id', '=', 'dpd.discount_product_id')
                     ->where('dp.status', 1);
            })
            ->select([
                'products.*',
                DB::raw('dp.id as discount_id'),
                DB::raw('dp.name as discount_name'),
                DB::raw('dp.rate as discount_rate'),
            ])
            ->orderByDesc('products.id')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * CHI TIẾT SẢN PHẨM
     * Route: admin.products.show
     */
    public function show($id)
    {
        $product = Product::with([
            'brand',
            'category',
            'productImage',
            'storageDetail.storage',
            'discountProducts',
        ])->findOrFail($id);
        // Lấy review của sản phẩm
        $reviews = Review::where('product_id', $id)
                    ->where('status', 1)
                    ->orderByDesc('created_at')
                    ->get();

        // Tính điểm trung bình
        $averageRating = round(Review::where('product_id', $id)
                        ->where('status', 1)
                        ->avg('rating'), 1);

        return view('admin.products.show', compact('product','reviews', 'averageRating'));
    }

    /**
     * FORM TẠO SẢN PHẨM MỚI TỪ KHO
     * Route: admin.products.create
     */
    public function create()
    {
        
        // Chỉ lấy dòng kho:
        // - đang hiển thị (status = 1)
        // - stock_status = pending
        // - chưa có product
        $storageDetails = StorageDetail::with('storage')
            ->where('status', 1)
            ->where('stock_status', 'pending')
            ->whereDoesntHave('product')
            ->orderByDesc('id')
            ->get();

            $categories = Category::where('status', 1)
            ->orderBy('name')
            ->get();
    
        $brands = Brand::where('status', 1)
            ->orderBy('name')
            ->get();
            
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
            'image_1.required'           => 'Cần ít nhất 1 ảnh chính cho sản phẩm.',
        ]);

        // 2) Lấy dòng kho
        $detail = StorageDetail::with('storage', 'product')->findOrFail($request->storage_detail_id);

        // ✅ Chặn: kho đang ẩn
        if ((int)$detail->status !== 1) {
            return back()
                ->withErrors(['storage_detail_id' => 'Dòng kho này đang bị ẩn, không thể đăng bán.'])
                ->withInput();
        }

        // ✅ Chặn: đã có product rồi
        if ($detail->product) {
            return back()
                ->withErrors(['storage_detail_id' => 'Dòng kho này đã được đăng bán (đã có sản phẩm).'])
                ->withInput();
        }

        // ✅ Chỉ cho đăng từ pending
        if ($detail->stock_status !== 'pending') {
            return back()
                ->withErrors(['storage_detail_id' => 'Dòng kho này không còn trạng thái Chờ bán (pending).'])
                ->withInput();
        }

        // 3) Lấy số lượng từ kho
        $quantityFromStorage = (int)$detail->import_quantity;

        if ($quantityFromStorage <= 0) {
            return back()
                ->withErrors(['storage_detail_id' => 'Dòng kho này không còn số lượng khả dụng.'])
                ->withInput();
        }

        // 4) Nếu không nhập tên -> dùng tên trong kho
        $name = $request->name ?: $detail->product_name;

        // 5) Tạo Product
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

            // ✅ quantity = import_quantity
            'quantity'          => $quantityFromStorage,

            // ✅ mới đăng => đang bán
            'stock_status'      => 'selling',

            // ✅ mặc định hiển thị
            'status'            => $request->status ?? 1,
        ]);

        // 6) Đồng bộ kho sang selling
        $detail->stock_status = 'selling';
        $detail->save();

        // 7) Lưu ảnh
        $folder = "products/{$product->id}";
        $imagesData = ['product_id' => $product->id];

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

    /**
     * UPDATE SẢN PHẨM
     * Route: admin.products.update
     */
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

            // Cho phép đổi trạng thái bán
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

            'stock_status'   => $request->stock_status ?? $product->stock_status,
            'status'         => $request->status ?? $product->status,
        ]);

        // 🔁 Đồng bộ stock_status sang kho
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
                if (!empty($images[$field])) {
                    Storage::disk('public')->delete($images[$field]);
                }
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
     */
    public function toggleStatus($id)
    {
        $product = Product::with('storageDetail')->findOrFail($id);
        $detail  = $product->storageDetail;

        // 1) ĐANG HIỂN THỊ -> ẨN
        if ($product->status == 1) {

            $product->status = 0;
            $product->stock_status = 'stopped';
            $product->save();

            if ($detail) {
                $detail->stock_status = 'stopped';
                $detail->save();
            }

            return redirect()
                ->back()
                ->with('success', 'Đã ẩn sản phẩm và ngừng bán.');
        }

        // 2) ĐANG ẨN -> HIỆN

        // Kho đang ẩn -> KHÔNG cho hiện
        if ($detail && $detail->status == 0) {
            return redirect()
                ->back()
                ->with('error', 'Sản phẩm trong kho đang bị ẩn, không thể hiển thị sản phẩm.');
        }

        $product->status = 1;

        // cập nhật trạng thái bán theo tồn kho
        if ($product->quantity > 0) {
            $product->stock_status = 'selling';
        } else {
            $product->stock_status = 'sold_out';
        }

        $product->save();

        // đồng bộ kho
        if ($detail) {
            $detail->stock_status = $product->stock_status;
            $detail->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Đã hiển thị sản phẩm.');
    }
}
