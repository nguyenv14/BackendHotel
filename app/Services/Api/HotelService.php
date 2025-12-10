<?php
namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use App\Models\Evaluate;
use App\Models\GalleryHotel;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\OrderDetails;
use App\Models\Room;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HotelService
{
    public function getRoomHotelByID($hotel_id)
    {
        $rooms = Room::with(['typesroom', 'galleriesroom'])
            ->where('hotel_id', $hotel_id)
            ->get();
        return ApiResponse::success($rooms);
    }

    public function getEvaluateHotelByID($hotel_id)
    {
        // Lấy 5 đánh giá mới nhất cùng quan hệ room và typeroom
        $evaluate_hotel = Evaluate::with(['room.typeroom'])
            ->where('hotel_id', $hotel_id)
            ->orderBy('evaluate_id', 'DESC')
            ->take(5)
            ->get();

        // Chuyển đổi dữ liệu sang format reviews
        $reviews = $evaluate_hotel->map(function($item) {
            // Tính điểm trung bình
            $totalPoint = $item->evaluate_loaction_point 
                        + $item->evaluate_service_point
                        + $item->evaluate_price_point
                        + $item->evaluate_sanitary_point
                        + $item->evaluate_convenient_point;

            $avgPoint = number_format($totalPoint / 5, 1);

            // Xác định nhãn đánh giá
            if ($avgPoint >= 4.5) {
                $ratingLabel = 'Tuyệt vời';
            } elseif ($avgPoint >= 3.5) {
                $ratingLabel = 'Tốt';
            } elseif ($avgPoint >= 2.5) {
                $ratingLabel = 'Trung bình';
            } else {
                $ratingLabel = 'Kém';
            }


            // Lấy tên phòng + số giường
            $roomType = optional($item->room)->room_name;
            if(optional($item->room->typeroom)->type_room_bed) {
                $roomType .= ' - ' . $item->room->typeroom->type_room_bed . ' giường';
            }

            return [
                'evaluate_id' => $item->evaluate_id,
                'customer_id' => $item->customer_id,
                'customer_name' =>  $item->customer_name,
                'evaluate' => [
                    'evaluate_title' => $item->evaluate_title,
                    'evaluate_content' => $item->evaluate_content,
                    'created_at' => $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : null,
                    'room_type' => $roomType,
                    'rating' => $avgPoint,
                    'rating_label' => $ratingLabel
                ]
            ];
        });
        return ApiResponse::success($reviews);
    }


    public function getDetailsHotelByID($hotel_id)
    {
        // Lấy thông tin khách sạn
        $hotel = Hotel::where('hotel_id', $hotel_id)->first();

        if (! $hotel) {
            return ApiResponse::error("Không tìm thấy khách sạn", 404);
        }

        $folder = preg_replace('/\s+/', '', $hotel->hotel_name);
        $host   = asset('public/fontend/assets/img/hotel/gallery_' . $folder);

        // Lấy video của khách sạn (type = 2)
        $video = GalleryHotel::where('hotel_id', $hotel_id)
            ->where('gallery_hotel_type', 2)
            ->first();
        if ($video) {
            $video->gallery_hotel_image = $host . '/' . $video->gallery_hotel_image;
        }
        // Lấy danh sách hình ảnh của khách sạn (type = 1)
        $images_hotel = GalleryHotel::where('hotel_id', $hotel_id)
            ->where('gallery_hotel_type', 1)
            ->get();
        $images_hotel = $images_hotel->map(function ($item) use ($host) {
            $item->gallery_hotel_image = $host . '/' . $item->gallery_hotel_image;
            return $item;
        });
        $evaluate = $this->evaluateHotel($hotel_id);

        $data = [
            'hotel'    => $hotel,
            'video'    => $video,
            'images'   => $images_hotel,
            'evaluate' => $evaluate,
        ];

        return ApiResponse::success($data);
    }

    public function getHotels(): JsonResponse
    {
        return $this->respondWithSummaries(
            $this->getHotelsQuery(12),
            $this->getActiveCoupons(),
            'Không tìm thấy khách sạn hợp lệ!'
        );
    }

    public function getFlashSaleHotels(): JsonResponse
    {
        return $this->respondWithSummaries(
            $this->getHotelsQuery(5),
            $this->getActiveCoupons(),
            'Không tìm thấy khách sạn flash sale hợp lệ!'
        );
    }

    public function getHotelList(?int $hotelType): JsonResponse
    {
        return $this->respondWithDetails(
            Hotel::query()->where('hotel_type', $hotelType)->get(),
            'Không truy xuất được dữ liệu'
        );
    }

    public function getHotelById(?int $hotelId): JsonResponse
    {
        return $this->respondWithDetails(
            Hotel::query()->where('hotel_id', $hotelId)->get(),
            'Không truy xuất được dữ liệu'
        );
    }

    public function getHotelListByArea(?int $areaId): JsonResponse
    {
        return $this->respondWithDetails(
            Hotel::query()->where('area_id', $areaId)->get(),
            'Không truy xuất được dữ liệu'
        );
    }

    public function getHotelFavouriteList($favourites): JsonResponse
    {
        $hotelIds = $this->normalizeFavouriteIds($favourites);

        if (empty($hotelIds)) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        $hotels = Hotel::query()
            ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id')
            ->whereIn('hotel_id', $hotelIds)
            ->get();

        if ($hotels->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        return ApiResponse::success($this->formatHotelSearchData($hotels), 'Thành công!');
    }

    public function recommendation(?int $customerId): JsonResponse
    {
        if (! $customerId) {
            return ApiResponse::error('Thiếu mã khách hàng', 422);
        }

        $hotelIds = $this->calculateRecommendedHotelIds($customerId);

        if ($hotelIds->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        $hotels = Hotel::query()
            ->whereIn('hotel_id', $hotelIds)
            ->get();

        if ($hotels->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        return ApiResponse::success($this->formatHotelDetailsData($hotels), 'Thành công!');
    }

    private function respondWithSummaries(Collection $hotels, Collection $coupons, string $emptyMessage): JsonResponse
    {
        $data = $this->formatHotelsData($hotels, $coupons);

        if (empty($data)) {
            return ApiResponse::error($emptyMessage, 404);
        }

        return ApiResponse::success([
            'count' => count($data),
            'items' => $data,
        ], 'Thành công!');
    }

    private function respondWithDetails(Collection $hotels, string $emptyMessage): JsonResponse
    {
        if ($hotels->isEmpty()) {
            return ApiResponse::error($emptyMessage, 404);
        }

        return ApiResponse::success($this->formatHotelDetailsData($hotels), 'Thành công!');
    }

    public function getActiveCoupons(): Collection
    {
        $timeNow = Carbon::now('Asia/Ho_Chi_Minh');

        return Coupon::inRandomOrder()
            ->where('coupon_end_date', '>=', $timeNow)
            ->where('coupon_start_date', '<=', $timeNow)
            ->where('coupon_qty_code', '>', 0)
            ->get();
    }

    private function getHotelsQuery(int $limit = 5, int $status = 1): Collection
    {
        return Hotel::with(['area'])
            ->where('hotel_status', $status)
            ->take($limit)
            ->get();
    }

    public function formatHotelsData(Collection $hotels, Collection $coupons): array
    {
        return $hotels->reduce(function (array $carry, Hotel $hotel) use ($coupons) {
            $roomPrices = TypeRoom::whereHas('room', fn($query) => $query->where('hotel_id', $hotel->hotel_id))
                ->get(['type_room_price', 'type_room_price_sale', 'type_room_condition']);

            if ($roomPrices->isEmpty()) {
                return $carry;
            }

            $basePrice = $roomPrices->min('type_room_price');
            $room      = $roomPrices->firstWhere('type_room_price', $basePrice);

            $priceSale = $basePrice;
            if ($room && $room->type_room_condition == 1) {
                $priceSale -= $basePrice * $room->type_room_price_sale / 100;
            }

            $coupon         = $coupons->isNotEmpty() ? $coupons->random() : null;
            $couponDiscount = $coupon ? ($coupon->coupon_price_sale ?? 0) : 0;
            $priceSaleEnd   = $priceSale - ($priceSale * $couponDiscount / 100);

            $carry[] = [
                'hotel_id'          => $hotel->hotel_id,
                'hotel_name'        => $hotel->hotel_name,
                'hotel_rank'        => $hotel->hotel_rank,
                'hotel_image'       => $hotel->hotel_image,
                'hotel_area'        => $hotel->area->area_name ?? null,
                'hotel_price'       => (int) $basePrice,
                'hotel_price_sale'  => (int) $priceSale,
                'coupon_code'       => $coupon ? ($coupon->coupon_name_code ?? null) : null,
                'coupon_discount'   => $couponDiscount,
                'hotel_price_final' => (int) $priceSaleEnd,
                'evaluate'          => $this->evaluateHotel($hotel->hotel_id),
                'order_time'        => $this->orderTime($hotel->hotel_id),
            ];

            return $carry;
        }, []);
    }

    private function normalizeFavouriteIds($favourites): array
    {
        if (empty($favourites) || $favourites == 1) {
            return [];
        }

        $decoded = is_string($favourites) ? json_decode($favourites, true) : $favourites;

        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->pluck('hotel_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function calculateRecommendedHotelIds(int $customerId): Collection
    {
        $limit = 5;

        $targetRatings = DB::table('tbl_evaluate')
            ->select('hotel_id', DB::raw('
                AVG((evaluate_loaction_point + evaluate_service_point + evaluate_price_point +
                     evaluate_sanitary_point + evaluate_convenient_point) / 5) AS average_score
            '))
            ->where('customer_id', $customerId)
            ->groupBy('hotel_id')
            ->pluck('average_score', 'hotel_id')
            ->toArray();

        if (empty($targetRatings)) {
            return collect();
        }

        $similarities   = [];
        $otherCustomers = DB::table('tbl_evaluate')
            ->where('customer_id', '!=', $customerId)
            ->distinct()
            ->pluck('customer_id');

        foreach ($otherCustomers as $otherCustomerId) {
            $otherRatingsResult = DB::table('tbl_evaluate')
                ->select(
                    'hotel_id',
                    DB::raw('AVG((evaluate_loaction_point + evaluate_service_point + evaluate_price_point +
                      evaluate_sanitary_point + evaluate_convenient_point) / 5) AS average_score')
                )
                ->where('customer_id', $otherCustomerId)
                ->groupBy('hotel_id')
                ->get();

            $otherRatings = [];
            foreach ($otherRatingsResult as $row) {
                $otherRatings[$row->hotel_id] = $row->average_score;
            }

            $dotProduct = 0;
            $norm1      = 0;
            $norm2      = 0;

            foreach ($targetRatings as $hotelId => $rating1) {
                if (isset($otherRatings[$hotelId])) {
                    $rating2 = $otherRatings[$hotelId];
                    $dotProduct += $rating1 * $rating2;
                    $norm1 += $rating1 ** 2;
                    $norm2 += $rating2 ** 2;
                }
            }

            $similarity = ($norm1 && $norm2) ? $dotProduct / (sqrt($norm1) * sqrt($norm2)) : 0;
            if ($similarity > 0) {
                $similarities[$otherCustomerId] = $similarity;
            }
        }

        arsort($similarities);

        $recommendedHotels = collect();
        foreach ($similarities as $similarCustomerId => $similarity) {
            $hotels = DB::table('tbl_evaluate')
                ->select('hotel_id', DB::raw('
                    AVG((evaluate_loaction_point + evaluate_service_point + evaluate_price_point +
                         evaluate_sanitary_point + evaluate_convenient_point) / 5) AS average_score
                '))
                ->where('customer_id', $similarCustomerId)
                ->groupBy('hotel_id')
                ->orderByDesc('average_score')
                ->limit($limit)
                ->get();

            $recommendedHotels = $recommendedHotels->merge($hotels);

            if ($recommendedHotels->count() >= $limit) {
                break;
            }
        }

        return $recommendedHotels->unique('hotel_id')->take($limit)->pluck('hotel_id');
    }

    public function evaluateHotel(int $hotelId): array
    {
        $evaluate = Evaluate::where('hotel_id', $hotelId)->get();
        $count    = $evaluate->count();

        if ($count === 0) {
            return [
                'avg'    => 0,
                'status' => 'Chưa Có Đánh Giá',
                'count'  => 0,
            ];
        }

        $avg = (
            $evaluate->avg('evaluate_loaction_point') +
            $evaluate->avg('evaluate_service_point') +
            $evaluate->avg('evaluate_price_point') +
            $evaluate->avg('evaluate_sanitary_point') +
            $evaluate->avg('evaluate_convenient_point')
        ) / 5;

        $avg = round($avg, 1);

        $status = match (true) {
            $avg <= 0 => 'Chưa Có Đánh Giá',
            $avg <= 2 => 'Trung Bình',
            $avg <= 3 => 'Tốt',
            $avg <= 4 => 'Tuyệt Vời',
            default   => 'Xuất Sắc',
        };
        return [    
            'avg'    => $avg,
            'status' => $status,
            'count'  => $count,
        ];
    }

    public function orderTime(int $hotelId): string
    {
        Carbon::setLocale('vi');
        $order = OrderDetails::where('hotel_id', $hotelId)->orderBy('order_details_id', 'DESC')->first();

        if (! $order) {
            return 'Chưa có đơn đặt nào';
        }

        $created = Carbon::parse($order->created_at, 'Asia/Ho_Chi_Minh');
        $now     = Carbon::now('Asia/Ho_Chi_Minh');

        return 'Vừa đặt cách đây ' . $created->diffForHumans($now);
    }

    private function formatHotelSearchData(Collection $data): array
    {
        return $data->map(function ($item) {
            return [
                'id'          => $item->hotel_id,
                'searchName'  => $item->hotel_name,
                'searchPrice' => $item->hotel_price_average,
                'searchArea'  => $item->area_name,
                'searchImage' => 'hotel/' . $item->hotel_image,
                'searchRank'  => $item->hotel_rank,
                'type'        => 1,
            ];
        })->values()->all();
    }

    private function formatHotelDetailsData(Collection $result): array
    {
        return $result->map(function (Hotel $hotel) {
            $rooms = Room::where('hotel_id', $hotel->hotel_id)->get();

            $roomData = $rooms->map(function (Room $room) {
                return [
                    'room_id'               => $room->room_id,
                    'hotel_id'              => $room->hotel_id,
                    'room_name'             => $room->room_name,
                    'gallery_room'          => GalleryRoom::where('room_id', $room->room_id)->get(),
                    'roomTypes'             => TypeRoom::where('room_id', $room->room_id)->get(),
                    'room_amount_of_people' => $room->room_amount_of_people,
                    'room_acreage'          => $room->room_acreage,
                    'room_view'             => $room->room_view,
                    'room_status'           => $room->room_status,
                    'created_at'            => $room->created_at,
                    'updated_at'            => $room->updated_at,
                    'deleted_at'            => $room->deleted_at,
                ];
            })->all();

            return [
                'hotel_id'           => $hotel->hotel_id,
                'hotel_name'         => $hotel->hotel_name,
                'hotel_rank'         => $hotel->hotel_rank,
                'hotel_type'         => $hotel->hotel_type,
                'brand_id'           => $hotel->brand_id,
                'evaluates'          => Evaluate::where('hotel_id', $hotel->hotel_id)->get(),
                'service_change'     => ServiceCharge::where('hotel_id', $hotel->hotel_id)->first(),
                'brand'              => $hotel->brand,
                'rooms'              => $roomData,
                'area'               => $hotel->area,
                'gallery_hotel'      => GalleryHotel::where('hotel_id', $hotel->hotel_id)
                    ->where('gallery_hotel_type', 1)
                    ->get(),
                'hotel_placedetails' => $hotel->hotel_placedetails,
                'hotel_linkplace'    => $hotel->hotel_linkplace,
                'hotel_jfameplace'   => $hotel->hotel_jfameplace,
                'hotel_image'        => $hotel->hotel_image,
                'hotel_desc'         => $hotel->hotel_desc,
                'hotel_tag_keyword'  => $hotel->hotel_tag_keyword,
                'hotel_view'         => $hotel->hotel_view,
                'hotel_status'       => $hotel->hotel_status,
                'created_at'         => $hotel->created_at,
                'updated_at'         => $hotel->updated_at,
            ];
        })->all();
    }
}
