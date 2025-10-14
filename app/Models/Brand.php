<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
   use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 
   public $timestamps = false;
   protected $fillable = [
    'brand_name' ,   'brand_desc' ,  'brand_status' /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'brand_id'; /* Khóa Chính */
   protected $table =   'tbl_brand'; /* Tên Bảng */
}