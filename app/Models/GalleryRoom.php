<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryRoom extends Model
{
   public $timestamps = false;
   protected $fillable = [
    'room_id' ,  'gallery_room_name' , 'gallery_room_image' , 'gallery_room_content' ,/* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'gallery_room_id'; /* Khóa Chính */
   protected $table =   'tbl_gallery_room'; /* Tên Bảng */

   public function room(){
      return $this->belongsTo('App\Models\Room', 'room_id');
  }

}