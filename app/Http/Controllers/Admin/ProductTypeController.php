<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Category;
use App\Models\ProductImage;
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
        $request->validate([
            'product_type_name' => 'required|max:255',
            'product_type_desc' => 'nullable|string|max:255' // Có thể tùy chỉnh theo yêu cầu
        ]);
        $data = array();
        $data['name'] = $request->product_type_name;
        $data['description'] = $request->product_type_desc;

       DB::table('categories')->insert($data);
       session()->flash('message', 'Thêm loại sản phẩm thành công');
       return Redirect::to('add-product-type');
    }

    public function edit_product_type($id){
        // $this->AuthLogin();
        $edit_product_type = DB::table('categories')->where('id',$id)->get();
        $manager_product_type = view('admin.edit_product_type')->with('edit_product_type',$edit_product_type);
        return view('pages.admin_layout')->with('admin.edit_product_type',$manager_product_type);
    }
    public function update_product_type(Request $request, $id) {
        // Xác thực dữ liệu
        $request->validate([
            'product_type_name' => 'required|max:255',
            'product_type_desc' => 'nullable|string|max:255',
        ]);
    
        // Cập nhật sản phẩm
        $data = array();
        $data['name'] = $request->product_type_name;
        $data['description'] = $request->product_type_desc;
    
        DB::table('categories')->where('id', $id)->update($data);
        
        // Thêm thông báo thành công vào session
        session()->flash('message');
        
        return redirect()->back();
    }

    public function delete_product_type($id){
        // $this->AuthLogin();
        $productType = Category::find($id);
    
    if ($productType) {
        $productType->delete();
        return redirect()->back()->with('message', 'Xóa sản phẩm thành công!');
    } else {
        return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
    }
        
    }
}
