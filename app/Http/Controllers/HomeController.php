<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\Product;

class HomeController extends Controller
{
   
    public function index()
    {
        
        $cate_pro = DB::table('categories')
            ->orderBy('id', 'asc')
            ->get();

        $brand_pro = DB::table('brands')
            ->orderBy('id', 'asc')
            ->get();

       $all_product = Product::with('productImage')->get();

        foreach ($all_product as $p) {
            $image = $p->productImage; 

        
            $p->main_image_url = $image && $image->image_1
                ? asset('storage/products/'.$p->id.'/'.$image->image_1)
                : asset('frontend/images/noimage.jpg');

            $p->hover_image_url = $image && $image->image_2
                ? asset('storage/products/'.$p->id.'/'.$image->image_2)
                : $p->main_image_url; // nếu không có ảnh 2 thì dùng lại ảnh 1
}
 
        return view('pages.home')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('all_product', $all_product);
    }

   
    public function search(Request $request)
    {
        $keywords = $request->keywords_submit;

        $cate_pro = DB::table('categories')
            ->orderBy('id', 'asc')
            ->get();

        $brand_pro = DB::table('brands')
            ->orderBy('id', 'asc')
            ->get();

        $search_product = Product::where('name', 'like', '%' . $keywords . '%')
            ->where('quantity', '>', 0)
            ->orderBy('id', 'asc')
            ->get();

       
        return view('admin.products.search')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('search_product', $search_product);
    }
}