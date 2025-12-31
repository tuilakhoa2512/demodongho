<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{

    protected $table = 'storages';

    protected $fillable = [
        'batch_code',
        'supplier_name',
        'supplier_email',
        'import_date', 
        'note',
        'status',           
    ];

    // 1 Storage có nhiều StorageDetail
    public function storageDetails()
    {
        return $this->hasMany(StorageDetail::class, 'storage_id', 'id');
    }
}
