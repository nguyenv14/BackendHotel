<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryHotel extends Model
{
   public $timestamps = false;
   protected $fillable = [
    'hotel_id' ,  'gallery_hotel_name' ,  'gallery_hotel_type' ,   'gallery_hotel_image' , 'gallery_hotel_content' ,/* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'gallery_hotel_id'; /* Khóa Chính */
   protected $table =   'tbl_gallery_hotel'; /* Tên Bảng */

   public function hotel(){
      return $this->belongsTo('App\Models\Hotel', 'hotel_id');
  }
}