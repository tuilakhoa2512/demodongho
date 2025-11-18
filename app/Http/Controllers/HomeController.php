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
        return view('pages.home')->with('category',$cate_pro)->with('brand',$brand_pro);  
    }
}
