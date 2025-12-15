<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Session;

class FavoriteController extends Controller
{
    // Hiển thị danh sách sản phẩm yêu thích
    public function index()
    {
        $user_id = Session::get('id');

        if (!$user_id) {
            return redirect('/login-checkout')->with('error', 'Bạn phải đăng nhập trước.');
        }

        // Lấy danh sách yêu thích kèm product
        $favorites = Favorite::where('user_id', $user_id)
                        ->with('product')
                        ->get();

        return view('favorite.indexfavorite', compact('favorites'));
    }

    // Thêm vào yêu thích
    public function addFavorite($product_id)
    {
        $user_id = Session::get('id');

        if (!$user_id) {
            return redirect('/login-checkout')->with('error', 'Bạn phải đăng nhập trước.');
        }

        // Kiểm tra nếu đã tồn tại
        $exists = Favorite::where('user_id', $user_id)
                        ->where('product_id', $product_id)
                        ->exists();

        if (!$exists) {
            Favorite::create([
                'user_id' => $user_id,
                'product_id' => $product_id,
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm vào yêu thích!');
    }

    // Xóa khỏi yêu thích
    public function removeFavorite($product_id)
    {
        $user_id = Session::get('id');

        Favorite::where('user_id', $user_id)
                ->where('product_id', $product_id)
                ->delete();

        return redirect()->back()->with('success', 'Đã xoá khỏi yêu thích!');
    }
    public function toggle($product_id)
    {
        $user_id = Session::get('id');
    
        // ===== CHƯA ĐĂNG NHẬP → LƯU SESSION =====
        if (!$user_id) {
    
            $favorites = Session::get('favorite_guest', []);
    
            if (in_array($product_id, $favorites)) {
                // bỏ thích
                $favorites = array_diff($favorites, [$product_id]);
                Session::put('favorite_guest', $favorites);
                return redirect()->back()->with('yeu-thich', 'removed');
            } else {
                // thêm thích
                $favorites[] = $product_id;
                Session::put('favorite_guest', array_unique($favorites));
                return redirect()->back()->with('yeu-thich', 'added');
            }
        }
    
        // ===== ĐÃ ĐĂNG NHẬP → LƯU DB =====
        $favorite = Favorite::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->first();
    
        if ($favorite) {
            $favorite->delete();
            return redirect()->back()->with('yeu-thich', 'removed');
        } else {
            Favorite::create([
                'user_id'    => $user_id,
                'product_id' => $product_id
            ]);
            return redirect()->back()->with('yeu-thich', 'added');
        }
    }
    

}
