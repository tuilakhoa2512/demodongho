<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function add_product_type(){
        return view('admin.add_product_type');
    }
    public function all_product_type(){
        return view('admin.all_product_type');
        
    }
}
