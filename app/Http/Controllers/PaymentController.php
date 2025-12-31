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
<<<<<<< HEAD
    
=======

>>>>>>> main
    public function show()
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

            $this->syncQty((int)$pid, (int)$qty);
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('success', 'Giỏ hàng đang trống hoặc sản phẩm không hợp lệ.');
        }

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

<<<<<<< HEAD
    
     //COD: tạo order + details + trừ kho + xóa cart
     //VNPAY: tạo order + details, KHÔNG trừ kho, KHÔNG xóa cart, chuyển sang /vnpay/create/{order_code}
    
=======
>>>>>>> main
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
        ]);

   
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

        $orderCode = 'DH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            // Create Order 
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $userId,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'total_price' => $grandTotal,

                'discount_bill_id' => $billDiscountId,
                'discount_bill_rate' => $billDiscountRate,
                'discount_bill_value' => (int)$billDiscountAmount,

                'receiver_name' => $request->receiver_name,
                'receiver_email' => $request->receiver_email,
                'receiver_phone' => $request->receiver_phone,
                'receiver_address' => $request->receiver_address,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
            ]);

            //  Create OrderDetails 
            foreach ($items as $it) {
                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $it['product_id'],
                    'quantity'   => $it['quantity'],
                    'price'      => $it['price'],
                ]);
            }

<<<<<<< HEAD
           
            // - COD: trừ kho + xóa cart ngay
             //- VNPAY: KHÔNG trừ kho, KHÔNG xóa cart ở đây
             //  => VNPayController xử lý khi return success
             
=======

>>>>>>> main
            if ($request->payment_method === 'COD') {
                foreach ($items as $it) {
                    $updated = Product::where('id', $it['product_id'])
                        ->where('quantity', '>=', $it['quantity'])
                        ->decrement('quantity', $it['quantity']);

                    if (!$updated) {
                        throw new \Exception('Sản phẩm đã hết hàng hoặc không đủ tồn kho.');
                    }
                }

                $this->clearCart();
            }

            DB::commit();

            // VNPAY: đi sang trang tạo URL thanh toán
            if ($request->payment_method === 'VNPAY') {
                return redirect()->route('vnpay.create', ['order_code' => $orderCode]);
            }

            // COD: về Payment Success
            return redirect()->route('payment.success', ['order_code' => $orderCode])
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }

  
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

    //  HÀM PHỤ 

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
