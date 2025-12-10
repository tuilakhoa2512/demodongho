<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountProduct;
use Illuminate\Http\Request;

class DiscountProductController extends Controller
{
    /**
     * DANH SÁCH CHƯƠNG TRÌNH ƯU ĐÃI SẢN PHẨM
     */
    public function index()
    {
        // Lấy danh sách, sắp xếp mới nhất trước
        $discounts = DiscountProduct::orderByDesc('id')->paginate(10);

        return view('admin.discount_products.index', compact('discounts'));
    }

    /**
     * FORM THÊM MỚI ƯU ĐÃI
     */
    public function create()
    {
        return view('admin.discount_products.create');
    }

    /**
     * LƯU ƯU ĐÃI MỚI
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'rate'   => 'required|integer|min:1|max:100',
            'status' => 'nullable|in:0,1',
        ], [
            'name.required' => 'Vui lòng nhập tên chương trình.',
            'rate.required' => 'Vui lòng nhập % giảm giá.',
            'rate.min'      => 'Phần trăm giảm phải lớn hơn 0.',
            'rate.max'      => 'Phần trăm giảm không được lớn hơn 100.',
        ]);

        DiscountProduct::create([
            'name'   => $request->name,
            'rate'   => $request->rate,
            'status' => $request->status ?? 1, // mặc định đang hoạt động
        ]);

        return redirect()
            ->route('admin.discount-products.index')
            ->with('success', 'Tạo chương trình ưu đãi sản phẩm thành công.');
    }

    /**
     * FORM SỬA ƯU ĐÃI
     */
    public function edit($id)
    {
        $discount = DiscountProduct::findOrFail($id);

        return view('admin.discount_products.edit', compact('discount'));
    }

    /**
     * CẬP NHẬT ƯU ĐÃI
     */
    public function update(Request $request, $id)
    {
        $discount = DiscountProduct::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'rate'   => 'required|integer|min:1|max:100',
            'status' => 'nullable|in:0,1',
        ]);

        $discount->update([
            'name'   => $request->name,
            'rate'   => $request->rate,
            'status' => $request->status ?? $discount->status,
        ]);

        return redirect()
            ->route('admin.discount-products.index')
            ->with('success', 'Cập nhật chương trình ưu đãi sản phẩm thành công.');
    }

    /**
     * ẨN / HIỆN ƯU ĐÃI (status 1/0)
     */
    public function toggleStatus($id)
    {
        $discount = DiscountProduct::findOrFail($id);

        $discount->status = $discount->status ? 0 : 1;
        $discount->save();

        return back()->with('success', 'Cập nhật trạng thái ưu đãi sản phẩm thành công.');
    }
}
