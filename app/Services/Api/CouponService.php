<?php

namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class CouponService
{
    public function getAvailableCoupons(): JsonResponse
    {
        $coupons = $this->queryActiveCoupons();

        if ($coupons->isEmpty()) {
            return ApiResponse::error('Không có mã giảm giá hợp lệ!', 404);
        }

        $items = $coupons
            ->map(fn (Coupon $coupon) => $this->transformCoupon($coupon))
            ->values();

        return ApiResponse::success([
            'count' => $items->count(),
            'items' => $items,
        ], 'Thành công!');
    }

    private function queryActiveCoupons(): Collection
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');

        return Coupon::query()
            ->where('coupon_end_date', '>=', $now)
            ->where('coupon_start_date', '<=', $now)
            ->where('coupon_qty_code', '>', 0)
            ->get();
    }

    private function transformCoupon(Coupon $coupon): array
    {
        return [
            'coupon_id' => $coupon->coupon_id,
            'coupon_name' => $coupon->coupon_name,
            'coupon_name_code' => $coupon->coupon_name_code,
            'coupon_desc' => $coupon->coupon_desc,
            'coupon_qty_code' => $coupon->coupon_qty_code,
            'coupon_condition' => $coupon->coupon_condition,
            'coupon_price_sale' => $coupon->coupon_price_sale,
            'coupon_start_date' => $coupon->coupon_start_date,
            'coupon_end_date' => $coupon->coupon_end_date,
            'condition' => 'Điều kiện và thể lệ chương trình',
            'expiry' => sprintf(
                'Hạn sử dụng %s - %s | Nhập mã trước khi thanh toán',
                Carbon::parse($coupon->coupon_start_date)->format('d/m/Y'),
                Carbon::parse($coupon->coupon_end_date)->format('d/m/Y')
            ),
            'link' => '#',
        ];
    }
}

