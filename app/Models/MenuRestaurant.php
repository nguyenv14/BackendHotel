<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuRestaurant extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'menu_item_id';

    protected $table = 'tbl_menu_restaurant';

    protected $fillable = [
        'restaurant_id',
        'menu_item_name',
        'menu_item_description',
        'menu_item_image',
        'menu_item_price',
        'menu_item_status'
    ];
}
