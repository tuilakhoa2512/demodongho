<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Models\Product;
use App\Models\Cart;
use App\Models\DiscountBill;

class CartController extends Controller
{
    /**
     * =========================
     * GIỎ HÀNG
     * =========================
     * - Login: Session::get('id') → DB carts
     * - Guest: Session::get('cart')
     * - Luôn load Product thật để lấy giá & tồn kho chuẩn
     */
    public function index()
    {
        $raw = $this->getRawCart(); // [product_id => qty]

        if (empty($raw)) {
            return view('pages.cart', [
                'cart' => [],
                'total' => 0,
                'subtotal' => 0,
                'billDiscount' => null,
                'billDiscountAmount' => 0,
                'grandTotal' => 0,
            ]);
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

            // Product không tồn tại → remove
            if (!$product) {
                $this->removeByProductId($pid);
                continue;
            }

            // Không còn bán / hết hàng → remove
            if (
                (int)$product->status !== 1 ||
                $product->stock_status !== 'selling' ||
                (int)$product->quantity <= 0
            ) {
                $this->removeByProductId($pid);
                continue;
            }

            // Image
            $image = ($product->productImage && $product->productImage->image_1)
                ? Storage::url($product->productImage->image_1)
                : asset('frontend/images/noimage.jpg');

            // Tồn kho thật
            $maxQty = max(1, (int)$product->quantity);

            // Ép qty theo tồn kho
            $qty = max(1, (int)$qty);
            $qty = min($qty, $maxQty);

            // Giá
            $basePrice = (float)$product->price;
            $salePrice = $product->discounted_price;
            $hasSale   = ($salePrice !== null && (float)$salePrice > 0 && (float)$salePrice < $basePrice);

            $finalPrice = $hasSale ? (float)$salePrice : $basePrice;

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
                'max_qty'     => $maxQty,

                'line_total'  => $lineTotal,
            ];

            // Đồng bộ qty nếu bị ép
            $this->syncQty($pid, $qty);
        }

        // =========================
        // ƯU ĐÃI HÓA ĐƠN
        // =========================
        $billDiscount = $this->getBestBillDiscount($subtotal);

        $billDiscountAmount = 0;
        if ($billDiscount) {
            $billDiscountAmount = round($subtotal * ((float)$billDiscount->rate / 100));
            $billDiscountAmount = min($billDiscountAmount, $subtotal);
        }

        $grandTotal = max(0, $subtotal - $billDiscountAmount);
        $total = $grandTotal;

        return view('pages.cart', compact(
            'cart',
            'total',
            'subtotal',
            'billDiscount',
            'billDiscountAmount',
            'grandTotal'
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
            return redirect()->back()->with('success', 'Sản phẩm hiện không thể mua.');
        }

        $stock = max(0, (int)$product->quantity);
        if ($stock <= 0) {
            return redirect()->back()->with('success', 'Sản phẩm đã hết hàng.');
        }

        $qty = max(1, (int)$request->input('quantity', 1));

        // ===== LOGIN (Session id) =====
        if ($this->isCustomerLoggedIn()) {
            $userId = $this->customerId();

            $row = Cart::firstOrNew([
                'user_id'    => $userId,
                'product_id' => $product->id,
            ]);

            $current = $row->exists ? (int)$row->quantity : 0;
            $row->quantity = min($current + $qty, $stock);
            $row->save();
        }
        // ===== GUEST =====
        else {
            $cart = Session::get('cart', []);

            $current = isset($cart[$id]) ? (int)($cart[$id]['quantity'] ?? 0) : 0;

            $cart[$id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'quantity' => min($current + $qty, $stock),
            ];

            Session::put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
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
            return redirect()->route('cart.index');
        }

        $productIds = array_map('intval', array_keys($quantities));

        $products = Product::whereIn('id', $productIds)
            ->select('id', 'quantity', 'status', 'stock_status')
            ->get()
            ->keyBy('id');

        if ($this->isCustomerLoggedIn()) {
            $userId = $this->customerId();

            foreach ($quantities as $pid => $qty) {
                $pid = (int)$pid;
                $qty = max(1, (int)$qty);

                $p = $products->get($pid);
                if (!$p) continue;

                if (
                    (int)$p->status !== 1 ||
                    $p->stock_status !== 'selling' ||
                    (int)$p->quantity <= 0
                ) {
                    Cart::where('user_id', $userId)
                        ->where('product_id', $pid)
                        ->delete();
                    continue;
                }

                Cart::where('user_id', $userId)
                    ->where('product_id', $pid)
                    ->update(['quantity' => min($qty, (int)$p->quantity)]);
            }
        } else {
            $cart = Session::get('cart', []);

            foreach ($quantities as $pid => $qty) {
                $pid = (int)$pid;
                if (!isset($cart[$pid])) continue;

                $qty = max(1, (int)$qty);

                $p = $products->get($pid);
                if (!$p) continue;

                if (
                    (int)$p->status !== 1 ||
                    $p->stock_status !== 'selling' ||
                    (int)$p->quantity <= 0
                ) {
                    unset($cart[$pid]);
                    continue;
                }

                $cart[$pid]['quantity'] = min($qty, (int)$p->quantity);
            }

            Session::put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    /**
     * =========================
     * REMOVE ITEM
     * =========================
     */
    public function remove(Request $request)
    {
        $productId = (int)$request->input('product_id');
        $this->removeByProductId($productId);

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    // =================================================
    // =================== HÀM PHỤ ======================
    // =================================================

    private function customerId(): ?int
    {
        return Session::get('id') ? (int)Session::get('id') : null;
    }

    private function isCustomerLoggedIn(): bool
    {
        return $this->customerId() !== null;
    }

    /**
     * Lấy raw cart: [product_id => qty]
     */
    private function getRawCart(): array
    {
        if ($this->isCustomerLoggedIn()) {
            $rows = Cart::where('user_id', $this->customerId())
                ->get(['product_id', 'quantity']);

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
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                Session::put('cart', $cart);
            }
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
