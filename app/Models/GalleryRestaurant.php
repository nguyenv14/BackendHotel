<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryRestaurant extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'gallery_restaurant_id';

    protected $table = 'tbl_gallery_restaurant';

    protected $fillable = [
        'restaurant_id',
        'gallery_restaurant_name',
        'gallery_restaurant_image',
        'gallery_restaurant_description',
        'gallery_restaurant_status',
    ];
}
