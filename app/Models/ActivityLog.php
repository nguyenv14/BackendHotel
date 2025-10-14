<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Location;
use Auth;
class ActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'activitylog_admin_id',
        'activitylog_customer_id',
        'activitylog_admin_name',
        'activitylog_customer_name',
        'activitylog_type',
        'activitylog_proceed',
        'activitylog_ip',
        'activitylog_located',
        'activitylog_device', /* Trường Trong Bảng */
    ];
    protected $primaryKey = 'activitylog_id'; /* Khóa Chính */
    protected $table = 'tbl_activitylog'; /* Tên Bảng */

    public function admin(){
        return $this->belongsTo('App\Models\Admin', 'activitylog_admin_id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customers', 'activitylog_customer_id');
    }

    public static function noteAdminLog($proceed){
        $activitylog = new ActivityLog();
        $activitylog->activitylog_admin_id =Auth::id();
        $activitylog->activitylog_admin_name = Auth::user()->admin_name;
        $activitylog->activitylog_type = 0; 
        $activitylog->activitylog_proceed = $proceed;
        $activitylog->activitylog_ip = request()->ip();
        $currentUserInfo = Location::get($activitylog->activitylog_ip);
        if($currentUserInfo != null){
            $activitylog->activitylog_located = $currentUserInfo->cityName;
        }else {
            $activitylog->activitylog_located = "Không Xác Định";
        }
        $activitylog->activitylog_device = request()->userAgent();
        $activitylog->save();
    }
    public static function noteCustomerLog($customer_id,$customer_name,$proceed){
        $activitylog = new ActivityLog();
        $activitylog->activitylog_customer_id = $customer_id;
        $activitylog->activitylog_customer_name = $customer_name;
        $activitylog->activitylog_type = 1; 
        $activitylog->activitylog_proceed = $proceed;
        $activitylog->activitylog_ip = request()->ip();
        $currentUserInfo = Location::get($activitylog->activitylog_ip);
        if($currentUserInfo != null){
            $activitylog->activitylog_located = $currentUserInfo->cityName;
        }else {
            $activitylog->activitylog_located = "Không Xác Định";
        }
        $activitylog->activitylog_device = request()->userAgent();
        $activitylog->save();
    }
}
