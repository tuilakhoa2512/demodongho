<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Session;

class CompareController extends Controller
{
    // Thêm sản phẩm vào SP1 hoặc SP2
    public function add($id)
{
    $product = Product::find($id);
    if (!$product) return back()->with('error', 'Sản phẩm không tồn tại!');

    $compare = session('compare', []);
    $slot = session('compare_slot'); // slot được chọn từ nút "Chọn sản phẩm"

    // Nếu người dùng chọn slot SP1 / SP2
    if ($slot === 'sp1') {
        $compare['sp1'] = $product->id;
    } elseif ($slot === 'sp2') {
        $compare['sp2'] = $product->id;
    } else {

        // Nếu chưa chọn slot → tự động đẩy vào SP1 hoặc SP2
        if (!isset($compare['sp1'])) {
            $compare['sp1'] = $product->id;
        } elseif (!isset($compare['sp2'])) {
            $compare['sp2'] = $product->id;
        } else {
            return back()->with('error', 'Bạn chỉ được so sánh tối đa 2 sản phẩm!');
        }
    }

    // Lưu lại session
    session(['compare' => $compare]);
    session()->forget('compare_slot');

    return redirect()->back()->with('success', 'Đã thêm vào so sánh!');
}

    

    // Xóa slot
    public function remove($slot)
    {
        $compare = Session::get('compare', []);

        if (isset($compare[$slot])) {
            unset($compare[$slot]);
            Session::put('compare', $compare);
        }

        return back()->with('success', 'Đã xoá sản phẩm khỏi so sánh!');
    }

    // View so sánh
    public function view()
    {
        $compare = Session::get('compare', []);

        $sp1 = isset($compare['sp1']) ? Product::find($compare['sp1']) : null;
        $sp2 = isset($compare['sp2']) ? Product::find($compare['sp2']) : null;

        return view('compare.indexcompare', compact('sp1', 'sp2'));
    }

    public function select($slot)
{
    if (!in_array($slot, ['sp1', 'sp2'])) {
        return redirect()->back()->with('error', 'Slot không hợp lệ!');
    }

    // Lưu slot vào session
    session(['compare_slot' => $slot]);

    return redirect('/')->with('success', 'Hãy chọn 1 sản phẩm để thêm vào so sánh!');
}

public function clear()
{
    session()->forget('compare');
    session()->forget('compare_slot');
    return back()->with('success', 'Đã xoá tất cả sản phẩm khỏi so sánh!');
}



}
