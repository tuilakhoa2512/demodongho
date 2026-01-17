<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Models\Product;
use App\Models\Cart;

// Promotion system
use App\Services\PromotionService;
use App\Services\OrderPricingService;

class CartController extends Controller
{
    protected PromotionService $promotionService;
    protected OrderPricingService $orderPricingService;

    public function __construct(
        PromotionService $promotionService,
        OrderPricingService $orderPricingService
    ) {
        $this->promotionService = $promotionService;
        $this->orderPricingService = $orderPricingService;
    }

    /**
     * =========================
     * GIỎ HÀNG
     * =========================
     */
    public function index()
    {
        $rawCart = $this->getRawCart(); // [product_id => qty]

        if (empty($rawCart)) {
            return view('pages.cart', [
                'cart' => [],
                'subtotal' => 0,
                'billDiscount' => null,
                'billDiscountAmount' => 0,
                'grandTotal' => 0,
                'total' => 0,

                // NEW
                'quote' => null,
                'promoCode' => Session::get('promo_code'),
            ]);
        }

        $products = Product::with('productImage')
            ->whereIn('id', array_keys($rawCart))
            ->get()
            ->keyBy('id');

        $cart = [];
        $subtotal = 0;

        foreach ($rawCart as $pid => $qty) {
            $product = $products->get($pid);

            // ❌ product không hợp lệ → remove
            if (
                !$product ||
                (int)$product->status !== 1 ||
                $product->stock_status !== 'selling' ||
                (int)$product->quantity <= 0
            ) {
                $this->removeByProductId((int)$pid);
                continue;
            }

            // ép qty theo tồn kho
            $qty = max(1, min((int)$qty, (int)$product->quantity));

            // image
            $image = ($product->productImage && $product->productImage->image_1)
                ? Storage::url($product->productImage->image_1)
                : asset('frontend/images/noimage.jpg');

            // ===== PRODUCT PROMO =====
            $basePrice = (float)$product->price;

            $pricePack = $this->promotionService->calcProductFinalPrice($product);

            $finalPrice = (float)($pricePack['final_price'] ?? $basePrice);
            $hasSale    = !empty($pricePack['promotion']);

            $lineTotal = $finalPrice * $qty;
            $subtotal += $lineTotal;

            $cart[$pid] = [
                'id'          => $product->id,
                'name'        => $product->name,
                'image'       => $image,

                'base_price'  => $basePrice,
                'final_price' => $finalPrice,
                'has_sale'    => $hasSale,

                'quantity'    => $qty,
                'max_qty'     => (int)$product->quantity,

                'line_total'  => $lineTotal,
            ];

            // đồng bộ qty nếu bị ép
            $this->syncQty((int)$pid, (int)$qty);
        }

        if (empty($cart)) {
            return view('pages.cart', [
                'cart' => [],
                'subtotal' => 0,
                'billDiscount' => null,
                'billDiscountAmount' => 0,
                'grandTotal' => 0,
                'total' => 0,
                'quote' => null,
                'promoCode' => Session::get('promo_code'),
            ]);
        }

        /**
         * =========================
         * ORDER PROMO + CODE
         * =========================
         */
        $promoCode = Session::get('promo_code');

        $pricingItems = [];
        foreach ($cart as $row) {
            $pricingItems[] = [
                'product_id'        => (int)$row['id'],
                'qty'               => (int)$row['quantity'],
                'unit_price_final'  => (float)$row['final_price'],
            ];
        }

        $quote = $this->orderPricingService->quote(
            $pricingItems,
            $this->customerId() ?? 0,
            $promoCode
        );

        // giữ biến cũ cho view
        $billDiscount        = $quote['order_promotion'] ?? null;
        $billDiscountAmount  = (float)($quote['discount_amount'] ?? 0);
        $grandTotal          = (float)($quote['total'] ?? $subtotal);
        $total               = $grandTotal;

        return view('pages.cart', compact(
            'cart',
            'subtotal',
            'billDiscount',
            'billDiscountAmount',
            'grandTotal',
            'total',
            'quote',
            'promoCode'
        ));
    }

    /**
     * =========================
     * ADD TO CART
     * =========================
     */
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ((int)$product->status !== 1 || $product->stock_status !== 'selling') {
            return back()->with('success', 'Sản phẩm hiện không thể mua.');
        }

        $stock = (int)$product->quantity;
        if ($stock <= 0) {
            return back()->with('success', 'Sản phẩm đã hết hàng.');
        }

        $qty = max(1, (int)$request->input('quantity', 1));

        if ($this->isCustomerLoggedIn()) {
            $row = Cart::firstOrNew([
                'user_id'    => $this->customerId(),
                'product_id' => $product->id,
            ]);

            $current = $row->exists ? (int)$row->quantity : 0;
            $row->quantity = min($current + $qty, $stock);
            $row->save();
        } else {
            $cart = Session::get('cart', []);
            $current = isset($cart[$id]) ? (int)$cart[$id]['quantity'] : 0;

            $cart[$id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'quantity' => min($current + $qty, $stock),
            ];

            Session::put('cart', $cart);
        }

        return back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
    }

    /**
     * =========================
     * UPDATE CART
     * =========================
     */
    public function update(Request $request)
    {
        $quantities = $request->input('quantities', []);

        if (empty($quantities)) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json($this->buildAjaxCartResponse());
            }

            return redirect()->route('cart.index');
        }

        $products = Product::whereIn('id', array_keys($quantities))
            ->select('id', 'quantity', 'status', 'stock_status')
            ->get()
            ->keyBy('id');

        if ($this->isCustomerLoggedIn()) {
            foreach ($quantities as $pid => $qty) {
                $product = $products->get((int)$pid);
                if (!$product) continue;

                if (
                    (int)$product->status !== 1 ||
                    $product->stock_status !== 'selling' ||
                    (int)$product->quantity <= 0
                ) {
                    Cart::where('user_id', $this->customerId())
                        ->where('product_id', $pid)
                        ->delete();
                    continue;
                }

                Cart::where('user_id', $this->customerId())
                    ->where('product_id', $pid)
                    ->update([
                        'quantity' => min(max(1, (int)$qty), (int)$product->quantity)
                    ]);
            }
        } else {
            $cart = Session::get('cart', []);

            foreach ($quantities as $pid => $qty) {
                if (!isset($cart[$pid])) continue;

                $product = $products->get((int)$pid);
                if (!$product) continue;

                if (
                    (int)$product->status !== 1 ||
                    $product->stock_status !== 'selling' ||
                    (int)$product->quantity <= 0
                ) {
                    unset($cart[$pid]);
                    continue;
                }

                $cart[$pid]['quantity'] =
                    min(max(1, (int)$qty), (int)$product->quantity);
            }

            Session::put('cart', $cart);
        }

       if ($request->ajax() || $request->expectsJson()) {
            return response()->json($this->buildAjaxCartResponse());
        }

        return redirect()->route('cart.index')
            ->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    /**
     * =========================
     * REMOVE ITEM
     * =========================
     */
    public function remove(Request $request)
    {
       $this->removeByProductId((int)$request->input('product_id'));

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json($this->buildAjaxCartResponse());
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ!');

    }

   

    private function customerId(): ?int
    {
        return Session::get('id') ? (int)Session::get('id') : null;
    }

    private function isCustomerLoggedIn(): bool
    {
        return $this->customerId() !== null;
    }

    /**
     * raw cart: [product_id => qty]
     */
    private function getRawCart(): array
    {
        if ($this->isCustomerLoggedIn()) {
            return Cart::where('user_id', $this->customerId())
                ->pluck('quantity', 'product_id')
                ->map(fn($q) => (int)$q)
                ->toArray();
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
            Cart::where('user_id', $this->customerId())
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
            Cart::where('user_id', $this->customerId())
                ->where('product_id', $productId)
                ->delete();
        } else {
            $cart = Session::get('cart', []);
            unset($cart[$productId]);
            Session::put('cart', $cart);
        }
    }

    //AJAX
    private function buildAjaxCartResponse(): array
    {
        $rawCart = $this->getRawCart();

        if (empty($rawCart)) {
            return [
                'cart' => [],
                'count' => 0,
                'subtotal' => 0,
                'billDiscountAmount' => 0,
                'grandTotal' => 0,
                'total' => 0,
            ];
        }

        $products = Product::with('productImage')
            ->whereIn('id', array_keys($rawCart))
            ->get()
            ->keyBy('id');

        $cart = [];
        $subtotal = 0;

        foreach ($rawCart as $pid => $qty) {
            $product = $products->get($pid);
            if (!$product) continue;

            $qty = max(1, min((int)$qty, (int)$product->quantity));

            // ✅ SYNC LẠI CART
            $this->syncQty((int)$pid, (int)$qty);

            $basePrice = (float)$product->price;
            $pricePack = $this->promotionService->calcProductFinalPrice($product);
            $finalPrice = (float)($pricePack['final_price'] ?? $basePrice);

            $lineTotal = $finalPrice * $qty;
            $subtotal += $lineTotal;

            $cart[$pid] = [
                'id' => $pid,
                'quantity' => $qty,
                'final_price' => $finalPrice,
                'line_total' => $lineTotal,
            ];
        }

        if (empty($cart)) {
            return [
                'cart' => [],
                'count' => 0,
                'subtotal' => 0,
                'billDiscountAmount' => 0,
                'grandTotal' => 0,
                'total' => 0,
            ];
        }


        $pricingItems = [];
        foreach ($cart as $row) {
            $pricingItems[] = [
                'product_id'       => (int)$row['id'],
                'qty'              => (int)$row['quantity'],
                'unit_price_final' => (float)$row['final_price'],
            ];
        }

        $quote = $this->orderPricingService->quote(
            $pricingItems,
            $this->customerId() ?? 0,
            Session::get('promo_code')
        );

        $count = 0;
        foreach ($cart as $row) {
            $count += (int) $row['quantity'];
        }

        return [
            'cart' => $cart,
            'count' => $count,
            'subtotal' => $subtotal,
            'billDiscountAmount' => (float)($quote['discount_amount'] ?? 0),
            'grandTotal' => (float)($quote['total'] ?? $subtotal),
            'total' => (float)($quote['total'] ?? $subtotal),
        ];
    }

}
