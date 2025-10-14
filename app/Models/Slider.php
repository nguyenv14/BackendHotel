<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
   use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 
   public $timestamps = false;
   protected $fillable = [
    'slider_name' ,  'slider_image' ,   'slider_status' ,   'slider_desc' /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'slider_id'; /* Khóa Chính */
   protected $table =   'tbl_slider'; /* Tên Bảng */
}