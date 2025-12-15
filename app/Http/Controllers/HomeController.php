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
    $cate_pro = DB::table('categories')->where('status','1')->orderBy('id', 'asc')->get();
    $brand_pro = DB::table('brands')->where('status','1')->orderBy('id', 'asc')->get();

    // Lấy tất cả sản phẩm, phân trang 9 sản phẩm/trang
    $all_product = Product::with('productImage', 'category', 'brand')

        ->where('status', 1)                 
        ->where('stock_status', 'selling')
        ->whereHas('category', function($q){
            $q->where('status', 1);   // category đang hiển thị
        })
        ->whereHas('brand', function($q){
            $q->where('status', 1);   // brand đang hiển thị
        }) 
        ->inRandomOrder()
        ->paginate(6);

    // --- Thêm recommended products ---
    // Lấy 6 sản phẩm ngẫu nhiên làm recommended
    $recommended_products = Product::with('productImage')
        ->where('status', 1)
        ->where('stock_status', 'selling')
        ->orderBy('created_at', 'desc') // hoặc orderBy('id', 'desc')
        ->take(6)
        ->get();


    return view('pages.home')
        ->with('category', $cate_pro)
        ->with('brand', $brand_pro)
        ->with('all_product', $all_product)
        ->with('recommended_products', $recommended_products);
}


    public function search(Request $request)
    {
        $keywords = $request->keywords_submit;

        $cate_pro = DB::table('categories')->where('status', 1)->orderBy('id','asc')->get();
        $brand_pro = DB::table('brands')->where('status', 1)->orderBy('id','asc')->get();

        $search_product = Product::with(['productImage', 'category', 'brand'])
            ->where('name', 'like', '%' . $keywords . '%')
            ->where('quantity', '>', 0)
            ->whereHas('category') // chỉ lấy sản phẩm có category đang hiển thị
            ->whereHas('brand')    // chỉ lấy sản phẩm có brand đang hiển thị
            ->orderBy('id','asc')
            ->get();

        return view('admin.products.search')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('search_product', $search_product)
            ->with('keywords', $keywords);
    }
    
    //Hàm lọc giá
    public function filterPrice(Request $request)
{
    // Giá VNĐ (default)
    $min = (float) $request->get('min_price', 0);
    $max = (float) $request->get('max_price', 100000000); // 100 triệu

    // Không cho vượt quá 100 triệu
    $max = min($max, 100000000);

    $cate_pro = DB::table('categories')
        ->where('status', 1)
        ->get();

    $brand_pro = DB::table('brands')
        ->where('status', 1)
        ->get();

    $all_product = Product::with('productImage', 'category', 'brand')
        ->where('status', 1)
        ->where('stock_status', 'selling')
        ->whereBetween('price', [$min, $max])
        ->paginate(6)
        ->appends([
            'min_price' => $min,
            'max_price' => $max,
        ]);

    $recommended_products = Product::with('productImage')
        ->where('status', 1)
        ->where('stock_status', 'selling')
        ->latest()
        ->take(6)
        ->get();

    return view('pages.home', [
        'all_product'          => $all_product,
        'category'             => $cate_pro,
        'brand'                => $brand_pro,
        'recommended_products' => $recommended_products,
    ]);
}



}


