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

//  NEW promotion system
use App\Services\PromotionService;

class ProductController extends Controller
{
    /**
     * ADMIN - danh sách sản phẩm
     *  Không join ưu đãi cũ
     *  Giá final runtime từ PromotionService
     */
    public function index(PromotionService $promoService)
    {
        $products = Product::query()
            ->with([
                'brand',
                'category',
                'storageDetail.storage',
                'productImage',
            ])
            ->orderByDesc('id')
            ->paginate(5);
            

        foreach ($products as $p) {
            $pack = $promoService->calcProductFinalPrice($p);

            $final = (float)($pack['final_price'] ?? $p->price);
            $base  = (float)($p->price ?? 0);

            // runtime fields cho view
            $p->final_price = (int)$final;

            // chỉ coi là "sale" khi final < base
            $p->promo_has_sale = ($final > 0 && $base > 0 && $final < $base) ? 1 : 0;

            // meta promo (nếu có)
            $campaign = $pack['campaign'] ?? null;
            $rule     = $pack['rule'] ?? null;

            $p->promo_name            = $campaign?->name;
            $p->promo_discount_type   = $rule?->discount_type;     // percent|fixed|null
            $p->promo_discount_value  = $rule ? (int)$rule->discount_value : null;
            $p->promo_label           = $pack['promo_label'] ?? null;

            // discount amount: ưu tiên pack, fallback base-final
            $p->promo_discount_amount = (int)($pack['discount_amount'] ?? max(0, (int)$base - (int)$final));
        }

        return view('admin.products.index', compact('products'));
    }

    /**
     * ADMIN - xem chi tiết sản phẩm
     *  Không join ưu đãi cũ
     *  Giá final runtime từ PromotionService
     */
    public function show($id, PromotionService $promoService)
    {
        $product = Product::with([
            'brand',
            'category',
            'productImage',
            'storageDetail.storage',
        ])->findOrFail($id);

        $pack = $promoService->calcProductFinalPrice($product);

        $final = (float)($pack['final_price'] ?? $product->price);
        $base  = (float)($product->price ?? 0);

        $product->final_price = (int)$final;
        $product->promo_has_sale = ($final > 0 && $base > 0 && $final < $base) ? 1 : 0;

        $campaign = $pack['campaign'] ?? null;
        $rule     = $pack['rule'] ?? null;

        $product->promo_name            = $campaign?->name;
        $product->promo_discount_type   = $rule?->discount_type;
        $product->promo_discount_value  = $rule ? (int)$rule->discount_value : null;
        $product->promo_label           = $pack['promo_label'] ?? null;
        $product->promo_discount_amount = (int)($pack['discount_amount'] ?? max(0, (int)$base - (int)$final));

        // Reviews
        $reviews = Review::where('product_id', $id)
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->get();

        $averageRating = round(
            (float) Review::where('product_id', $id)
                ->where('status', 1)
                ->avg('rating'),
            1
        );

        return view('admin.products.show', compact('product', 'reviews', 'averageRating'));
    }

    // =========================
    // CRUD 
    // =========================

    public function create()
    {
        $storageDetails = StorageDetail::with('storage')
            ->where('status', 1)
            ->where('stock_status', 'pending')
            ->whereDoesntHave('product')
            ->orderByDesc('id')
            ->get();

        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands     = Brand::where('status', 1)->orderBy('name')->get();

        return view('admin.products.create', compact('storageDetails', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'storage_detail_id' => ['required', 'exists:storage_details,id'],
            'category_id'       => ['required', 'exists:categories,id'],
            'brand_id'          => ['required', 'exists:brands,id'],

            'name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}0-9\s\-]+$/u'
            ],

            'description' => ['nullable', 'string', 'max:2000'],

            'strap_material' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-]+$/u'
            ],

            'dial_size' => ['nullable', 'numeric', 'min:0', 'max:99.99'],
            'gender'    => ['nullable', 'in:male,female,unisex'],

            'price'  => ['required', 'numeric', 'min:0', 'max: 99999999'],
            'status' => ['nullable', 'in:0,1'],

            'image_1' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'image_2' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'name.regex'           => 'Tên sản phẩm chỉ được chứa chữ cái, số và dấu gạch ngang',
            'strap_material.regex' => 'Chất liệu dây chỉ được chứa chữ cái và khoảng trắng',
            'image_1.required'     => 'Cần ít nhất 1 ảnh chính cho sản phẩm',
        ]);
        
        $detail = StorageDetail::with('storage', 'product')->findOrFail($request->storage_detail_id);

        if ((int)$detail->status !== 1) {
            return back()->withErrors(['storage_detail_id' => 'Dòng kho này đang bị ẩn, không thể đăng bán.'])->withInput();
        }

        if ($detail->product) {
            return back()->withErrors(['storage_detail_id' => 'Dòng kho này đã được đăng bán (đã có sản phẩm).'])->withInput();
        }

        if ($detail->stock_status !== 'pending') {
            return back()->withErrors(['storage_detail_id' => 'Dòng kho này không còn trạng thái Chờ bán (pending).'])->withInput();
        }

        $quantityFromStorage = (int)$detail->import_quantity;
        if ($quantityFromStorage <= 0) {
            return back()->withErrors(['storage_detail_id' => 'Dòng kho này không còn số lượng khả dụng.'])->withInput();
        }

        $name = $request->name ?: $detail->product_name;

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
            'quantity'          => $quantityFromStorage,
            'stock_status'      => 'selling',
            'status'            => $request->status ?? 1,
        ]);

        $detail->stock_status = 'selling';
        $detail->save();

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

        return redirect()->route('admin.products.index')->with('success', 'Đăng sản phẩm mới thành công.');
    }

    public function edit($id)
    {
        $product = Product::with(['productImage', 'storageDetail.storage', 'brand', 'category'])->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('productImage', 'storageDetail')->findOrFail($id);

        $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id'
            ],
            'brand_id' => [
                'required',
                'exists:brands,id'
            ],
        
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}0-9\s\-]+$/u'
            ],
        
            'description' => [
                'nullable',
                'string',
                'max:2000'
            ],
        
            'strap_material' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-]+$/u'
            ],
        
            'dial_size' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99.99'
            ],
        
            'gender' => [
                'nullable',
                'in:male,female,unisex'
            ],
        
            'price' => [
                'required',
                'numeric',
                'min:0'
            ],
        
            'stock_status' => [
                'nullable',
                'in:selling,sold_out,stopped'
            ],
        
            'status' => [
                'nullable',
                'in:0,1'
            ],
        
            'image_1' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

        if ($product->storageDetail) {
            $product->storageDetail->stock_status = $product->stock_status;
            $product->storageDetail->save();
        }

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

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function toggleStatus($id)
    {
        $product = Product::with('storageDetail')->findOrFail($id);
        $detail  = $product->storageDetail;

        /**
         * ❌ KHÔNG CHO HIỆN LẠI KHI ĐÃ HẾT HÀNG
         */
        if ((int)$product->quantity <= 0) {
            return redirect()
                ->back()
                ->with('error', 'Sản phẩm đã hết hàng, không thể hiển thị lại.');
        }

        /**
         * ĐANG HIỆN => ẨN
         */
        if ((int)$product->status === 1) {

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

        /**
         * ĐANG ẨN =>  HIỆN (CHỈ KHI CÒN HÀNG)
         */
        $product->status = 1;
        $product->stock_status = 'selling';
        $product->save();

        if ($detail) {
            $detail->stock_status = 'selling';
            $detail->save();
        }

        return redirect()
            ->back()
            ->with('success', 'Đã hiển thị sản phẩm.');
    }

}
