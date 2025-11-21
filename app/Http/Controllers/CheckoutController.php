<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

session_start();
class CheckoutController extends Controller
{
    public function login_checkout(){
        $cate_pro = DB::table('categories')->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->orderby('id','asc')->get();
        return view('pages.checkout.login_checkout')->with('category', $cate_pro)
        ->with('brand', $brand_pro);
    }
    public function add_user(Request $request){
        $data = array();
        $data['fullname'] = $request->fullname;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);
        $data['phone'] = $request->phone;

        $data['role_id'] = 2;
        $id = DB::table('users')->insertGetId($data);

        Session::put('id',$id);
        Session::put('fullname',$request->fullname);
        return Redirect::to('/checkout');
    }

    public function checkout(){
        $cate_pro = DB::table('categories')->orderby('id','asc')->get();
        $brand_pro = DB::table('brands')->orderby('id','asc')->get();        
        return view('pages.checkout.show_checkout')->with('category', $cate_pro)
        ->with('brand', $brand_pro);
    }
    public function logout_checkout(){
        Session::flush();
        return Redirect::to('/login-checkout');
    }
    public function login_user(Request $request){
        $email = $request->email;
        $password = bcrypt($request->password);
        $result = DB::table('users')->where('email',$email)->where('password',$password)->first();
        
        if($result){
            Session::put('id',$result->id);
            return Redirect::to('/checkout');
        }else{
            return Redirect::to('/login-checkout');
        }
        
    }
}
