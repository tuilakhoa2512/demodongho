<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    private array $statuses = [
        'pending'   => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao',
        'success'   => 'Hoàn thành',
        'canceled'  => 'Đã hủy',
    ];

    private function requireAdmin()
    {
        if (!Session::has('admin_id')) {
            return Redirect::to('/admin')->send();
        }
        return null;
    }

    public function index(Request $request)
    {
        $this->requireAdmin();

        $filterStatus = (string) $request->query('status', '');
        $keyword      = trim((string) $request->query('keyword', ''));

        $query = DB::table('orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.user_id')
            ->select(
                'o.id',
                'o.order_code',
                'o.status',
                'o.payment_method',
                'o.total_price',
                'o.created_at',
                'o.receiver_name',
                'o.receiver_phone',
                'o.receiver_email',
                'u.fullname as user_fullname',
                'u.email as user_email'
            )
            ->orderByDesc('o.id');

        if ($filterStatus !== '') {
            $query->where('o.status', $filterStatus);
        }

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('o.order_code', 'like', "%{$keyword}%")
                    ->orWhere('o.receiver_name', 'like', "%{$keyword}%")
                    ->orWhere('o.receiver_phone', 'like', "%{$keyword}%")
                    ->orWhere('o.receiver_email', 'like', "%{$keyword}%")
                    ->orWhere('u.fullname', 'like', "%{$keyword}%")
                    ->orWhere('u.email', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->paginate(10)->appends($request->query());

        return view('admin.orders.index', [
            'orders'       => $orders,
            'filterStatus' => $filterStatus,
            'keyword'      => $keyword,
            'statuses'     => $this->statuses,
        ]);
    }

    public function show($order_code)
    {
        $this->requireAdmin();

        $order = DB::table('orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.user_id')
            ->select(
                'o.*',
                'u.fullname as user_fullname',
                'u.email as user_email'
            )
            ->where('o.order_code', $order_code)
            ->first();

        if (!$order) {
            return redirect('/admin/orders')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Lấy chi tiết sản phẩm trong đơn (join products)
        $items = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'p.id') // nếu bạn có bảng product_images
            ->where('od.order_id', $order->id)
            ->select(
                'od.product_id',
                'od.quantity',
                'od.price',
                'p.name as product_name',
                'p.price as product_base_price',
                'pi.image_1 as product_image'
            )
            ->get();

        // Tính tạm tính
        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += ((float)$it->price * (int)$it->quantity);
        }

        $discountValue = (int)($order->discount_bill_value ?? 0);
        $grandTotal    = (float)($order->total_price ?? 0);

        return view('admin.orders.show', [
            'order'         => $order,
            'items'         => $items,
            'subtotal'      => $subtotal,
            'discountValue' => $discountValue,
            'grandTotal'    => $grandTotal,
            'statuses'      => $this->statuses,
        ]);
    }

    public function updateStatus(Request $request, $order_code)
    {
        $this->requireAdmin();

        $request->validate([
            'status' => 'required|string|max:30',
        ]);

        $newStatus = $request->input('status');

        // Chặn status lạ
        if (!array_key_exists($newStatus, $this->statuses)) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        }

        DB::beginTransaction();
        try {
            // Lock đơn để tránh bấm liên tục
            $order = DB::table('orders')
                ->where('order_code', $order_code)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Không tìm thấy đơn hàng.');
            }

            $currentStatus = $order->status ?? 'pending';

            // Nếu đơn đã hủy -> KHÔNG cho đổi nữa
            if ($currentStatus === 'canceled') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Đơn đã hủy, không thể thay đổi trạng thái nữa.');
            }

            // Nếu chọn lại đúng trạng thái hiện tại -> không làm gì
            if ($currentStatus === $newStatus) {
                DB::rollBack();
                return redirect()->back()->with('success', 'Trạng thái không thay đổi.');
            }

            // Nếu đổi sang CANCELED -> hoàn kho chỉ hoàn 1 lần vì đã chặn ở bước 1
            if ($newStatus === 'canceled') {
                $details = DB::table('order_details')
                    ->where('order_id', $order->id)
                    ->get(['product_id', 'quantity']);

                foreach ($details as $d) {
                    $qty = (int)($d->quantity ?? 0);
                    if ($qty <= 0) continue;

                    // Cộng lại tồn kho
                    DB::table('products')
                        ->where('id', (int)$d->product_id)
                        ->increment('quantity', $qty);
                }
            }

            // Update status
            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'status'     => $newStatus,
                    'updated_at' => now(),
                ]);

            DB::commit();
            return redirect()->back()->with('success', 'Đã cập nhật trạng thái đơn hàng!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi cập nhật trạng thái: ' . $e->getMessage());
        }
    }


}
