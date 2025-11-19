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
    public function AuthLogin(){
        $id = Session::get('id');
            if($id){
                return Redirect::to('dashboard');
            }else{
                return Redirect::to('admin')->send();
            }
        }   
    public function add_product_type(){
        // $this->AuthLogin();
        return view('admin.add_product_type');
    }
    public function all_product_type(){
        // $this->AuthLogin();
        $all_product_type = DB::table('categories')->get();
        $manager_product_type = view('admin.all_product_type')->with('all_product_type',$all_product_type);
        return view('pages.admin_layout')->with('admin.all_product_type',$manager_product_type);
        
    }
    public function save_product_type(Request $request){
        // $this->AuthLogin();
        $data = array();
        $data['name'] = $request->product_type_name;
        $data['description'] = $request->product_type_desc;

       DB::table('categories')->insert($data);
       Session::put('message','Thêm loại sản phẩm thành công');
       return Redirect::to('add-product-type');
    }

    public function edit_product_type($id){
        // $this->AuthLogin();
        $edit_product_type = DB::table('categories')->where('id',$id)->get();
        $manager_product_type = view('admin.edit_product_type')->with('edit_product_type',$edit_product_type);
        return view('pages.admin_layout')->with('admin.edit_product_type',$manager_product_type);
    }
    public function update_product_type(Request $request,$id){
        // $this->AuthLogin();
        $data = array();
        $data['name'] = $request->product_type_name;
        $data['description'] = $request->product_type_desc;

        DB::table('categories')->where('id',$id)->update($data);
        Session::put('message','Cập nhật loại sản phẩm thành công');
        return Redirect::to('all-product-type');
    }

    public function delete_product_type($id){
        // $this->AuthLogin();
        DB::table('categories')->where('id',$id)->delete();
        Session::put('message','Xoá loại sản phẩm thành công');
        return Redirect::to('all-product-type');
    }
}
