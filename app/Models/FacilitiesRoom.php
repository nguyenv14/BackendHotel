<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilitiesRoom extends Model
{
   use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 
   public $timestamps = false;
   protected $fillable = [
    'facilitiesroom_name' ,  'facilitiesroom_image' ,   'facilitiesroom_status' ,   'facilitiesroom_desc' /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'facilitiesroom_id'; /* Khóa Chính */
   protected $table =   'tbl_facilitiesroom'; /* Tên Bảng */
}