<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class AdminController extends Controller
{
    public function index(){
        return view ('pages.admin_login');
    }

    public function show_dashboard(){
        return view('pages.admin_layout');
    }
    public function dashboard( ){ //Request $request
        // $email = $request->email;
        // $password = $request->password;

        // $result = DB::table('users')->where('email',$email)>where('password',$password)->first();
        // return view('admin.dashboard')
    }
}
