<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigWeb extends Model
{
   public $timestamps = false;
   protected $fillable = [
    'config_image' ,  'config_title' , 'config_content' ,  'config_type'   /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'config_id'; /* Khóa Chính */
   protected $table =   'tbl_configweb'; /* Tên Bảng */
}
