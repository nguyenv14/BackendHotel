<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'restaurant_id'; /* Khóa Chính */
    protected $table = 'tbl_restaurant';

    protected $fillable = [
        'restaurant_name',
        'restaurant_rank',
        'restaurant_placedetails',
        'restaurant_linkplace',
        'restaurant_image',
        'area_id',
        'restaurant_desc',
        'restaurant_status'
    ];

    public function area(){
        return $this->belongsTo('App\Models\Area', 'area_id', 'area_id');
    }
}
