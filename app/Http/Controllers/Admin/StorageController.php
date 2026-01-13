<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StorageController extends Controller
{
    public function index(Request $request)
    {
        // Lấy trạng thái lọc từ URL ?status=1 hoặc 0
        $filterStatus = $request->input('status');

        $query = Storage::query()->orderByDesc('id');

        if ($filterStatus !== null && $filterStatus !== '') {
            $query->where('status', $filterStatus);
        }

        $storages = $query->paginate(10)->appends($request->query());

        return view('admin.storages.index', [
            'storages' => $storages,
            'filterStatus' => $filterStatus
        ]);
    }


    public function create()
    {
        return view('admin.storages.create');
    }

   public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{L}\s]+$/u'
            ],
            'supplier_email' => [
                'nullable',
                'email',
                'max:30',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
            ],
            'import_date' => [
                'nullable',
                'date'
            ],
            'note' => [
                'nullable',
                'string',
                'max:200'
            ],
        ], [
            'supplier_name.regex' =>
                'Tên nhà cung cấp chỉ được chứa chữ cái và khoảng trắng',
            'supplier_email.regex' =>
                'Email phải có đuôi @gmail.com',
        ]);        

        $year = Carbon::now()->year;
        $sequence = (Storage::max('id') ?? 0) + 1;
        $batchCode = "LH{$year}-{$sequence}";

        Storage::create([
            'batch_code'     => $batchCode,
            'supplier_name'  => $request->supplier_name,
            'supplier_email' => $request->supplier_email,
            'import_date'    => $request->import_date ?? Carbon::now(),
            'note'           => $request->note,
            'status'         => 1,
        ]);
        
        return redirect()->route('admin.storages.index')
            ->with('success', 'Tạo lô hàng thành công. Mã lô: '.$batchCode);
    }

    public function edit($id)
    {
        $storage = Storage::findOrFail($id);

        return view('admin.storages.edit', compact('storage'));
    }

    public function update(Request $request, $id)
    {
        $storage = Storage::findOrFail($id);

        $request->validate([
            'supplier_name'  => 'nullable|string|max:50',
            'supplier_email' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
            ],
            'import_date'    => 'nullable|date',
            'note'           => 'nullable|string|max:200',
        ], [
            'supplier_email.regex' => 'Email phải có đuôi @gmail.com',
        ]);

        // batch_code KHÔNG sửa ở đây, chỉ sửa các thông tin khác
        $storage->update([
            'supplier_name'  => $request->supplier_name,
            'supplier_email' => $request->supplier_email,
            'import_date'    => $request->import_date ?? $storage->import_date,
            'note'           => $request->note,
        ]);

        return redirect()
            ->route('admin.storages.index')
            ->with('success', 'Cập nhật lô hàng thành công.');
    }

    // Ẩn / hiện lô hàng bằng status
    public function toggleStatus($id)
    {
        $storage = Storage::findOrFail($id);
        $storage->status = $storage->status ? 0 : 1;
        $storage->save();

        return redirect()->back()
            ->with('success', 'Cập nhật trạng thái lô hàng thành công.');
    }
}
