<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilitiesHotel extends Model
{
   use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 
   public $timestamps = false;
   protected $fillable = [
    'facilitieshotel_name' ,  'facilitieshotel_image' ,    'facilitieshotel_group' , 'facilitieshotel_status' ,   'facilitieshotel_desc' /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'facilitieshotel_id'; /* Khóa Chính */
   protected $table =   'tbl_facilitieshotel'; /* Tên Bảng */
}