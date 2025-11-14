<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
session_start();

class AdminController extends Controller
{
    public function index(){
        return view ('pages.admin_login');
    }

    public function show_dashboard(){
        return view('pages.admin_layout');
    }
    public function dashboard(Request $request ){ //Request $request
        $email =  $request->email;
        $password =  md5($request->password);

        $result = DB::table('users')->where('email',$email)->where('password',$password)->first();
        
        if($result){
            Session::put('fullname',$result->fullname);
            Session::put('id',$result->id);
            return Redirect::to('/dashboard');
        }else{
            Session::put('message','Tài khoản hoặc mật khẩu sai!');
            return Redirect::to('/admin');
        }
    }
    public function logout( ){ //Request $request
            echo '123';
    }
}
