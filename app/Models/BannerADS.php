<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerADS extends Model
{
   use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 
   public $timestamps = false;
   protected $fillable = [
    'bannerads_title' , 'bannerads_desc' , 'bannerads_page' , 'bannerads_link' , 'bannerads_image' , 'bannerads_status' , /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'bannerads_id'; /* Khóa Chính */
   protected $table =   'tbl_bannerads'; /* Tên Bảng */
}