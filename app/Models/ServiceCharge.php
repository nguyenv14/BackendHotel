<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCharge extends Model
{
  use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 

   public $timestamps = false;
   protected $fillable = [
    'hotel_id' ,  'servicecharge_condition' ,   'servicecharge_fee',  /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'servicecharge_id'; /* Khóa Chính */
   protected $table =   'tbl_servicecharge'; /* Tên Bảng */

   public function hotel(){
     return $this->belongsTo('App\Models\Hotel', 'hotel_id');
   }
}
