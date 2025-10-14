<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeRoom extends Model
{
    use SoftDeletes;
   
   protected $dates = [
      'deleted_at' ,
   ]; 
    public $timestamps = false;
    protected $fillable = [
        'type_room_id', 'room_id', 'type_room_bed', 'type_room_price', 'type_room_condition', 'type_room_price_sale', 'type_room_status' ,'type_room_quantity',
    ];

    protected $primaryKey = 'type_room_id';
    protected $table = 'tbl_type_room';

    public function room(){
        return $this->belongsTo('App\Models\Room', 'room_id');
    }

}