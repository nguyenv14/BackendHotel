<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    public $timestamps = false;
    protected $fillable = [
    'admin_email' ,  'admin_password' ,  'admin_email' ,  'admin_name' ,  'admin_phone' , 'hotel_id'/* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'admin_id'; /* Khóa Chính */
   protected $table =   'tbl_admin'; /* Tên Bảng */

    /* 1 Admin Thuộc Có Nhiều Roles */
    public function roles(){
        return $this->belongsToMany('App\Models\Roles');
    }
    public function getAuthPassword(){
        return $this->admin_password;
    }
    public function viewRoles(){
        return $this->roles()->first();
    }
    public function hasRoles($role){
        return null !== $this->roles()->where('roles_name',$role)->first(); /* Mặc định trả về null , nếu có dữ liệu thì trả về dữ liệu */
    }
    public function hasAnyRoles($roles){
        return null !== $this->roles()->whereIn('roles_name',$roles)->first();
    }
  
}