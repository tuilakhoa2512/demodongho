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
use App\Models\DiscountBill;

class PaymentController extends Controller
{
    /**
     * GET /payment
     * - Bắt buộc login theo session id
     * - Prefill thông tin giao hàng từ bảng users
     * - Load cart + tính tiền giống CartController
     */
    public function show()
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        // 1) Load user (prefill)
        $user = DB::table('users')->where('id', $userId)->first();

        // 2) Load provinces + districts + wards theo profile
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

        // 3) Load cart (raw)
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

            // Không còn bán / hết hàng => remove
            if ((int)$product->status !== 1 || $product->stock_status !== 'selling' || (int)$product->quantity <= 0) {
                $this->removeByProductId((int)$pid);
                continue;
            }

            // tồn kho thật
            $maxQty = max(1, (int)$product->quantity);

            // ép qty theo tồn kho
            $qty = max(1, (int)$qty);
            $qty = min($qty, $maxQty);

            // giá final
            $basePrice = (float)$product->price;
            $salePrice = $product->discounted_price;
            $hasSale   = ($salePrice !== null && (float)$salePrice > 0 && (float)$salePrice < $basePrice);
            $finalPrice = $hasSale ? (float)$salePrice : $basePrice;

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

            // sync lại qty nếu bị ép
            $this->syncQty((int)$pid, (int)$qty);
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đang trống hoặc sản phẩm không hợp lệ.');
        }

        // Bill discount
        $billDiscount = $this->getBestBillDiscount($subtotal);
        $billDiscountAmount = 0;

        if ($billDiscount) {
            $rate = (float)$billDiscount->rate;
            $billDiscountAmount = round($subtotal * $rate / 100);
            $billDiscountAmount = min($billDiscountAmount, $subtotal);
        }

        $grandTotal = max(0, $subtotal - $billDiscountAmount);

        return view('pages.payment', compact(
            'user',
            'provinces',
            'districts',
            'wards',
            'cart',
            'subtotal',
            'billDiscount',
            'billDiscountAmount',
            'grandTotal'
        ));
    }

    /**
     * POST /payment
     * - Validate thông tin giao hàng
     * - Tạo orders + order_details
     * - Trừ kho
     * - Xóa cart
     */
    public function placeOrder(Request $request)
    {
        $userId = (int) Session::get('id');
        if (!$userId) {
            return redirect('/login-checkout')->with('error', 'Vui lòng đăng nhập để đặt hàng.');
        }

        // Validate giao hàng
        $request->validate([
            'receiver_name'    => 'required|string|max:150',
            'receiver_email'   => 'required|email|max:150',
            'receiver_phone'   => 'required|string|max:20',
            'receiver_address' => 'required|string|max:255',

            'province_id'      => 'nullable|integer',
            'district_id'      => 'nullable|integer',
            'ward_id'          => 'nullable|integer',

            'payment_method'   => 'required|string|max:30', // COD/VNPAY
        ]);

        // Lấy lại cart & tính tiền (KHÔNG tin dữ liệu client)
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
            $salePrice = $p->discounted_price;
            $hasSale = ($salePrice !== null && (float)$salePrice > 0 && (float)$salePrice < $basePrice);
            $finalPrice = $hasSale ? (float)$salePrice : $basePrice;

            $lineTotal = $finalPrice * $qty;
            $subtotal += $lineTotal;

            $items[] = [
                'product_id' => (int)$p->id,
                'quantity'   => (int)$qty,
                'price'      => (float)$finalPrice,
            ];

            // sync qty cho sạch
            $this->syncQty((int)$p->id, (int)$qty);
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đang trống hoặc sản phẩm không hợp lệ.');
        }

        // Bill discount
        $billDiscount = $this->getBestBillDiscount($subtotal);
        $billDiscountAmount = 0;
        $billDiscountId = null;
        $billDiscountRate = null;

        if ($billDiscount) {
            $billDiscountId = (int)$billDiscount->id;
            $billDiscountRate = (int)$billDiscount->rate;
            $billDiscountAmount = round($subtotal * $billDiscountRate / 100);
            $billDiscountAmount = min($billDiscountAmount, $subtotal);
        }

        $grandTotal = max(0, $subtotal - $billDiscountAmount);

        // Tạo order_code
        $orderCode = 'DH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            // 1) Create Order
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $userId,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'total_price' => $grandTotal,

                'discount_bill_id' => $billDiscountId,
                'discount_bill_rate' => $billDiscountRate,
                'discount_bill_value' => (int)$billDiscountAmount,

                // receiver fields
                'receiver_name' => $request->receiver_name,
                'receiver_email' => $request->receiver_email,
                'receiver_phone' => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
            ]);

            // 2) Create OrderDetails + trừ kho
            foreach ($items as $it) {
                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $it['product_id'],
                    'quantity'   => $it['quantity'],
                    'price'      => $it['price'],
                ]);

                $updated = Product::where('id', $it['product_id'])
                    ->where('quantity', '>=', $it['quantity'])
                    ->decrement('quantity', $it['quantity']);

                if (!$updated) {
                    throw new \Exception('Sản phẩm đã hết hàng hoặc không đủ tồn kho.');
                }
            }

            // 3) Xóa cart
            $this->clearCart();

            DB::commit();

            if ($request->payment_method === 'VNPAY') {
                // qua trang tạo url thanh toán VNPay
                return redirect()->route('vnpay.create', ['order_code' => $orderCode]);
            }

            // mặc định COD
            return redirect()->route('payment.success', ['order_code' => $orderCode])
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }

    /**
     * GET /payment/success/{order_code}
     * Trang đặt hàng thành công (thuộc Payment)
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

        $discountValue = (int)($order->discount_bill_value ?? 0);
        $grandTotal = (float)$order->total_price;

        return view('pages.payment_success', compact(
            'order',
            'items',
            'subtotal',
            'discountValue',
            'grandTotal'
        ));
    }

    // ===================== HÀM PHỤ (cart theo session id, không dùng Auth) =====================

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
        // login => DB carts
        if ($this->isCustomerLoggedIn()) {
            $userId = $this->customerId();
            $rows = Cart::where('user_id', $userId)->get(['product_id', 'quantity']);

            $raw = [];
            foreach ($rows as $r) {
                $raw[(int)$r->product_id] = (int)$r->quantity;
            }
            return $raw;
        }

        // guest => session cart
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

    private function getBestBillDiscount(float $subtotal)
    {
        if ($subtotal <= 0) return null;

        return DiscountBill::query()
            ->where('status', 1)
            ->where('min_subtotal', '<=', $subtotal)
            ->orderByDesc('rate')
            ->orderByDesc('min_subtotal')
            ->first();
    }
}
