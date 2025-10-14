<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluate extends Model
{
   use SoftDeletes;

   public $timestamps = false;

   protected $dates = [
      'deleted_at' ,
   ]; 

   protected $fillable = [
    'customer_id' ,  'customer_name' , 'hotel_id' ,'room_id' , 'type_room_id' , 'evaluate_title' ,   'evaluate_content' ,   'evaluate_loaction_point' , 'evaluate_service_point' ,'evaluate_price_point','evaluate_sanitary_point','evaluate_convenient_point' /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'evaluate_id'; /* Khóa Chính */
   protected $table = 'tbl_evaluate'; /* Tên Bảng */
   public function room(){
      return $this->belongsTo('App\Models\Room', 'room_id');
  }

  public function show_evalute_hotel($hotel_id){
   $hotel = Hotel::where('hotel_id',$hotel_id)->first();
   $output ='
   <a href="'.URL('khach-san-chi-tiet?hotel_id='.$hotel_id).'">
   '.$hotel->hotel_name.'
   </a>
   ';
   return $output;
  }
}