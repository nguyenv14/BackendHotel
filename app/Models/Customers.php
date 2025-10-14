<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customers extends Authenticatable implements JWTSubject
{
   use SoftDeletes;

   public $timestamps = false;

   protected $dates = [
      'deleted_at',
   ];

   protected $fillable = [
      'customer_name',
      'customer_phone',
      'customer_email',
      'customer_password',
      'customer_status',
      'customer_ip',
      'customer_located',
      'customer_device',  /* Trường Trong Bảng */
   ];
   protected $primaryKey = 'customer_id'; /* Khóa Chính */
   protected $table = 'tbl_customers'; /* Tên Bảng */

   public function social()
   {
      return $this->belongsTo('App\Models\Social', 'customer_id', 'user');
   }
   public function socialTrashed()
   {
      return $this->belongsTo('App\Models\Social', 'customer_id', 'user')->onlyTrashed();
   }

   // JWTSubject methods
   public function getJWTIdentifier()
   {
      return $this->getKey(); // ID người dùng
   }

   public function getJWTCustomClaims()
   {
      return [
         'name' => $this->customer_name,
         'email' => $this->customer_email,
      ]; // Các claims bổ sung nếu cần
   }
}
