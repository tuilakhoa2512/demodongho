<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountBill;
use Illuminate\Http\Request;

class DiscountBillController extends Controller
{
    public function index()
    {
        $discountBills = DiscountBill::orderByDesc('id')->paginate(20);
        return view('admin.discount_bills.index', compact('discountBills'));
    }

    public function create()
    {
        return view('admin.discount_bills.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:150',
            'min_subtotal' => 'required|integer|min:0',
            'rate'         => 'required|integer|min:1|max:100',
            'status'       => 'nullable|in:0,1',
        ]);

        DiscountBill::create([
            'name'         => $request->name,
            'min_subtotal' => (int)$request->min_subtotal,
            'rate'         => (int)$request->rate,
            'status'       => (int)($request->status ?? 1),
        ]);

        return redirect()->route('admin.discount-bills.index')
            ->with('success', 'Đã tạo ưu đãi theo bill.');
    }

    public function edit($id)
    {
        $discountBill = DiscountBill::findOrFail($id);
        return view('admin.discount_bills.edit', compact('discountBill'));
    }

    public function update(Request $request, $id)
    {
        $discountBill = DiscountBill::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:150',
            'min_subtotal' => 'required|integer|min:0',
            'rate'         => 'required|integer|min:1|max:100',
            'status'       => 'nullable|in:0,1',
        ]);

        $discountBill->update([
            'name'         => $request->name,
            'min_subtotal' => (int)$request->min_subtotal,
            'rate'         => (int)$request->rate,
            'status'       => (int)($request->status ?? $discountBill->status),
        ]);

        return redirect()->route('admin.discount-bills.index')
            ->with('success', 'Đã cập nhật ưu đãi theo bill.');
    }

    public function toggleStatus($id)
    {
        $discountBill = DiscountBill::findOrFail($id);
        $discountBill->status = $discountBill->status ? 0 : 1;
        $discountBill->save();

        return back()->with('success', 'Đã cập nhật trạng thái ưu đãi theo bill.');
    }
}
