<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orderer extends Model
{
    public $timestamps = false;
    protected $fillable = [
       'customer_id','orderer_name','orderer_phone', 'orderer_email', 'orderer_type_bed', 'orderer_special_requirements', 'orderer_own_require','orderer_bill_require'  /* Trường Trong Bảng */
    ];
    protected $primaryKey = 'orderer_id'; /* Khóa Chính */
    protected $table = 'tbl_orderer'; /* Tên Bảng */

    public function customer()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id');
    }

}
