<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistical extends Model
{
   public $timestamps = false;
   protected $fillable = [
    'order_date' ,  'sales' , 'order_refused' ,  'price_order_refused' ,   'quantity_order_room' ,   'total_order',    /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'statistical_id'; /* Khóa Chính */
   protected $table =   'tbl_statistical'; /* Tên Bảng */
}
