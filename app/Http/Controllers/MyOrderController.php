<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MyOrderController extends Controller
{
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

        // Lấy redemption mới nhất theo order_id (để tránh join ra nhiều dòng)
        $latestRedemption = DB::table('promotion_redemptions')
            ->selectRaw('MAX(id) as id, order_id')
            ->groupBy('order_id');

        $orders = DB::table('orders as o')
            ->leftJoinSub($latestRedemption, 'pr_max', function ($join) {
                $join->on('pr_max.order_id', '=', 'o.id');
            })
            ->leftJoin('promotion_redemptions as pr', 'pr.id', '=', 'pr_max.id')
            ->leftJoin('promotion_campaigns as c', 'c.id', '=', 'pr.campaign_id')
            ->where('o.user_id', $userId)
            ->orderByDesc('o.id')
            ->select(
                'o.*',
                DB::raw('COALESCE(pr.discount_amount, 0) as promo_discount_amount'),
                'pr.code as promo_code',
                'c.name as promo_campaign_name'
            )
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

        // Lấy redemption mới nhất theo order_id
        $latestRedemption = DB::table('promotion_redemptions')
            ->selectRaw('MAX(id) as id, order_id')
            ->groupBy('order_id');

        $order = DB::table('orders as o')
            ->leftJoin('provinces as pv', 'pv.id', '=', 'o.province_id')
            ->leftJoin('districts as dt', 'dt.id', '=', 'o.district_id')
            ->leftJoin('wards as wd', 'wd.id', '=', 'o.ward_id')

            ->leftJoinSub($latestRedemption, 'pr_max', function ($join) {
                $join->on('pr_max.order_id', '=', 'o.id');
            })
            ->leftJoin('promotion_redemptions as pr', 'pr.id', '=', 'pr_max.id')
            ->leftJoin('promotion_campaigns as c', 'c.id', '=', 'pr.campaign_id')

            ->where('o.order_code', $order_code)
            ->where('o.user_id', $userId)
            ->select(
                'o.*',
                'pv.name as province_name',
                'dt.name as district_name',
                'wd.name as ward_name',

                DB::raw('COALESCE(pr.discount_amount, 0) as promo_discount_amount'),
                'pr.code as promo_code',
                'c.name as promo_campaign_name',
                'pr.used_at as promo_used_at',
                'pr.status as promo_status'
            )
            ->first();

        if (!$order) {
            return redirect('/my-orders')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $items = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'p.id')
            ->where('od.order_id', $order->id)
            ->select(
                'p.id as product_id',
                'p.name',
                'pi.image_1 as image',
                'p.price as base_price',
                'od.price as unit_price',
                'od.quantity'
            )
            ->get();

        // Subtotal theo order_details
        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += ((float)$it->unit_price * (int)$it->quantity);
        }

        $promoDiscount = (int)($order->promo_discount_amount ?? 0);
        $grandTotal = (float)($order->total_price ?? max(0, $subtotal - $promoDiscount));

        // text khu vực
        $areaParts = array_filter([
            $order->ward_name ?? null,
            $order->district_name ?? null,
            $order->province_name ?? null,
        ]);
        $order->area_text = !empty($areaParts) ? implode(' - ', $areaParts) : null;

        // gắn thêm để blade dùng nếu muốn
        $order->subtotal = $subtotal;
        $order->promo_discount = $promoDiscount;
        $order->grand_total = $grandTotal;

        return view('pages.my_orders.show', [
            'order'        => $order,
            'items'        => $items,
            'statusLabels' => $this->statusLabels,
        ]);
    }
}
