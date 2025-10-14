<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class ApiCouponController extends Controller{
    public function getCoupons(Request $request){
        $coupons = Coupon::get();
        if(count($coupons) > 0){
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công!',
                'data' => $coupons,
            ]) ;
        }else{
            return response()->json([
                'status_code' => 404,
                'message' => 'Thất bại!',
                'data' => null,
            ]) ;
        }
    }
}



