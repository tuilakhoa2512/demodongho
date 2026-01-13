<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use App\Models\StorageDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageDetailController extends Controller
{

    
     // Subquery: tổng số lượng đã bán theo product_id (chỉ tính các đơn KHÔNG bị hủy)
   

    private function soldQtySubQuery()
    {
        return DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->select('od.product_id', DB::raw('SUM(od.quantity) as sold_qty'))
            // Các trạng thái được tính là "đã bán / đã đặt" (không tính canceled)
            ->whereIn('o.status', ['pending', 'confirmed', 'shipping', 'success'])
            ->groupBy('od.product_id');
    }

    
     // Query chung cho index + các list theo stock_status
     

    private function buildIndexQuery(Request $request, ?string $onlyStockStatus = null)
    {
        $selectedStorageId = $request->input('storage_id');

        $soldSub = $this->soldQtySubQuery();

        $query = StorageDetail::query()
            ->with(['storage', 'product']) // để dùng optional($detail->storage)->batch_code và $detail->product
            // join product theo khóa đúng: products.storage_detail_id = storage_details.id
            ->leftJoin('products as p', 'p.storage_detail_id', '=', 'storage_details.id')
            // join sold subquery theo product_id
            ->leftJoinSub($soldSub, 'sold', function ($join) {
                $join->on('sold.product_id', '=', 'p.id');
            })
            ->addSelect(
                'storage_details.*',
                DB::raw('COALESCE(p.quantity, 0) as selling_qty'),
                DB::raw('COALESCE(sold.sold_qty, 0) as sold_qty')
            )
            ->orderByDesc('storage_details.id');

        if (!empty($selectedStorageId)) {
            $query->where('storage_details.storage_id', $selectedStorageId);
        }

        if (!empty($onlyStockStatus)) {
            $query->where('storage_details.stock_status', $onlyStockStatus);
        }

        return [$query, $selectedStorageId];
    }

    public function index(Request $request)
    {
        [$query, $selectedStorageId] = $this->buildIndexQuery($request);

        $details = $query->paginate(5)->appends($request->query());

        $storages = Storage::orderByDesc('id')->get(['id', 'batch_code']);

        return view('admin.storage_details.index_all', compact(
            'details',
            'storages',
            'selectedStorageId'
        ));
    }


    public function indexByStorage(Request $request, $storageId)
    {
        $storage = Storage::findOrFail($storageId);

        // ép storage_id vào request để dùng chung buildIndexQuery()
        $request->merge(['storage_id' => $storageId]);

        [$query] = $this->buildIndexQuery($request);

        $details = $query
            ->paginate(10)
            ->appends($request->query());

        return view('admin.storage_details.index', compact('storage', 'details'));
    }


    public function create($storageId)
    {
        $storage = Storage::findOrFail($storageId);
        return view('admin.storage_details.create', compact('storage'));
    }

    public function store(Request $request, $storageId)
    {
        $storage = Storage::findOrFail($storageId);

        $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}0-9\s\-]+$/u'
            ],
            'import_quantity' => [
                'required',
                'integer',
                'min:1'
            ],
            'note' => [
                'nullable',
                'string',
                'max:300'
            ],
        ], [
            'product_name.regex' =>
                'Tên sản phẩm chỉ được chứa chữ cái, số, dấu gạch ngang và khoảng trắng',
        ]);        

        StorageDetail::create([
            'storage_id'      => $storage->id,
            'product_name'    => $request->product_name,
            'import_quantity' => $request->import_quantity,
            'stock_status'    => 'pending',
            'note'            => $request->note,
            'status'          => 1,
        ]);

        return redirect()
            ->route('admin.storage-details.by-storage', $storage->id)
            ->with('success', 'Thêm sản phẩm vào lô hàng thành công.');
    }

    public function edit($id)
    {
        $detail  = StorageDetail::findOrFail($id);
        $storage = $detail->storage;

        return view('admin.storage_details.edit', compact('detail', 'storage'));
    }

    public function update(Request $request, $id)
    {
        $detail  = StorageDetail::with(['storage', 'product'])->findOrFail($id);
        $storage = $detail->storage;

        // chặn sửa khi sold out
        if ($detail->stock_status === 'sold_out') {
            return redirect()
                ->back()
                ->with('error', 'Sản phẩm đã bán hết, không thể chỉnh sửa số lượng nhập.');
        }

        //tính sl đang + đã bán
        $sellingQty = (int) ($detail->selling_qty ?? 0);
        $soldQty    = (int) ($detail->sold_qty ?? 0);
        $minImport  = max(1, $sellingQty + $soldQty);

        // giới hạn max sl
        $maxImport = 10000;

        $rules = [
            'product_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}0-9\s\-]+$/u'
            ],
            'import_quantity' => [
                'required',
                'integer',
                'min:' . $minImport,
                'max:' . $maxImport,
            ],
            'note' => [
                'nullable',
                'string',
                'max:300'
            ],
        ];
        
        $messages = [
            'product_name.regex' =>
                'Tên sản phẩm chỉ được chứa chữ cái, số và dấu gạch ngang',
            'import_quantity.min' =>
                'Số lượng nhập phải ≥ ' . $minImport,
            'import_quantity.max' =>
                'Số lượng nhập không được vượt quá ' . number_format($maxImport),
        ];

        $request->validate($rules, $messages);

        $detail->update([
            'product_name'    => $request->product_name,
            'import_quantity' => $request->import_quantity,
            'note'            => $request->note,
        ]);

        return redirect()
            ->route('admin.storage-details.by-storage', $storage->id)
            ->with('success', 'Cập nhật sản phẩm trong kho thành công.');
    }


    public function toggleStatus($id)
    {
        $detail  = StorageDetail::with('product')->findOrFail($id);
        $product = $detail->product;

        /**
         * ❌ NẾU SẢN PHẨM ĐÃ SOLD OUT → KHÔNG CHO TOGGLE
         * (vì sold_out là trạng thái khóa)
         */
        if ($product && (int)$product->quantity <= 0) {
            return redirect()
                ->back()
                ->with('error', 'Sản phẩm đã bán hết, không thể thay đổi trạng thái kho.');
        }

        // toggle Ẩn / Hiện kho
        $detail->status = $detail->status ? 0 : 1;

        /**
         * KHO BỊ ẨN
         * → Ẩn sản phẩm
         */
        if ($detail->status == 0) {

            $detail->stock_status = 'stopped';

            if ($product) {
                $product->status = 0;
                $product->stock_status = 'stopped';
                $product->save();
            }

        }
        /**
         * KHO ĐƯỢC HIỆN
         * → chỉ hiện nếu còn hàng
         */
        else {

            if ($product) {
                if ((int)$product->quantity > 0) {
                    $detail->stock_status = 'selling';
                    $product->status = 1;
                    $product->stock_status = 'selling';
                    $product->save();
                } else {
                    // phòng thủ (trường hợp hiếm)
                    $detail->stock_status = 'sold_out';
                }
            } else {
                $detail->stock_status = 'pending';
            }
        }

        $detail->save();

        return redirect()
            ->back()
            ->with('success', 'Cập nhật trạng thái kho thành công.');
    }


    //LIST THEO STOCK_STATUS (dùng chung query builder để có selling_qty/sold_qty)

    public function listPending(Request $request)
    {
        [$query] = $this->buildIndexQuery($request, 'pending');
        $details = $query->paginate(10)->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => $request->input('storage_id'),
            'currentStatus' => 'pending'
        ]);
    }

    public function listSelling(Request $request)
    {
        [$query] = $this->buildIndexQuery($request, 'selling');
        $details = $query->paginate(10)->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => $request->input('storage_id'),
            'currentStatus' => 'selling'
        ]);
    }

    public function listSoldOut(Request $request)
    {
        [$query] = $this->buildIndexQuery($request, 'sold_out');
        $details = $query->paginate(10)->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => $request->input('storage_id'),
            'currentStatus' => 'sold_out'
        ]);
    }

    public function listStopped(Request $request)
    {
        [$query] = $this->buildIndexQuery($request, 'stopped');
        $details = $query->paginate(10)->appends($request->query());

        return view('admin.storage_details.index_all', [
            'details' => $details,
            'storages' => Storage::orderByDesc('id')->get(['id','batch_code']),
            'selectedStorageId' => $request->input('storage_id'),
            'currentStatus' => 'stopped'
        ]);
    }
}
