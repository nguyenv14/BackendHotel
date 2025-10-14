<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableRestaurant extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'table_id';
    protected $table = 'tbl_table_restaurant';
    protected $fillable = [
        'restaurant_id',
        'table_name',
        'table_price',
        'table_condition',
        'table_quantity',
        'table_status',
    ];
}
