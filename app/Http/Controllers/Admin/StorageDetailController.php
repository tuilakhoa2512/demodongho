<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use App\Models\StorageDetail;
use Illuminate\Http\Request;

class StorageDetailController extends Controller
{
    
   public function index(Request $request)
    {
        // Lấy storage_id từ query (?storage_id=...)
        $selectedStorageId = $request->input('storage_id');

        // Base query
        $query = StorageDetail::with('storage');

        // Nếu có chọn lô thì lọc theo lô
        if (!empty($selectedStorageId)) {
            $query->where('storage_id', $selectedStorageId);
        }

        $details = $query->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query()); // để khi phân trang vẫn giữ param lọc

        // Lấy danh sách lô để đổ vào select
        $storages = Storage::orderByDesc('id')->get(['id', 'batch_code']);

        return view('admin.storage_details.index_all', compact(
            'details',
            'storages',
            'selectedStorageId'
        ));
    }

    /**
     * DANH SÁCH KHO THEO 1 LÔ HÀNG
     * GET /admin/storages/{storageId}/details
     * Route: admin.storage-details.by-storage
     */
    public function indexByStorage($storageId)
    {
        $storage = Storage::findOrFail($storageId);

        $details = StorageDetail::where('storage_id', $storageId)
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.storage_details.index', compact('storage', 'details'));
    }

    /**
     * FORM THÊM SẢN PHẨM VÀO 1 LÔ
     * GET /admin/storages/{storageId}/details/create
     * Route: admin.storage-details.create
     */
    public function create($storageId)
    {
        $storage = Storage::findOrFail($storageId);

        return view('admin.storage_details.create', compact('storage'));
    }

    /**
     * LƯU SẢN PHẨM VÀO LÔ
     * POST /admin/storages/{storageId}/details
     * Route: admin.storage-details.store
     */
    public function store(Request $request, $storageId)
    {
        $storage = Storage::findOrFail($storageId);

        $request->validate([
            'product_name'    => 'required|string|max:255',
            'import_quantity' => 'required|integer|min:1',
            'stock_status'    => 'nullable|in:pending,selling,sold_out,stopped',
            'note'            => 'nullable|string',
        ]);

        StorageDetail::create([
            'storage_id'      => $storage->id,
            'product_name'    => $request->product_name,
            'import_quantity' => $request->import_quantity,
            'stock_status'    => $request->stock_status ?? 'pending',
            'note'            => $request->note,
            'status'          => 1, // mặc định hiển thị
        ]);

        return redirect()
            ->route('admin.storage-details.by-storage', $storage->id)
            ->with('success', 'Thêm sản phẩm vào lô hàng thành công.');
    }

    /**
     * FORM SỬA 1 DÒNG KHO
     * GET /admin/storage-details/{id}/edit
     * Route: admin.storage-details.edit
     */
    public function edit($id)
    {
        $detail  = StorageDetail::findOrFail($id);
        $storage = $detail->storage;

        return view('admin.storage_details.edit', compact('detail', 'storage'));
    }

    /**
     * CẬP NHẬT 1 DÒNG KHO
     * PUT /admin/storage-details/{id}
     * Route: admin.storage-details.update
     */
    public function update(Request $request, $id)
    {
        $detail  = StorageDetail::findOrFail($id);
        $storage = $detail->storage;

        $request->validate([
            'product_name'    => 'required|string|max:255',
            'import_quantity' => 'required|integer|min:0',
            'stock_status'    => 'required|in:pending,selling,sold_out,stopped',
            'note'            => 'nullable|string',
        ]);

        $detail->update([
            'product_name'    => $request->product_name,
            'import_quantity' => $request->import_quantity,
            'stock_status'    => $request->stock_status,
            'note'            => $request->note,
        ]);

        return redirect()
            ->route('admin.storage-details.by-storage', $storage->id)
            ->with('success', 'Cập nhật sản phẩm trong lô thành công.');
    }

    /**
     * ẨN / HIỆN 1 DÒNG KHO
     * PATCH /admin/storage-details/{id}/toggle-status
     * Route: admin.storage-details.toggle-status
     */
    public function toggleStatus($id)
    {
        $detail = StorageDetail::findOrFail($id);

        $detail->status = $detail->status ? 0 : 1;
        $detail->save();

        return redirect()
            ->back()
            ->with('success', 'Cập nhật trạng thái sản phẩm trong kho thành công.');
    }

    
    public function listPending(Request $request)
    {
        $details = StorageDetail::with('storage')
            ->where('stock_status', 'pending')
            ->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => null,
            'currentStatus' => 'pending'
        ]);
    }

    public function listSelling(Request $request)
    {
        $details = StorageDetail::with('storage')
            ->where('stock_status', 'selling')
            ->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => null,
            'currentStatus' => 'selling'
        ]);
    }

    public function listSoldOut(Request $request)
    {
        $details = StorageDetail::with('storage')
            ->where('stock_status', 'sold_out')
            ->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => null,
            'currentStatus' => 'sold_out'
        ]);
    }

    public function listStopped(Request $request)
    {
        $details = StorageDetail::with('storage')
            ->where('stock_status', 'stopped')
            ->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => null,
            'currentStatus' => 'stopped'
        ]);
    }

}
