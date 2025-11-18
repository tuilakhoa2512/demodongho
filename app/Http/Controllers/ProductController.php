<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
session_start();

class ProductController extends Controller
{
    public function AuthLogin(){
        $id = Session::get('id');
            if($id){
                return Redirect::to('dashboard');
            }else{
                return Redirect::to('admin')->send();
            }
        }   
    public function add_product(){
        // $this->AuthLogin();
        return view('admin.add_product');
    }
    public function all_product(){
        // $this->AuthLogin();
        $all_product = DB::table('products')->get();
        $manager_product = view('admin.all_product')->with('all_product',$all_product);
        return view('pages.admin_layout')->with('admin.all_product',$manager_product);
        
    }
    public function save_product(Request $request){
        // $this->AuthLogin();
        $data = array();
        $data['name'] = $request->brand_product_name;
        $data['image'] = $request->brand_product_image;
        $data['description'] = $request->brand_product_desc;

       DB::table('products')->insert($data);
       Session::put('message','Thêm  sản phẩm thành công');
       return Redirect::to('add-product');
    }
    public function edit_product($id){
        // $this->AuthLogin();
        $edit_product = DB::table('products')->where('id',$id)->get();
        $manager_product = view('admin.edit_product')->with('edit_product',$edit_product);
        return view('pages.admin_layout')->with('admin.edit_product',$manager_product);
    }
    public function update_product(Request $request,$id){
        // $this->AuthLogin();
        $data = array();
        $data['name'] = $request->brand_product_name;
        $data['image'] = $request->brand_product_image;
        $data['description'] = $request->brand_product_desc;

        DB::table('products')->where('id',$id)->update($data);
        Session::put('message','Cập nhật sản phẩm thành công');
        return Redirect::to('all-product');
    }

    public function delete_product($id){
        // $this->AuthLogin();
        DB::table('products')->where('id',$id)->delete();
        Session::put('message','Xoá sản phẩm thành công');
        return Redirect::to('all-product');
    }
}
