<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountProductController extends Controller
{
    public function index()
    {
        $discounts = DiscountProduct::orderByDesc('id')->paginate(20);
        return view('admin.discount_products.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discount_products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'rate'   => 'required|integer|min:1|max:100',
            'status' => 'nullable|in:0,1',
        ]);

        DiscountProduct::create([
            'name'   => $request->name,
            'rate'   => $request->rate,
            'status' => $request->status ?? 1,
        ]);

        return redirect()
            ->route('admin.discount-products.index')
            ->with('success', 'Tạo chương trình ưu đãi sản phẩm thành công.');
    }

    public function edit($id)
    {
        $discount = DiscountProduct::findOrFail($id);
        return view('admin.discount_products.edit', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $discount = DiscountProduct::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|integer|min:1|max:100',
        ]);

        $discount->update([
            'name' => $request->name,
            'rate' => $request->rate,
        ]);

        return redirect()
            ->route('admin.discount-products.index')
            ->with('success', 'Cập nhật chương trình ưu đãi thành công.');
    }

    /**
     * RÀNG BUỘC QUAN TRỌNG:
     * - Tắt DiscountProduct => tất cả pivot status = 0 (tạm ngừng)
     * - Bật DiscountProduct => pivot còn hạn => status = 1, hết hạn => status = 0
     */
    public function toggleStatus($id)
    {
        $discount = DiscountProduct::findOrFail($id);

        $newStatus = $discount->status ? 0 : 1;
        $discount->status = $newStatus;
        $discount->save();

        // Đồng bộ pivot theo trạng thái chương trình
        if ($newStatus == 0) {
            // Tắt chương trình => tắt hết pivot
            DB::table('discount_product_details')
                ->where('discount_product_id', $discount->id)
                ->update(['status' => 0, 'updated_at' => now()]);
        } else {
            // Bật lại chương trình:
            // - còn hạn (expiration_date null OR >= today) => status=1
            // - hết hạn (expiration_date < today) => status=0
            $today = now()->toDateString();

            DB::table('discount_product_details')
                ->where('discount_product_id', $discount->id)
                ->where(function ($q) use ($today) {
                    $q->whereNull('expiration_date')
                      ->orWhere('expiration_date', '>=', $today);
                })
                ->update(['status' => 1, 'updated_at' => now()]);

            DB::table('discount_product_details')
                ->where('discount_product_id', $discount->id)
                ->whereNotNull('expiration_date')
                ->where('expiration_date', '<', $today)
                ->update(['status' => 0, 'updated_at' => now()]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái chương trình ưu đãi.');
    }
}
