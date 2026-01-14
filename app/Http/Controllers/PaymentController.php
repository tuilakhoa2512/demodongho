<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;

use App\Services\PromotionService;
use App\Services\OrderPricingService;
use App\Models\PromotionRedemption;

class PaymentController extends Controller
{
    public function __construct(
        protected PromotionService $promotionService,
        protected OrderPricingService $orderPricingService
    ) {}

    /**
     * GET /payment
     */
    public function show(Request $request)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        $user = DB::table('users')->where('id', $userId)->first();

        $provinces = DB::table('provinces')->orderBy('name')->get();

        $districts = [];
        if (!empty($user->province_id)) {
            $districts = DB::table('districts')
                ->where('province_id', $user->province_id)
                ->orderBy('name')
                ->get();
        }

        $wards = [];
        if (!empty($user->district_id)) {
            $wards = DB::table('wards')
                ->where('district_id', $user->district_id)
                ->orderBy('name')
                ->get();
        }

        // ✅ promo code: ưu tiên query -> session
        $promoCode = trim((string)$request->get('promo_code', ''));
        if ($promoCode === '') {
            $promoCode = trim((string) Session::get('promo_code', ''));
        }

        $raw = $this->getRawCart();
        if (empty($raw)) {
            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đang trống.');
        }

        $productIds = array_keys($raw);

        $products = Product::with('productImage')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $cart = [];
        $subtotal = 0;

        foreach ($raw as $pid => $qty) {
            $product = $products->get($pid);

            if (!$product) {
                $this->removeByProductId((int)$pid);
                continue;
            }

            if ((int)$product->status !== 1 || $product->stock_status !== 'selling' || (int)$product->quantity <= 0) {
                $this->removeByProductId((int)$pid);
                continue;
            }

            $maxQty = max(1, (int)$product->quantity);
            $qty = max(1, (int)$qty);
            $qty = min($qty, $maxQty);

            // ✅ NEW: product final price by PromotionService
            $basePrice = (float) $product->price;

            $pack = $this->promotionService->calcProductFinalPrice($product);
            $finalPrice = (float)($pack['final_price'] ?? $basePrice);

            // ✅ FIX: calcProductFinalPrice không có key 'promotion'
            $hasSale = !empty($pack['rule']) && $finalPrice < $basePrice;

            $lineTotal = $finalPrice * $qty;
            $subtotal += $lineTotal;

            $cart[$pid] = [
                'id'          => $product->id,
                'name'        => $product->name,
                'base_price'  => $basePrice,
                'final_price' => $finalPrice,
                'has_sale'    => $hasSale,
                'quantity'    => $qty,
                'max_qty'     => $maxQty,
                'line_total'  => $lineTotal,
            ];

            $this->syncQty((int)$pid, (int)$qty);
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đang trống hoặc sản phẩm không hợp lệ.');
        }

        // ✅ NEW: quote order promotion + code
        $pricingItems = [];
        foreach ($cart as $row) {
            $pricingItems[] = [
                'product_id' => (int)$row['id'],
                'qty' => (int)$row['quantity'],
                'unit_price_final' => (int)$row['final_price'],
            ];
        }

        $quote = $this->orderPricingService->quote($pricingItems, $userId, $promoCode !== '' ? $promoCode : null);

        $billDiscount = $quote['order_promotion'] ?? null;
        $billDiscountAmount = (int)($quote['discount_amount'] ?? 0);
        $grandTotal = (int)($quote['total'] ?? $subtotal);

        return view('pages.payment', compact(
            'user',
            'provinces',
            'districts',
            'wards',
            'cart',
            'subtotal',
            'billDiscount',
            'billDiscountAmount',
            'grandTotal',
            'quote',
            'promoCode'
        ));
    }

    /**
     * POST /payment/place
     */
    public function placeOrder(Request $request)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để đặt hàng.');
        }

        $request->validate([
            'receiver_name'    => 'required|string|max:150',
            'receiver_email'   => 'required|email|max:150',
            'receiver_phone'   => 'required|string|max:20',
            'receiver_address' => 'required|string|max:255',

            'province_id'      => 'nullable|integer',
            'district_id'      => 'nullable|integer',
            'ward_id'          => 'nullable|integer',

            'payment_method'   => 'required|in:COD,VNPAY',

            // ✅ NEW
            'promo_code'       => 'nullable|string|max:50',
        ]);

        $promoCode = trim((string)$request->input('promo_code', ''));

        // giữ lại để hiển thị nếu redirect
        Session::put('promo_code', $promoCode);

        $raw = $this->getRawCart();
        if (empty($raw)) {
            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đang trống.');
        }

        $productIds = array_keys($raw);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $items = [];
        $subtotal = 0;

        foreach ($raw as $pid => $qty) {
            $p = $products->get($pid);

            if (!$p) {
                $this->removeByProductId((int)$pid);
                continue;
            }

            if ((int)$p->status !== 1 || $p->stock_status !== 'selling' || (int)$p->quantity <= 0) {
                $this->removeByProductId((int)$pid);
                continue;
            }

            $stock = (int)$p->quantity;
            $qty = max(1, (int)$qty);
            $qty = min($qty, $stock);

            $basePrice = (float)$p->price;

            // ✅ NEW: product final price
            $pack = $this->promotionService->calcProductFinalPrice($p);
            $finalPrice = (float)($pack['final_price'] ?? $basePrice);

            $lineTotal = $finalPrice * $qty;
            $subtotal += $lineTotal;

            $items[] = [
                'product_id' => (int)$p->id,
                'quantity'   => (int)$qty,
                'price'      => (float)$finalPrice, // unit price final
            ];

            $this->syncQty((int)$p->id, (int)$qty);
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đang trống hoặc sản phẩm không hợp lệ.');
        }

        // ✅ Quote order promo/code again
        $pricingItems = [];
        foreach ($items as $it) {
            $pricingItems[] = [
                'product_id' => (int)$it['product_id'],
                'qty' => (int)$it['quantity'],
                'unit_price_final' => (int)$it['price'],
            ];
        }

        $quote = $this->orderPricingService->quote($pricingItems, $userId, $promoCode !== '' ? $promoCode : null);

        $orderPromotion = $quote['order_promotion'] ?? null; // tùy bạn đang trả về Rule hay gì
        $promotionCode  = $quote['promotion_code'] ?? null;  // PromotionCode|null
        $discountAmount = (int)($quote['discount_amount'] ?? 0);
        $grandTotal     = (int)($quote['total'] ?? max(0, $subtotal - $discountAmount));

        // $orderCode = 'DH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {

        // ===== TẠO MÃ ĐƠN: UnK-YYYYMM-DD0001 =====

        $ym = now()->format('Ym'); // YYYYMM
        $d  = now()->format('d');  // DD

        $prefix = "UnK-{$ym}-{$d}";

        // tìm đơn cuối cùng trong ngày
        $lastOrderToday = Order::where('order_code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if ($lastOrderToday) {
            // lấy 4 số cuối
            $lastNumber = (int) substr($lastOrderToday->order_code, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $orderCode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // 1) Create Order (KHÔNG thêm cột)
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $userId,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'total_price' => $grandTotal,

                'receiver_name' => $request->receiver_name,
                'receiver_email' => $request->receiver_email,
                'receiver_phone' => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
            ]);

            // 2) Create OrderDetails
            foreach ($items as $it) {
                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $it['product_id'],
                    'quantity'   => $it['quantity'],
                    'price'      => $it['price'],
                ]);
            }

            /**
             * ✅ GHI promotion_redemptions THEO ĐÚNG SCHEMA (không thêm cột DB)
             * - Ghi cho cả auto promotion và code promotion (nếu có giảm)
             * - COD: used_at=now(), status='applied'
             * - VNPAY: used_at=null, status='pending' (VNPay success sẽ update used_at)
             */
            if ($discountAmount > 0) {
                $ruleId = data_get($orderPromotion, 'id');              // nếu order_promotion là Rule
                $campaignId = data_get($orderPromotion, 'campaign_id'); // nếu có

                $codeId = data_get($promotionCode, 'id');               // PromotionCode id

                PromotionRedemption::create([
                    'campaign_id'     => $campaignId ? (int)$campaignId : 0,
                    'rule_id'         => $ruleId ? (int)$ruleId : 0,
                    'code_id'         => $codeId ? (int)$codeId : null,

                    'user_id'         => $userId,
                    'order_id'        => $order->id,
                    'code'            => $promoCode !== '' ? $promoCode : null,

                    'subtotal'        => (int)$subtotal,
                    'discount_amount' => (int)$discountAmount,
                    'final_total'     => (int)$grandTotal,

                    'used_at'         => ($request->payment_method === 'COD') ? now() : null,
                    'status'          => ($request->payment_method === 'COD') ? 'applied' : 'pending',
                ]);
            }

            // COD: trừ kho + auto update trạng thái + clear cart
            if ($request->payment_method === 'COD') {

                foreach ($items as $it) {

                    /** @var Product $product */
                    $product = Product::lockForUpdate()->find($it['product_id']);
                    if (!$product) {
                        throw new \Exception('Không tìm thấy sản phẩm.');
                    }

                    if ($product->quantity < $it['quantity']) {
                        throw new \Exception('Sản phẩm đã hết hàng hoặc không đủ tồn kho.');
                    }

                    // Trừ kho
                    $product->quantity -= $it['quantity'];

                    // ✅ HẾT HÀNG → sold_out + ẩn
                    if ($product->quantity <= 0) {
                        $product->quantity = 0;
                        $product->stock_status = 'sold_out';
                        $product->status = 0; // ẨN

                        if ($product->storageDetail) {
                            $product->storageDetail->stock_status = 'sold_out';
                            $product->storageDetail->save();
                        }
                    }

                    $product->save();
                }

                $this->clearCart();
                Session::forget('promo_code');
            }


            DB::commit();

            if ($request->payment_method === 'VNPAY') {
                return redirect()->route('vnpay.create', ['order_code' => $orderCode]);
            }

            return redirect()->route('payment.success', ['order_code' => $orderCode])
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }

    /**
     * GET /payment/success/{order_code}
     */
    public function success($order_code)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập.');
        }

        $order = Order::where('order_code', $order_code)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return redirect('/trang-chu')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $items = OrderDetail::query()
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->where('order_details.order_id', $order->id)
            ->select(
                'order_details.product_id',
                'order_details.quantity',
                'order_details.price',
                'products.name'
            )
            ->get();

        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += ((float)$it->price * (int)$it->quantity);
        }

        // ✅ lấy giảm từ promotion_redemptions (vì orders không có cột)
        $discountValue = (int) DB::table('promotion_redemptions')
            ->where('order_id', $order->id)
            ->value('discount_amount');

        $grandTotal = (float)$order->total_price;

        return view('pages.payment_success', compact(
            'order',
            'items',
            'subtotal',
            'discountValue',
            'grandTotal'
        ));
    }

    // ===================== HÀM PHỤ (giữ nguyên) =====================

    private function customerId(): ?int
    {
        $id = Session::get('id');
        return $id ? (int)$id : null;
    }

    private function isCustomerLoggedIn(): bool
    {
        return $this->customerId() !== null;
    }

    private function getRawCart(): array
    {
        if ($this->isCustomerLoggedIn()) {
            $userId = $this->customerId();
            $rows = Cart::where('user_id', $userId)->get(['product_id', 'quantity']);

            $raw = [];
            foreach ($rows as $r) {
                $raw[(int)$r->product_id] = (int)$r->quantity;
            }
            return $raw;
        }

        $cart = Session::get('cart', []);
        $raw = [];

        foreach ($cart as $key => $item) {
            $pid = isset($item['id']) ? (int)$item['id'] : (int)$key;
            $raw[$pid] = (int)($item['quantity'] ?? 1);
        }

        return $raw;
    }

    private function syncQty(int $productId, int $qty): void
    {
        if ($this->isCustomerLoggedIn()) {
            $userId = $this->customerId();
            Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->update(['quantity' => $qty]);
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $qty;
                Session::put('cart', $cart);
            }
        }
    }

    private function removeByProductId(int $productId): void
    {
        if ($this->isCustomerLoggedIn()) {
            $userId = $this->customerId();
            Cart::where('user_id', $userId)->where('product_id', $productId)->delete();
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                Session::put('cart', $cart);
            }
        }
    }

    private function clearCart(): void
    {
        if ($this->isCustomerLoggedIn()) {
            $userId = $this->customerId();
            Cart::where('user_id', $userId)->delete();
        } else {
            Session::forget('cart');
        }
    }

    public function applyPromo(Request $request)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return response()->json([
                'ok' => false,
                'message' => 'Vui lòng đăng nhập để áp dụng mã.',
            ], 401);
        }

        $request->validate([
            'promo_code' => 'nullable|string|max:50',
        ]);

        $promoCode = trim((string)$request->input('promo_code', ''));

        // Rebuild cart y hệt show()
        $raw = $this->getRawCart();
        if (empty($raw)) {
            return response()->json([
                'ok' => false,
                'message' => 'Giỏ hàng đang trống.',
            ]);
        }

        $productIds = array_keys($raw);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $pricingItems = [];
        $subtotal = 0;

        foreach ($raw as $pid => $qty) {
            $p = $products->get($pid);
            if (!$p) continue;

            if ((int)$p->status !== 1 || $p->stock_status !== 'selling' || (int)$p->quantity <= 0) {
                continue;
            }

            $stock = (int)$p->quantity;
            $qty = max(1, (int)$qty);
            $qty = min($qty, $stock);

            $basePrice = (float)$p->price;

            // product final price (scope=product)
            $pack = $this->promotionService->calcProductFinalPrice($p);
            $finalPrice = (float)($pack['final_price'] ?? $basePrice);

            $lineTotal = $finalPrice * $qty;
            $subtotal += $lineTotal;

            $pricingItems[] = [
                'product_id' => (int)$p->id,
                'qty' => (int)$qty,
                'unit_price_final' => (int)$finalPrice,
            ];
        }

        if (empty($pricingItems)) {
            return response()->json([
                'ok' => false,
                'message' => 'Không có sản phẩm hợp lệ trong giỏ hàng.',
            ]);
        }

        // Quote order promo/code (scope=order)
        $quote = $this->orderPricingService->quote($pricingItems, $userId, $promoCode !== '' ? $promoCode : null);

        // Lưu promo_code vào session để show() render lại đúng (tùy bạn)
        Session::put('promo_code', $promoCode);

        return response()->json([
            'ok' => true,
            'message' => ($promoCode !== '' && !empty($quote['promotion_code']))
                ? 'Áp dụng mã thành công.'
                : (($promoCode !== '') ? 'Mã không hợp lệ / không đủ điều kiện (đã chuyển sang ưu đãi tự động nếu có).' : 'Đã tính ưu đãi tự động.'),

            'promo_code' => $promoCode,
            'subtotal' => (int)($quote['subtotal'] ?? (int)$subtotal),
            'discount_amount' => (int)($quote['discount_amount'] ?? 0),
            'total' => (int)($quote['total'] ?? (int)$subtotal),

            // optional: để hiện tên ưu đãi
            'has_code' => !empty($quote['promotion_code']),
            'has_promo' => !empty($quote['order_promotion']),
            'promo_name' => !empty($quote['order_promotion']) ? ($quote['order_promotion']->name ?? null) : null,
        ]);
    }

}
