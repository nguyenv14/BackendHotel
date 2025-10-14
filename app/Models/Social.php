<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Social extends Model
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at' ,
     ]; 

    public $timestamps = false;
    protected $fillable = [
        'provider_user_id', 'provider', 'user',
    ];

    protected $primaryKey = 'user_id';
    protected $table = 'tbl_social';
    public function login(){
        return $this->belongsTo('App\Models\Customers', 'user');
    }
}
