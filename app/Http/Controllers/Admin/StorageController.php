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
            ->orderBy('id', 'desc')
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
}
