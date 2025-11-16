<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
session_start();

class ProductTypeController extends Controller
{
    public function add_product_type(){
        return view('admin.add_product_type');
    }
    public function all_product_type(){
        $all_product_type = DB::table('categories')->get();
        $manager_product_type = view('admin.all_product_type')->with('all_product_type',$all_product_type);
        return view('pages.admin_layout')->with('admin.all_product_type',$manager_product_type);
        
    }
    public function save_product_type(Request $request){
        $data = array();
        $data['name'] = $request->product_type_name;
        $data['image'] = $request->product_type_image;
        $data['description'] = $request->product_type_desc;

       DB::table('categories')->insert($data);
       Session::put('message','Thêm loại sản phẩm thành công');
       return Redirect::to('add-product-type');
    }
}
