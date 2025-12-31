<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; 

session_start();

class BrandProductController extends Controller
{
    public function add_brand_product()
    {
        
        return view('admin.add_brand_product');
    }

    public function all_brand_product(Request $request)
    {
        
        // Lấy trạng thái lọc từ URL, có thể là: 1,0
        $filterStatus = $request->get('status');
         // Query brands
        $query = DB::table('brands');
        // Nếu chọn lọc
        if ($filterStatus === "1") {
            $query->where('status', 1);
        } elseif ($filterStatus === "0") {
            $query->where('status', 0);
        }
        // Lấy kết quả
        $all_brand_product = $query->get();
        
        $manager_brand_product = view('admin.all_brand_product')
            ->with('all_brand_product', $all_brand_product);

            return view('admin.all_brand_product', [
                'all_brand_product' => $all_brand_product,
                'filterStatus' => $filterStatus
            ]);
    }

    public function save_brand_product(Request $request)
    {
        $request->validate([
            'brand_product_name'   => 'required|string|max:150',
            'brand_product_slug'   => 'nullable|string|max:255',
            'brand_product_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'brand_product_desc'   => 'nullable|string',
            'brand_product_status' => 'required'
        ]);

        $data = [];
        $data['name']        = $request->brand_product_name;
        $data['description'] = $request->brand_product_desc;
        $data['status'] = $request->brand_product_status;

        //  Tạo slug từ tên brand
    $data['brand_slug'] = Str::slug($request->brand_product_name);

    // Nếu slug bị trùng thì thêm số vào sau
    $count = DB::table('brands')->where('brand_slug', $data['brand_slug'])->count();
    if ($count > 0) {
        $data['brand_slug'] = $data['brand_slug'] . '-' . ($count + 1);
    }

        if ($request->hasFile('brand_product_image')) {
            $file = $request->file('brand_product_image');

            // lưu vào storage/app/public/brands
            $path = $file->store('brands', 'public');
            $data['image'] = $path;
        } else {
            $data['image'] = null;
        }

        DB::table('brands')->insert($data);

        return redirect()->route('admin.allbrandproduct')
        ->with('message', 'Thêm thương hiệu sản phẩm thành công');

    }

    public function unactive_brand_product($id){
        DB::table('brands')->where('id',$id)->update(['status'=> 0]);
        session()->flash('message','Ẩn thương hiệu thành công');
        return Redirect::to('all-brand-product');
    }

    public function active_brand_product($id){
        DB::table('brands')->where('id',$id)->update(['status'=> 1]);
        session()->flash('message','Hiện thương hiệu thành công');
        return Redirect::to('all-brand-product');
    }

    public function edit_brand_product($id)
    {
        $edit_brand_product = DB::table('brands')->where('id', $id)->get();
        $manager_brand_product = view('admin.edit_brand_product')
            ->with('edit_brand_product', $edit_brand_product);

        return view('pages.admin_layout')
            ->with('admin.edit_brand_product', $manager_brand_product);
    }

    public function update_brand_product(Request $request, $id)
    {

        $request->validate([
            'brand_product_name'   => 'required|string|max:150',
            'brand_product_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'brand_product_desc'   => 'nullable|string',
        ]);

        // lấy dữ liệu brand hiện tại để biết ảnh cũ
        $brand = DB::table('brands')->where('id', $id)->first();

        $data = [];
        $data['name']        = $request->brand_product_name;
        $data['description'] = $request->brand_product_desc;
       

        // cập nhật slug khi đổi tên
        $newSlug = Str::slug($request->brand_product_name);

        // Nếu slug bị trùng (ngoại trừ chính nó)
        $count = DB::table('brands')
                    ->where('brand_slug', $newSlug)
                    ->where('id', '!=', $id)
                    ->count();
        if ($count > 0) {
            $newSlug .= '-' . ($count + 1);
        }
        $data['brand_slug'] = $newSlug;

        if ($request->hasFile('brand_product_image')) {
            $file = $request->file('brand_product_image');
            $path = $file->store('brands', 'public'); // ảnh mới

            $data['image'] = $path;

            // xóa ảnh cũ
            if ($brand && $brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
        } else {
            // không upload ảnh mới mà giữ nguyên ảnh cũ
            $data['image'] = $brand ? $brand->image : null;
        }

        DB::table('brands')->where('id', $id)->update($data);

        
        return redirect()->route('admin.allbrandproduct')
        ->with('message', 'Cập nhật thương hiệu thành công.');
    }

    public function show_brand_home($brand_slug)
{
    // Lấy tất cả categories để render menu
    $cate_pro = DB::table('categories')
        ->where('status','1')
        ->orderBy('id', 'asc')
        ->get();

    // Lấy tất cả brands để render menu
    $brand_pro = DB::table('brands')
        ->where('status','1')
        ->orderBy('id', 'asc')
        ->get();
        // Lấy brand theo slug (thay vì id)
    $brand = DB::table('brands')->where('brand_slug', $brand_slug)->where('status', 1)->first();

    if (!$brand) {
        abort(404, 'Thương hiệu không tồn tại');
    }

    //  lấy sản phẩm của 1 thương hiệu
    $brand_by_id = Product::with('productImage')
        ->where('brand_id', $brand->id)
        ->where('status', 1)
        ->get();

    return view('pages.brand.show_brand', [
        'category'      => $cate_pro,
        'brand'         => $brand_pro,
        'brand_name'    => $brand->name,
        'brand_by_id'   => $brand_by_id,
    ]);
}

}
