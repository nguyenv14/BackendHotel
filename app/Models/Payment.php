<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
   public $timestamps = false;
   protected $fillable = [
    'payment_method' ,  'payment_status' ,
   ]; 
   protected $primaryKey =  'payment_id'; /* Khóa Chính */
   protected $table =   'tbl_payment'; /* Tên Bảng */
}