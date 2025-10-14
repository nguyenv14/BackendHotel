<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
   public $timestamps = false;
   protected $fillable = [
    'order_code' ,'hotel_id', 'hotel_name' , 'room_id' , 'room_name' ,'type_room_id' , 'price_room', 'hotel_fee' ,   /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'order_details_id'; /* Khóa Chính */
   protected $table =   'tbl_order_details'; /* Tên Bảng */

   public function order()
   {
       return $this->belongsTo('App\Models\Order', 'order_code' , 'order_code');
   }
   public function hotel()
   {
       return $this->belongsTo('App\Models\Hotel', 'hotel_id' , 'hotel_id');
   }
   public function room()
   {
       return $this->belongsTo('App\Models\Room', 'room_id' , 'room_id');
   }
}