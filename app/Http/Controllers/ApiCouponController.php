<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiCouponController extends Controller{
    public function getCoupons(Request $request){
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $coupons = Coupon::where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
        if ($coupons->count() > 0) {
        // Biến đổi dữ liệu để phù hợp với frontend
        $mappedCoupons = $coupons->map(function ($coupon) {
            return [
                'coupon_id' => $coupon->coupon_id,
                'coupon_name_code' => $coupon->coupon_name_code,
                'coupon_desc' => $coupon->coupon_desc,
                'coupon_qty_code' => $coupon->coupon_qty_code,
                'coupon_condition' => $coupon->coupon_condition,
                'coupon_price_sale' => $coupon->coupon_price_sale,
                'coupon_start_date' => $coupon->coupon_start_date,
                'coupon_end_date' => $coupon->coupon_end_date,
                'condition' => 'Điều kiện và thể lệ chương trình',
                'expiry' => 'Hạn sử dụng ' .
                    Carbon::parse($coupon->coupon_start_date)->format('d/m/Y') .
                    ' - ' .
                    Carbon::parse($coupon->coupon_end_date)->format('d/m/Y') .
                    ' | Nhập mã trước khi thanh toán',
                'link' => '#',
            ];
        });

        return response()->json([
            'status_code' => 200,
            'message' => 'Thành công!',
            'data' => $mappedCoupons,
        ]);
    } else {
        return response()->json([
            'status_code' => 404,
            'message' => 'Không có mã giảm giá hợp lệ!',
            'data' => [],
        ]);
    }
    }
}



