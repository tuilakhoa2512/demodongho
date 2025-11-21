<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    // danh sách lô hàng
    public function index()
    {
        $storages = Storage::with('product')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('admin.storages.index', compact('storages'));
    }

    // form thêm lô
    public function create()
    {
        return view('admin.storages.create');
    }

    // lưu lô mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name'       => 'required|string|max:255',
            'supplier_name'      => 'nullable|string|max:255',
            'import_date'        => 'required|date',
            'import_quantity'    => 'required|integer|min:1',
            'unit_import_price'  => 'required|numeric|min:0',
        ]);

        $validated['total_import_price'] =
            $validated['import_quantity'] * $validated['unit_import_price'];

        Storage::create($validated);

        // sau khi lưu xong thì quay về danh sách
        return redirect()
            ->route('admin.storages.index')
            ->with('success', 'Thêm lô hàng mới thành công!');
    }

        //xóa lô
       public function destroy($id)
    {
        $storage = Storage::with('product')->findOrFail($id);

        if ($storage->product) {
            return redirect()->back()
                ->with('error', 'Lô hàng này đang được đăng bán, không thể xóa.');
        }

        $storage->delete();

        return redirect()->back()
            ->with('success', 'Xóa lô hàng thành công.');
    }

    public function edit($id)
    {
        
        $storage = Storage::with('product')->findOrFail($id);

        return view('admin.storages.edit', compact('storage'));
    }


    public function update(Request $request, $id)
    {
        
        $storage = Storage::with('product')->findOrFail($id);

        $rules = [
            'product_name'      => 'required|string|max:255',
            'supplier_name'     => 'nullable|string|max:255',
            'import_date'       => 'required|date',
            'unit_import_price' => 'required|numeric|min:0',
        ];

        // lô chưa đăng -> cho phép sửa SL nhập
        if (!$storage->product) {
            $rules['import_quantity'] = 'required|integer|min:1';
        }
       
        $validated = $request->validate($rules);

        if ($storage->product) {
            // đã đăng -> lấy sl nhập cũ
            $validated['import_quantity'] = $storage->import_quantity;
        } else {
            // chưa đăng → lấy từ form
            $validated['import_quantity'] = (int) $validated['import_quantity'];
        }

        // tính lại tổng nhập
        $validated['total_import_price'] =
            $validated['import_quantity'] * $validated['unit_import_price'];

        $storage->update($validated);

        return redirect()
            ->to('/admin/storages')
            ->with('success', 'Cập nhật lô hàng thành công.');
    }

}
