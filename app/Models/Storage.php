<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{

    // gán các cột này bằng mass assignment (create/update)
    protected $fillable = [
        'product_name',      
        'supplier_name',     
        'import_date',       
        'import_quantity',    
        'unit_import_price',  
        'total_import_price', 
    ];

    // quan hệ: 1 lô hàng -> 1 sản phẩm
    public function product()
    {
        return $this->hasOne(Product::class, 'storage_id', 'id');
    }
}
