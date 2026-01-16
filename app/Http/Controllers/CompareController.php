<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Product;

class CompareController extends Controller
{
    // Thêm sản phẩm vào SP1 hoặc SP2
    public function add($id)
{
    $product = Product::find($id);
    $compare = session('compare', []);

    // kiểm tra trùng sản phẩm
    if (
        (isset($compare['sp1']) && $compare['sp1'] == $product->id) ||
        (isset($compare['sp2']) && $compare['sp2'] == $product->id)
    ) {
        return back()->with('success', 'Sản phẩm này đã có trong mục so sánh!');
    }

    $slot = session('compare_slot'); // slot được chọn

    // Nếu người dùng chọn slot
    if ($slot === 'sp1') {
        $compare['sp1'] = $product->id;
    } elseif ($slot === 'sp2') {
        $compare['sp2'] = $product->id;
    } else {
        // Tự động thêm
        if (!isset($compare['sp1'])) {
            $compare['sp1'] = $product->id;
        } elseif (!isset($compare['sp2'])) {
            $compare['sp2'] = $product->id;
        } else {
            return back()->with('success', 'Bạn chỉ được so sánh tối đa 2 sản phẩm!');
        }
    }

    session(['compare' => $compare]);
    session()->forget('compare_slot');

    return back()->with('success', 'Đã thêm vào so sánh!');
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
