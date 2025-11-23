<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
session_start();

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $cate_pro = DB::table('categories')->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->orderby('id','asc')->get();

        // $all_product = DB::('products')
        // ->john('categories','categories.id','=','products.id')
        // ->john('brands','brands.id','=','products.id')
        // ->orderBy('products.id','desc')->get();
        
        $all_product = DB::table('products')->orderby('id', 'asc')->get();
        

        return view('pages.home')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('all_product', $all_product);
    }
    public function search(Request $request){
        $keywords = $request->keywords_submit;
        $cate_pro = DB::table('categories')->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->orderby('id','asc')->get();

        $search_product = DB::table('products')->where('name','like','%'.$keywords.'%')->get();
        
        return view('admin.products.search')
            ->with('category', $cate_pro)
            ->with('brand', $brand_pro)
            ->with('search_product',$search_product);
            
    } 
}
