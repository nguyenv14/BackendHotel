<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Roles extends Model
{
    public $timestamps = false;
    protected $fillable = [
    'roles_name',  /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'roles_id'; /* Khóa Chính */
   protected $table =   'tbl_roles'; /* Tên Bảng */
   /* 1 Roles Thuộc Nhiều Admin */
   public function admin(){
    return $this->belongsToMany('App\Models\Admin');
   }
}
