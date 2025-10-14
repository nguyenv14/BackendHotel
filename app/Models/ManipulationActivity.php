<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;
use Location;
class ManipulationActivity extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'manipulation_activity_admin_id',
        'manipulation_activity_admin_name',
        'manipulation_activity_customer_id',
        'manipulation_activity_customer_name',
        'manipulation_activity_type',
        'manipulation_activity_action',
        'manipulation_activity_ip',
        'manipulation_activity_located',
        'manipulation_activity_device',/* Trường Trong Bảng */
    ];
    protected $primaryKey = 'manipulation_activity_id'; /* Khóa Chính */
    protected $table = 'tbl_manipulation_activity'; /* Tên Bảng */

    public function admin(){
        return $this->belongsTo('App\Models\Admin', 'manipulation_activity_admin_id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customers', 'manipulation_activity_customer_id');
    }

    public static function noteManipulationAdmin($action){
        $manipulationActivity = new ManipulationActivity();
        $manipulationActivity->manipulation_activity_admin_id =  Auth::id();
        $manipulationActivity->manipulation_activity_admin_name = Auth::user()->admin_name;
        $manipulationActivity->manipulation_activity_type = 0;
        $manipulationActivity->manipulation_activity_action = $action;
        $manipulationActivity->manipulation_activity_ip = request()->ip();
        //$currentUserInfo = Location::get($manipulationActivity->manipulation_activity_ip);
        $currentUserInfo = null;
        if($currentUserInfo != null){
            $manipulationActivity->manipulation_activity_located = $currentUserInfo->cityName;
        }else {
            $manipulationActivity->manipulation_activity_located = "Không Xác Định";
        }
        $manipulationActivity->manipulation_activity_device = request()->userAgent();
        $manipulationActivity->save();
    }
    public static function noteManipulationCustomer($action){
        $manipulationActivity = new ManipulationActivity();
        if( session()->has('customer_id') &&  session()->has('customer_name')){
            $manipulationActivity->manipulation_activity_customer_id = session()->get('customer_id');
            $manipulationActivity->manipulation_activity_customer_name = session()->get('customer_name');
        }else{
            $manipulationActivity->manipulation_activity_customer_name = 'Khách Vãng Lai';
        }
        $manipulationActivity->manipulation_activity_type = 1;
        $manipulationActivity->manipulation_activity_action = $action;
        $manipulationActivity->manipulation_activity_ip = request()->ip();
        $currentUserInfo = Location::get($manipulationActivity->manipulation_activity_ip);
        $currentUserInfo = null;
        if($currentUserInfo != null){
            $manipulationActivity->manipulation_activity_located = $currentUserInfo->cityName;
        }else {
            $manipulationActivity->manipulation_activity_located = "Không Xác Định";
        }
        $manipulationActivity->manipulation_activity_device = request()->userAgent();
        $manipulationActivity->save();
    }
    /* Sử Dụng Session Để Chống Lag */

}
