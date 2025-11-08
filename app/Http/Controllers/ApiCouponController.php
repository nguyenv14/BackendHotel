<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Api\CouponService;
use Illuminate\Http\Request;

class ApiCouponController extends Controller
{
    private CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function getCoupons(Request $request)
    {
        return $this->couponService->getAvailableCoupons();
    }
}



