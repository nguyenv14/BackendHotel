<?php
namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use App\Models\Hotel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AIService
{
    private HotelService $hotelService;
    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }
    public function getHotelRecommendationPopular()
    {
        $baseUrl = env('AI_SERVICE_URL');
        $url     = $baseUrl . 'recommend/popular';
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $result   = $response->json();
                $data     = $result['data'];
                $hotelIds = collect($data['recommendations'])
                    ->pluck('hotel_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                if (empty($hotelIds)) {
                    return ApiResponse::success([], 'Không có khách sạn được đề xuất.');
                }
                $hotels = Hotel::query()
                    ->whereIn('hotel_id', $hotelIds)
                    ->with('area') // Giả sử model Hotel có relationship 'area'
                    ->get();
                $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
                $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
                return ApiResponse::success($this->hotelService->formatHotelsData($hotels, $coupons), 'Lấy danh sách khách sạn thành công');
            } else {
                return ApiResponse::error('Lấy danh sách khách sạn thất bại', 400);
            }
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function getHotelRecommendationForSimilar($hotel_id)
    {
        $baseUrl = env('AI_SERVICE_URL');
        $url     = $baseUrl . 'recommend/similar/' . $hotel_id;
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $result   = $response->json();
                $data     = $result['data'];
                $hotelIds = collect($data['recommendations'])
                    ->pluck('hotel_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                if (empty($hotelIds)) {
                    return ApiResponse::success([], 'Không có khách sạn được đề xuất.');
                }
                $hotels = Hotel::query()
                    ->whereIn('hotel_id', $hotelIds)
                    ->whereNotIn('hotel_id', [$hotel_id])
                    ->with('area') // Giả sử model Hotel có relationship 'area'
                    ->get();
                $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
                $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
                return ApiResponse::success($this->hotelService->formatHotelsData($hotels, $coupons), 'Lấy danh sách khách sạn thành công');
            } else {
                return ApiResponse::error('Lấy danh sách khách sạn thất bại', 400);
            }
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }
}
