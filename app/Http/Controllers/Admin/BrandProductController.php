<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
session_start();
class BrandProductController extends Controller
{
    public function AuthLogin(){
        $id = Session::get('id');
            if($id){
                return Redirect::to('dashboard');
            }else{
                return Redirect::to('admin')->send();
            }
        }   
    public function add_brand_product(){
        // $this->AuthLogin();
        return view('admin.add_brand_product');
    }
    public function all_brand_product(){
        // $this->AuthLogin();
        $all_brand_product = DB::table('brands')->get();
        $manager_brand_product = view('admin.all_brand_product')->with('all_brand_product',$all_brand_product);
        return view('pages.admin_layout')->with('admin.all_brand_product',$manager_brand_product);
        
    }
    public function save_brand_product(Request $request){
        // $this->AuthLogin();
        $data = array();
        $data['name'] = $request->brand_product_name;
        $data['image'] = $request->brand_product_image;
        $data['description'] = $request->brand_product_desc;

       DB::table('brands')->insert($data);
       Session::put('message','Thêm thương hiệu sản phẩm thành công');
       return Redirect::to('add-brand-product');
    }
    public function edit_brand_product($id){
        // $this->AuthLogin();
        $edit_brand_product = DB::table('brands')->where('id',$id)->get();
        $manager_brand_product = view('admin.edit_brand_product')->with('edit_brand_product',$edit_brand_product);
        return view('pages.admin_layout')->with('admin.edit_brand_product',$manager_brand_product);
    }
    public function update_brand_product(Request $request,$id){
        // $this->AuthLogin();
        $data = array();
        $data['name'] = $request->brand_product_name;
        $data['image'] = $request->brand_product_image;
        $data['description'] = $request->brand_product_desc;

        DB::table('brands')->where('id',$id)->update($data);
        Session::put('message','Cập nhật thương hiệu sản phẩm thành công');
        return Redirect::to('all-brand-product');
    }

    public function delete_brand_product($id){
        // $this->AuthLogin();
        DB::table('brands')->where('id',$id)->delete();
        Session::put('message','Xoá thương hiệu sản phẩm thành công');
        return Redirect::to('all-brand-product');
    }
}
