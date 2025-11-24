<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

session_start();

class BrandProductController extends Controller
{
    public function AuthLogin()
    {
        $id = Session::get('id');
        if ($id) {
            return Redirect::to('dashboard');
        } else {
            return Redirect::to('admin')->send();
        }
    }

    public function add_brand_product()
    {
        // $this->AuthLogin();
        return view('admin.add_brand_product');
    }

    public function all_brand_product()
    {
        // $this->AuthLogin();
        $all_brand_product = DB::table('brands')->get();
        $manager_brand_product = view('admin.all_brand_product')
            ->with('all_brand_product', $all_brand_product);

        return view('pages.admin_layout')
            ->with('admin.all_brand_product', $manager_brand_product);
    }

    public function save_brand_product(Request $request)
    {
        $request->validate([
            'brand_product_name'   => 'required|string|max:150',
            'brand_product_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'brand_product_desc'   => 'nullable|string',
        ]);

        $data = [];
        $data['name']        = $request->brand_product_name;
        $data['description'] = $request->brand_product_desc;

        if ($request->hasFile('brand_product_image')) {
            $file = $request->file('brand_product_image');

            // lưu vào storage/app/public/brands
            $path = $file->store('brands', 'public'); // vd "brands/logo1.jpg"
            $data['image'] = $path;
        } else {
            $data['image'] = null;
        }

        DB::table('brands')->insert($data);

        session()->flash('message', 'Thêm thương hiệu sản phẩm thành công');
        return Redirect::to('add-brand-product');
    }

    public function edit_brand_product($id)
    {
        // $this->AuthLogin();
        $edit_brand_product = DB::table('brands')->where('id', $id)->get();
        $manager_brand_product = view('admin.edit_brand_product')
            ->with('edit_brand_product', $edit_brand_product);

        return view('pages.admin_layout')
            ->with('admin.edit_brand_product', $manager_brand_product);
    }

    public function update_brand_product(Request $request, $id)
    {
        // $this->AuthLogin();

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

        if ($request->hasFile('brand_product_image')) {
            $file = $request->file('brand_product_image');
            $path = $file->store('brands', 'public'); // ảnh mới

            $data['image'] = $path;

            // xóa ảnh cũ
            if ($brand && $brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
        } else {
            // không upload ảnh mới → giữ nguyên ảnh cũ
            $data['image'] = $brand ? $brand->image : null;
        }

        DB::table('brands')->where('id', $id)->update($data);

        session()->flash('message');
        return redirect()->back();
    }

   public function delete_brand_product($id)
    {
        // $this->AuthLogin();

        
        $brand = DB::table('brands')->where('id', $id)->first();

        if ($brand && $brand->image) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand = Brand::find($id);
    
        if ($brand) {
            $brand->delete();
            return redirect()->back()->with('message', 'Xóa sản phẩm thành công!');
        } else {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }
        
        return Redirect::to('all-brand-product');
    }
    public function destroy($id)
    {
        // tim, ko co thi bao 404
        $product = Brand::findOrFail($id);

        $product->delete();

        // qlai dq
        return redirect()->to('/admin/brands')
                        ->with('success', 'Xoá sản phẩm thành công!');
    }
    public function show_brand_home($id)
{
    // Lấy tất cả categories để render menu
    $cate_pro = DB::table('categories')->orderBy('id', 'asc')->get();

    // Lấy tất cả brands để render menu
    $brand_pro = DB::table('brands')->orderBy('id', 'asc')->get();

    // Lấy tên thương hiệu được click
    $brand_name = DB::table('brands')->where('id', $id)->value('name');

    // Lấy sản phẩm theo brand_id
    $brand_by_id = DB::table('products')
        ->join('brands', 'products.brand_id', '=', 'brands.id')
        ->leftJoin('product_images', 'product_images.product_id', '=', 'products.id')
        ->where('products.brand_id', $id)
        ->select(
            'products.*',
            'brands.name as brand_name',
            'product_images.image_1',
            'product_images.image_2',
            'product_images.image_3',
            'product_images.image_4'
        )
        ->get();

    return view('pages.brand.show_brand')
        ->with('category', $cate_pro)
        ->with('brand', $brand_pro)
        ->with('brand_name', $brand_name)
        ->with('brand_by_id', $brand_by_id);
}

}
