<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetailRestaurant extends Model
{
    protected $table = 'tbl_order_details_restaurant';
    protected $primaryKey = 'order_details_id';
    protected $fillable = [
        'order_code',
        'restaurant_id',
        'restaurant_menu_id',
        'restaurant_menu_price',
        'restaurant_menu_quantity',
        'table_restaurant_price',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'restaurant_id');
    }

    public function menu(){
        return $this->belongsTo(MenuRestaurant::class, 'restaurant_menu_id', 'menu_item_id');
    }
}
