<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\Cart;
use App\Models\DiscountBill;

class CartController extends Controller
{
    /**
     * Trang giỏ hàng
     * - Login: lấy DB carts
     * - Guest: lấy session cart
     * - Luôn load Product thật để lấy tồn kho & giá đúng
     * - Trả về đủ biến: cart, subtotal, billDiscount, billDiscountAmount, grandTotal
     */
    public function index()
    {
        // 1) Lấy raw cart: [product_id => qty]
        $raw = $this->getRawCart(); // qty có thể đang vượt tồn kho

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

        // 2) Load products 1 lần (tránh N+1)
        $products = Product::with('productImage')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $cart = [];
        $subtotal = 0;

        foreach ($raw as $pid => $qty) {
            $product = $products->get($pid);
            if (!$product) {
                // product bị xóa => bỏ khỏi cart luôn
                $this->removeByProductId($pid);
                continue;
            }

            // Nếu không còn bán / hết hàng => remove khỏi cart
            if ((int)$product->status !== 1 || $product->stock_status !== 'selling' || (int)$product->quantity <= 0) {
                $this->removeByProductId($pid);
                continue;
            }

            // Ảnh
            $image = $product->productImage && $product->productImage->image_1
                ? Storage::url($product->productImage->image_1)
                : asset('frontend/images/noimage.jpg');

            // Tồn kho thật
            $maxQty = max(1, (int)$product->quantity);

            // Ép qty theo tồn kho
            $qty = max(1, (int)$qty);
            $qty = min($qty, $maxQty);

            // Giá (final) theo discounted_price nếu có
            $basePrice = (float)$product->price;
            $salePrice = $product->discounted_price;
            $hasSale = ($salePrice !== null && (float)$salePrice > 0 && (float)$salePrice < $basePrice);

            $finalPrice = $hasSale ? (float)$salePrice : $basePrice;

            $lineTotal = $finalPrice * $qty;
            $subtotal += $lineTotal;

            $cart[$pid] = [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $image,

                'base_price' => $basePrice,
                'final_price' => $finalPrice,
                'has_sale' => $hasSale,

                'quantity' => $qty,
                'max_qty' => $maxQty,

                'line_total' => $lineTotal,
            ];

            // Nếu qty bị ép khác dữ liệu đang lưu => cập nhật lại cho sạch
            $this->syncQty($pid, $qty);
        }

        // 3) Bill discount (nếu bạn muốn áp)
        $billDiscount = $this->getBestBillDiscount($subtotal);

        $billDiscountAmount = 0;
        if ($billDiscount) {
            $rate = (float)$billDiscount->rate; // %
            $billDiscountAmount = round($subtotal * $rate / 100);
        }

        $grandTotal = max(0, $subtotal - $billDiscountAmount);

        // view bạn đang dùng cả total/subtotal => cho đồng bộ
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
     * Add to cart (chặn tồn kho)
     */
    public function add(Request $request, $id)
    {
        $product = Product::with('productImage')->findOrFail($id);

        // chỉ cho mua khi đang bán + hiển thị
        if ((int)$product->status !== 1 || $product->stock_status !== 'selling') {
            return redirect()->back()->with('success', 'Sản phẩm hiện không thể mua.');
        }

        $stock = max(0, (int)$product->quantity);
        if ($stock <= 0) {
            return redirect()->back()->with('success', 'Sản phẩm đã hết hàng.');
        }

        $qty = max(1, (int)$request->input('quantity', 1));

        if (Auth::check()) {
            $row = Cart::firstOrNew([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]);

            $current = $row->exists ? (int)$row->quantity : 0;
            $newQty = min($current + $qty, $stock);

            $row->quantity = $newQty;
            $row->save();
        } else {
            $cart = Session::get('cart', []);

            $current = isset($cart[$id]) ? (int)($cart[$id]['quantity'] ?? 0) : 0;
            $newQty = min($current + $qty, $stock);

            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $newQty,
                // không cần lưu price nữa
            ];

            Session::put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
    }

    /**
     * Update cart (chặn tồn kho)
     */
    public function update(Request $request)
    {
        $quantities = $request->input('quantities', []); // [product_id => qty]
        if (empty($quantities)) {
            return redirect()->route('cart.index');
        }

        $productIds = array_map('intval', array_keys($quantities));

        // load tồn kho + trạng thái
        $products = Product::whereIn('id', $productIds)
            ->select('id', 'quantity', 'status', 'stock_status')
            ->get()
            ->keyBy('id');

        if (Auth::check()) {
            foreach ($quantities as $pid => $qty) {
                $pid = (int)$pid;
                $qty = max(1, (int)$qty);

                $p = $products->get($pid);
                if (!$p) continue;

                if ((int)$p->status !== 1 || $p->stock_status !== 'selling' || (int)$p->quantity <= 0) {
                    Cart::where('user_id', Auth::id())->where('product_id', $pid)->delete();
                    continue;
                }

                $qty = min($qty, (int)$p->quantity);

                Cart::where('user_id', Auth::id())
                    ->where('product_id', $pid)
                    ->update(['quantity' => $qty]);
            }
        } else {
            $cart = Session::get('cart', []);

            foreach ($quantities as $pid => $qty) {
                $pid = (int)$pid;
                if (!isset($cart[$pid])) continue;

                $qty = max(1, (int)$qty);

                $p = $products->get($pid);
                if (!$p) continue;

                if ((int)$p->status !== 1 || $p->stock_status !== 'selling' || (int)$p->quantity <= 0) {
                    unset($cart[$pid]);
                    continue;
                }

                $qty = min($qty, (int)$p->quantity);
                $cart[$pid]['quantity'] = $qty;
            }

            Session::put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    /**
     * Remove item (xóa đúng cho cả login + guest)
     */
    public function remove(Request $request)
    {
        $productId = (int)$request->input('product_id');

        $this->removeByProductId($productId);

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    // ===================== HÀM PHỤ =====================

    /**
     * Lấy raw cart dạng: [product_id => qty]
     */
    private function getRawCart(): array
    {
        if (Auth::check()) {
            $rows = Cart::where('user_id', Auth::id())->get(['product_id', 'quantity']);
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

    /**
     * Đồng bộ qty đã bị ép về tồn kho (cho sạch dữ liệu)
     */
    private function syncQty(int $productId, int $qty): void
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
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

    /**
     * Xóa item theo product_id cho cả login + guest
     */
    private function removeByProductId(int $productId): void
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
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

    /**
     * Lấy Discount Bill tốt nhất theo subtotal
     */
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
