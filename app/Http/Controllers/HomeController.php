<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
{
    $cate_pro = DB::table('categories')->orderBy('id', 'asc')->get();
    $brand_pro = DB::table('brands')->orderBy('id', 'asc')->get();

    // Lấy tất cả sản phẩm, phân trang 9 sản phẩm/trang
    $all_product = Product::with('productImage')
        ->orderBy('id', 'desc')
        ->paginate(6);  // <-- 9 sản phẩm/trang

    foreach ($all_product as $p) {
        $img = $p->productImage;

        $p->main_image_url = $img && $img->image_1 ? Storage::url($img->image_1) : asset('frontend/images/noimage.jpg');
        $p->hover_image_url = $img && $img->image_2 ? Storage::url($img->image_2) : $p->main_image_url;
    }

    return view('pages.home')
        ->with('category', $cate_pro)
        ->with('brand', $brand_pro)
        ->with('all_product', $all_product);
}



    public function search(Request $request)
    {
        $keywords = $request->keywords_submit;

        $cate_pro = DB::table('categories')->orderBy('id','asc')->get();
        $brand_pro = DB::table('brands')->orderBy('id','asc')->get();

        $search_product = Product::where('name', 'like', '%' . $keywords . '%')
            ->where('quantity', '>', 0)
            ->orderBy('id','asc')
            ->get();

        return view('admin.products.search')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('search_product', $search_product);
    }
}
