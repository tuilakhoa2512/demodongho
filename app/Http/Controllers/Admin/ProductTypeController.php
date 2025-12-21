<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Category;
use App\Models\Product;
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
    // public function all_product_type(){
    //     // $this->AuthLogin();
    //     $all_product_type = DB::table('categories')->get();
    //     $manager_product_type = view('admin.all_product_type')->with('all_product_type',$all_product_type);
    //     return view('pages.admin_layout')->with('admin.all_product_type',$manager_product_type);
        
    // }

    public function all_product_type(Request $request){
        // $this->AuthLogin();
        $filterStatus = $request->get('status');
        $query = DB::table('categories');

        // Nếu chọn lọc
        if ($filterStatus === "1") {
            $query->where('status', 1);
        } elseif ($filterStatus === "0") {
            $query->where('status', 0);
        }
        // Lấy kết quả
        $all_product_type = $query->get();

    // Truyền biến qua view
    $manager_product_type = view('admin.all_product_type')
        ->with('all_product_type', $all_product_type)
        ->with('filterStatus', $filterStatus);

    // return view('pages.admin_layout')->with('admin.all_product_type', $manager_product_type);
    return view('admin.all_product_type', [
        'all_product_type' => $all_product_type,
        'filterStatus' => $filterStatus
    ]);
        
    }
    public function save_product_type(Request $request){
        // $this->AuthLogin();
        $request->validate([
            'product_type_name' => 'required|max:255',
            'product_type_slug'   => 'nullable|string|max:255',
            'product_type_desc'   => 'nullable|string',
            'product_type_status' => 'required'
        ]);
        $data = array();
        $data['name'] = $request->product_type_name;
        $data['description'] = $request->product_type_desc;
        $data['status'] = $request->product_type_status;
        $data['category_slug'] = $this->createSlug($request->product_type_name);

        

       DB::table('categories')->insert($data);
       session()->flash('message', 'Thêm loại sản phẩm thành công');
       return Redirect::to('all-product-type');
    }

    private function createSlug($str)
    {
        $str = mb_strtolower($str, 'UTF-8');
    
        // Danh sách thay thế tiếng Việt → không dấu
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];
    
        foreach ($unicode as $nonAccent => $accent) {
            $str = preg_replace("/($accent)/u", $nonAccent, $str);
        }
    
        // Thay ký tự không phải chữ và số thành dấu gạch ngang
        $str = preg_replace('/[^a-z0-9]+/u', '-', $str);
    
        // Xóa dấu gạch đầu/ cuối
        $str = trim($str, '-');
    
        return $str;
    }


    public function unactive_product_type($id){
        DB::table('categories')->where('id',$id)->update(['status'=> 0]);
        session()->flash('message','Ẩn danh mục sản phẩm thành công');
        return Redirect::to('all-product-type');
    }

    public function active_product_type($id){
        DB::table('categories')->where('id',$id)->update(['status'=> 1]);
        session()->flash('message','Hiện danh mục sản phẩm thành công');
        return Redirect::to('all-product-type');
    }

    public function edit_product_type($id){
        // $this->AuthLogin();
        $edit_product_type = DB::table('categories')->where('id',$id)->get();
        $manager_product_type = view('admin.edit_product_type')->with('edit_product_type',$edit_product_type);
        return view('pages.admin_layout')->with('admin.edit_product_type',$manager_product_type);
    }
    public function update_product_type(Request $request, $id) {
        $request->validate([
            'product_type_name' => 'required|max:255',
            'product_type_desc'   => 'nullable|string',
            
        ]);
    
        // Cập nhật sản phẩm
        $data = array();
        $data['name'] = $request->product_type_name;
        $data['description'] = $request->product_type_desc;
        // cập nhật slug khi đổi tên
        $data['category_slug'] = $this->createSlug($request->product_type_name);
    
        DB::table('categories')->where('id', $id)->update($data);
        
        return redirect()->route('admin.addproducttype')
        ->with('message', 'Cập nhật sản phẩm thành công.');
    }
public function show_category_home($category_slug)
{
    $cate_pro = DB::table('categories')
        ->where('status', '1')
        ->orderBy('id', 'asc')
        ->get();

    $brand_pro = DB::table('brands')
        ->where('status', '1')
        ->orderBy('id', 'asc')
        ->get();

    // lấy category theo slug
    $category = DB::table('categories')
        ->where('category_slug', $category_slug)
        ->first();

    if (!$category) {
        abort(404);
    }

    // CHỈ DÙNG ELOQUENT + RELATION
    $category_by_id = Product::with('productImage')
        ->where('category_id', $category->id)
        ->where('status', 1)
        ->get();

    return view('pages.category.show_category')
        ->with('category', $cate_pro)
        ->with('brand', $brand_pro)
        ->with('category_name', $category->name)
        ->with('category_by_id', $category_by_id);
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
