<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Cart;

class CartController extends Controller
{
  
    public function index()
    {
        if (Auth::check()) {
            $cartRows = Cart::with('product.productImage')
                ->where('user_id', Auth::id())
                ->get();

            $cart = [];
            $total = 0;

            foreach ($cartRows as $row) {
                $product = $row->product;

                if (!$product) continue;

                $image = null;
                if ($product->productImage && $product->productImage->image_1) {
                    $image = Storage::url($product->productImage->image_1);
                }

                $cart[$product->id] = [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => $product->price,
                    'quantity' => $row->quantity,
                    'image'    => $image,
                ];

                $total += $product->price * $row->quantity;
            }

        } else {
            $cart = Session::get('cart', []);

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
        }

        return view('pages.cart', compact('cart', 'total'));
    }


    public function add(Request $request, $id)
    {
        $product = Product::with('productImage')->findOrFail($id);

        $qty = (int)$request->input('quantity', 1);
        $qty = max(1, $qty); 

        if (Auth::check()) {
            
            $cartRow = Cart::firstOrNew([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
            ]);

            $cartRow->quantity = ($cartRow->exists ? $cartRow->quantity : 0) + $qty;
            $cartRow->save();

        } else {
            $cart = Session::get('cart', []);

            if (isset($cart[$id])) {
                $cart[$id]['quantity'] += $qty;
            } else {
                $imgPath = null;
                if ($product->productImage && $product->productImage->image_1) {
                    $imgPath = Storage::url($product->productImage->image_1);
                }

                $cart[$id] = [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => $product->price,
                    'quantity' => $qty,
                    'image'    => $imgPath,
                ];
            }

            Session::put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }



    public function update(Request $request)
    {
        $quantities = $request->input('quantities', []); 
        if (Auth::check()) {
            foreach ($quantities as $productId => $qty) {
                $qty = max(1, (int)$qty);

                Cart::where('user_id', Auth::id())
                    ->where('product_id', $productId)
                    ->update(['quantity' => $qty]);
            }

        } else {
            $cart = Session::get('cart', []);

            foreach ($quantities as $productId => $qty) {
                if (isset($cart[$productId])) {
                    $cart[$productId]['quantity'] = max(1, (int)$qty);
                }
            }

            Session::put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công!');
    }


    public function remove($id)
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->delete();

        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                Session::put('cart', $cart);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}
