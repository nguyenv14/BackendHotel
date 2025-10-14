<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyConfig extends Model
{
   public $timestamps = false;
   protected $fillable = [
    'company_name' ,  'company_hostline' , 'company_mail' ,  'company_address', 'company_slogan', 'company_copyright'   /* Trường Trong Bảng */
   ]; 
   protected $primaryKey =  'company_id'; /* Khóa Chính */
   protected $table =   'tbl_company_config'; /* Tên Bảng */
}
