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

            // ✅ NEW promotion system
            ->leftJoin('promotion_redemptions as pr', 'pr.order_id', '=', 'o.id')
            ->leftJoin('promotion_campaigns as pcamp', 'pcamp.id', '=', 'pr.campaign_id')
            ->leftJoin('promotion_codes as pcode', 'pcode.id', '=', 'pr.code_id')

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
                'u.email as user_email',

                // promo snapshot
                DB::raw('COALESCE(pr.discount_amount, 0) as promo_discount_amount'),
                'pcode.code as promo_code',
                'pcamp.name as promo_campaign_name'
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

            
            ->leftJoin('provinces as pv', 'pv.id', '=', 'o.province_id')
            ->leftJoin('districts as dt', 'dt.id', '=', 'o.district_id')
            ->leftJoin('wards as wd', 'wd.id', '=', 'o.ward_id')

            // promotion system (lấy snapshot từ promotion_redemptions)
            ->leftJoin('promotion_redemptions as pr', 'pr.order_id', '=', 'o.id')
            ->leftJoin('promotion_rules as r', 'r.id', '=', 'pr.rule_id')
            // campaign: ưu tiên campaign_id lưu trong redemption, fallback theo rule
            ->leftJoin('promotion_campaigns as c', function ($join) {
                $join->on('c.id', '=', 'pr.campaign_id')
                    ->orOn('c.id', '=', 'r.campaign_id');
            })
            ->leftJoin('promotion_codes as pc', 'pc.id', '=', 'pr.code_id')

            ->select(
                'o.*',
                'u.fullname as user_fullname',
                'u.email as user_email',

                // location names
                'pv.name as province_name',
                'dt.name as district_name',
                'wd.name as ward_name',

                // promo snapshot
                DB::raw('COALESCE(pr.discount_amount, 0) as promo_discount_amount'),
                'pr.used_at as promo_used_at',
                'c.name as promo_campaign_name',
                'pc.code as promo_code',

                // optional info (để admin biết rule giảm gì)
                'r.discount_type as promo_discount_type',
                'r.discount_value as promo_discount_value'
            )
            ->where('o.order_code', $order_code)
            ->first();

        if (!$order) {
            return redirect('/admin/orders')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $items = DB::table('order_details as od')
            ->join('products as p', 'p.id', '=', 'od.product_id')
            ->leftJoin('product_images as pi', 'pi.product_id', '=', 'p.id')
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

        //  Subtotal = sum(order_details.price * qty)
        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += ((float)$it->price * (int)$it->quantity);
        }

        //  Discount theo hệ mới (promotion_redemptions)
        $discountValue = (int)($order->promo_discount_amount ?? 0);

        // Grand total: ưu tiên orders.total_price (đã chốt khi đặt)
        $grandTotal = (float)($order->total_price ?? max(0, $subtotal - $discountValue));

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

        if (!array_key_exists($newStatus, $this->statuses)) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        }

        DB::beginTransaction();
        try {
            $order = DB::table('orders')
                ->where('order_code', $order_code)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Không tìm thấy đơn hàng.');
            }

            $currentStatus = $order->status ?? 'pending';

            if ($currentStatus === 'canceled') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Đơn đã hủy, không thể thay đổi trạng thái nữa.');
            }

            if ($currentStatus === $newStatus) {
                DB::rollBack();
                return redirect()->back()->with('success', 'Trạng thái không thay đổi.');
            }

            if ($newStatus === 'canceled') {

                $details = DB::table('order_details')
                    ->where('order_id', $order->id)
                    ->get(['product_id', 'quantity']);

                foreach ($details as $d) {
                    $qty = (int)($d->quantity ?? 0);
                    if ($qty <= 0) continue;

                    // 1️⃣ Hoàn lại tồn kho
                    DB::table('products')
                        ->where('id', (int)$d->product_id)
                        ->increment('quantity', $qty);

                    // 2️⃣ Lấy lại product sau khi hoàn kho
                    $product = DB::table('products')
                        ->where('id', (int)$d->product_id)
                        ->first();

                    if ($product && (int)$product->quantity > 0) {

                        // 3️⃣ Mở lại sản phẩm
                        DB::table('products')
                            ->where('id', (int)$product->id)
                            ->update([
                                'stock_status' => 'selling',
                                'status'       => 1,
                            ]);

                        // 4️⃣ Đồng bộ lại kho (KHÔNG đụng status kho)
                        if (!empty($product->storage_detail_id)) {
                            DB::table('storage_details')
                                ->where('id', (int)$product->storage_detail_id)
                                ->update([
                                    'stock_status' => 'selling',
                                ]);
                        }
                    }
                }

                // (optional) hủy redemption nếu muốn
                // DB::table('promotion_redemptions')->where('order_id', $order->id)->update(['status' => 'canceled']);
            }


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
