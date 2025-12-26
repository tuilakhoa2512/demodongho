<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MyOrderController extends Controller
{
    /**
     * Mapping trạng thái đơn hàng (EN → VI)
     * Dùng chung cho index + show
     */
    private array $statusLabels = [
        'pending'   => 'Đợi Xác Nhận',
        'confirmed' => 'Đã Xác Nhận',
        'shipping'  => 'Đang Giao Hàng',
        'success'   => 'Hoàn Thành',
        'canceled'  => 'Đã Hủy',
    ];

    // GET /my-orders
    public function index()
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        return view('pages.my_orders.index', [
            'orders'       => $orders,
            'statusLabels' => $this->statusLabels,
        ]);
    }

    // GET /my-orders/{order_code}
    public function show(string $order_code)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        $order = DB::table('orders')
            ->where('order_code', $order_code)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return redirect('/my-orders')->with('error', 'Không tìm thấy đơn hàng.');
        }

        /**
         * Lưu ý:
         * - od.price: đơn giá tại thời điểm đặt hàng (giá đã chốt)
         * - p.price : giá gốc hiện tại của sản phẩm (để hiển thị gạch ngang nếu có sale)
         * - KHÔNG query p.discounted_price (vì DB bạn không có cột này)
         */
        $items = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'p.id')
            ->where('od.order_id', $order->id)
            ->select(
                'p.id as product_id',
                'p.name',
                'pi.image_1 as image',          // ảnh default
                'p.price as base_price',        // giá gốc hiện tại
                'od.price as unit_price',       // đơn giá đã chốt trong đơn
                'od.quantity'
            )
            ->get();

        return view('pages.my_orders.show', [
            'order'        => $order,
            'items'        => $items,
            'statusLabels' => $this->statusLabels,
        ]);
    }
}
