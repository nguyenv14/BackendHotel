<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
   use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 
   public $timestamps = false;
   protected $fillable = [
    'area_name' ,  'area_image' ,   'area_status' ,   'area_desc' /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'area_id'; /* Khóa Chính */
   protected $table =   'tbl_area'; /* Tên Bảng */
}