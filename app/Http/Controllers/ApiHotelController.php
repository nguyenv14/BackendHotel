<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Evaluate;
use App\Models\GalleryHotel;
use App\Models\GalleryRoom;
use App\Models\OrderDetails;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiHotelController extends Controller
{

    protected function getActiveCoupons()
    {
        $timeNow = Carbon::now('Asia/Ho_Chi_Minh');
        return Coupon::inRandomOrder()
            ->where('coupon_end_date', '>=', $timeNow)
            ->where('coupon_start_date', '<=', $timeNow)
            ->where('coupon_qty_code', '>', 0)
            ->get();
    }

    protected function getHotelsQuery(int $limit = 5, int $status = 1)
    {
        return Hotel::with(['area'])
            ->where('hotel_status', $status)
            ->take($limit)
            ->get();
    }

    protected function formatHotelsData($hotels, $coupons)
    {
        $data = [];

        foreach ($hotels as $hotel) {
            // Lấy giá phòng thấp nhất
            $roomPrices = TypeRoom::whereHas('room', function ($query) use ($hotel) {
                $query->where('hotel_id', $hotel->hotel_id);
            })->get(['type_room_price', 'type_room_price_sale', 'type_room_condition']);

            if ($roomPrices->isEmpty()) continue;

            $basePrice = $roomPrices->min('type_room_price');
            $room = $roomPrices->firstWhere('type_room_price', $basePrice);

            // Tính giá sale
            $price_sale = $basePrice;
            if ($room && $room->type_room_condition == 1) {
                $price_sale = $basePrice - ($basePrice * $room->type_room_price_sale / 100);
            }

            // Lấy coupon ngẫu nhiên nếu có
            $coupon = $coupons->isNotEmpty() ? $coupons->random(1)->first() : null;
            $coupon_name = $coupon->coupon_name_code ?? null;
            $coupon_discount = $coupon->coupon_price_sale ?? 0;

            // Tính giá cuối cùng
            $price_sale_end = $price_sale - ($price_sale * $coupon_discount / 100);

            $data[] = [
                'hotel_id' => $hotel->hotel_id,
                'hotel_name' => $hotel->hotel_name,
                'hotel_rank' => $hotel->hotel_rank,
                'hotel_image' => asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image),
                'hotel_area' => $hotel->area->area_name ?? null,
                'hotel_price' => (int)$basePrice,
                'hotel_price_sale' => (int)$price_sale,
                'coupon_code' => $coupon_name,
                'coupon_discount' => $coupon_discount,
                'hotel_price_final' => (int)$price_sale_end,
                'evaluate' => $this->evaluateHotel($hotel->hotel_id),
                'order_time' => $this->orderTime($hotel->hotel_id),
            ];
        }

        return $data;
    }

    public function getHotels()
    {
        $coupons = $this->getActiveCoupons();
        $hotels = $this->getHotelsQuery(12);

        $data = $this->formatHotelsData($hotels, $coupons);

        if (empty($data)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Không tìm thấy khách sạn hợp lệ!'
            ], 404);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Thành công!',
            'count' => count($data),
            'data' => $data
        ]);
    }

    public function getFlashSaleHotels()
    {
        $coupons = $this->getActiveCoupons();
        $hotels = $this->getHotelsQuery(5);

        $data = $this->formatHotelsData($hotels, $coupons);

        if (empty($data)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Không tìm thấy khách sạn flash sale hợp lệ!'
            ], 404);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Thành công!',
            'count' => count($data),
            'data' => $data
        ]);
    }
    private function evaluateHotel($hotel_id)
    {
        $evaluate = Evaluate::where('hotel_id', $hotel_id)->get();
        $count = $evaluate->count();

        if ($count == 0) {
            return [
                'avg' => 0,
                'status' => 'Chưa Có Đánh Giá',
                'count' => 0
            ];
        }

        $avg = ($evaluate->avg('evaluate_loaction_point') +
                $evaluate->avg('evaluate_service_point') +
                $evaluate->avg('evaluate_price_point') +
                $evaluate->avg('evaluate_sanitary_point') +
                $evaluate->avg('evaluate_convenient_point')) / 5;

        $avg = round($avg, 1);

        if ($avg == 0) $status = 'Chưa Có Đánh Giá';
        elseif ($avg <= 2) $status = 'Trung Bình';
        elseif ($avg <= 3) $status = 'Tốt';
        elseif ($avg <= 4) $status = 'Tuyệt Vời';
        else $status = 'Xuất Sắc';

        return [
            'avg' => $avg,
            'status' => $status,
            'count' => $count
        ];
    }

    private function orderTime($hotel_id)
    {
        Carbon::setLocale('vi');
        $order = OrderDetails::where('hotel_id', $hotel_id)->orderby('order_details_id', 'DESC')->first();

        if (!$order) {
            return 'Chưa có đơn đặt nào';
        }

        $created = Carbon::create($order->created_at, 'Asia/Ho_Chi_Minh');
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        return 'Vừa đặt cách đây ' . $created->diffForHumans($now);
    }
    

    public function getHotelList(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = Hotel::query()->where("hotel_type", $request->hotel_type)->get();
        if ($result) {
            $data = $this->convertDataToJson($result);
            return response()->json([
                'status_code' => 200,
                'message'     => 'Thành công!',
                'data'        => $data,
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'message'     => 'Không truy xuất được dữ liệu',
                'data'        => null,
            ]);
        }
    }

    public function getHotelById(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = Hotel::query()->where("hotel_id", $request->hotel_id)->get();
        if (count($result) > 0) {
            $data = $this->convertDataToJson($result);
            return response()->json([
                'status_code' => 200,
                'message'     => 'Thành công!',
                'data'        => $data,
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'message'     => 'Không truy xuất được dữ liệu',
                'data'        => null,
            ]);
        }
    }

    public function getHotelListByArea(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = Hotel::query()->where("area_id", $request->area_id)->get();
        if ($result) {
            // dd($result);
            $data = $this->convertDataToJson($result);
            return response()->json([
                'status_code' => 200,
                'message'     => 'Thành công!',
                'data'        => $data,
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'message'     => 'Không truy xuất được dữ liệu',
                'data'        => null,
            ]);
        }
    }

    public function getHotelFavouriteList(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->favourites != 1) {
            $favourites = json_decode($request->favourites, true);
            $hotel_id   = [];

            foreach ($favourites as $key => $value) {
                $hotel_id[$key] = $value['hotel_id'];
            }
            $hotels = Hotel::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id')
                ->whereIn('hotel_id', $hotel_id)->get();
            if ($hotels) {
                $data = $this->convertJsonData($hotels);
                return response()->json([
                    'status_code' => 200,
                    'message'     => 'Thanh cong',
                    'data'        => $data,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message'     => 'Không truy xuất được dữ liệu',
                    'data'        => null,
                ]);
            }
        } else {
            return response()->json([
                'status_code' => 404,
                'message'     => 'Không truy xuất được dữ liệu',
                'data'        => null,
            ]);
        }
    }

    public function convertJsonData($data): array
    {
        $dataReturn = [];
        foreach ($data as $item) {
            $value['id']          = $item->hotel_id;
            $value['searchName']  = $item->hotel_name;
            $value['searchPrice'] = $item->hotel_price_average;
            $value['searchArea']  = $item->area_name;
            $value['searchImage'] = 'hotel/' . $item->hotel_image;
            $value['searchRank']  = $item->hotel_rank;
            $value['type']        = 1;
            $dataReturn[]         = $value;
        }
        return $dataReturn;
    }

    public function Recommendation(Request $request): \Illuminate\Http\JsonResponse
    {
        $customerId    = $request->customer_id;
        $limit         = 5;
        $targetRatings = DB::table('tbl_evaluate')
            ->select('hotel_id', DB::raw('
            AVG((evaluate_loaction_point + evaluate_service_point + evaluate_price_point +
                 evaluate_sanitary_point + evaluate_convenient_point) / 5) AS average_score
        '))
            ->where('customer_id', $customerId)
            ->groupBy('hotel_id')
            ->pluck('average_score', 'hotel_id')
            ->toArray();
//        dump($targetRatings);
        $similarities   = [];
        $otherCustomers = DB::table('tbl_evaluate')
            ->where('customer_id', '!=', $customerId)
            ->distinct()
            ->pluck('customer_id');
//        dump($otherCustomers);
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
                    $norm1 += pow($rating1, 2);
                    $norm2 += pow($rating2, 2);
                }
            }

            $similarity = ($norm1 && $norm2) ? $dotProduct / (sqrt($norm1) * sqrt($norm2)) : 0;
            if ($similarity > 0) {
                $similarities[$otherCustomerId] = $similarity;
            }
        }
//        dump($similarities);
        arsort($similarities);
//        dump($similarities);
        $recommendedHotels = collect();
//        dump($recommendedHotels);

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
//            dump($hotels);
            $recommendedHotels = $recommendedHotels->merge($hotels);
            if ($recommendedHotels->count() >= $limit) {
                break;
            }
        }
//        dump($recommendedHotels);
        $hotelIds = $recommendedHotels->unique('hotel_id')->take($limit)->pluck('hotel_id');
        $datas    = Hotel::query()
            ->whereIn('hotel_id', $hotelIds)
            ->get();
        if (count($datas) > 0) {
            $data = $this->convertDataToJson($datas);
            return response()->json([
                'status_code' => 200,
                'message'     => 'Thành công!',
                'data'        => $data,
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'message'     => 'Không truy xuất được dữ liệu',
                'data'        => null,
            ]);
        }
    }

    public function calculateCosineSimilarity($customerId1, $customerId2): float | int
    {
        $ratings1 = DB::table('tbl_evaluate')
            ->where('customer_id', $customerId1)
            ->pluck('average_score', 'hotel_id')
            ->toArray();

        $ratings2 = DB::table('tbl_evaluate')
            ->where('customer_id', $customerId2)
            ->pluck('average_score', 'hotel_id')
            ->toArray();

        $dotProduct = 0;
        $norm1      = 0;
        $norm2      = 0;

        foreach ($ratings1 as $hotelId => $rating1) {
            if (isset($ratings2[$hotelId])) {
                $rating2 = $ratings2[$hotelId];
                $dotProduct += $rating1 * $rating2;
                $norm1 += pow($rating1, 2);
                $norm2 += pow($rating2, 2);
            }
        }

        return ($norm1 && $norm2) ? $dotProduct / (sqrt($norm1) * sqrt($norm2)) : 0;
    }

    public function convertDataToJson($result)
    {
        foreach ($result as $dt) {
            $evaluates     = Evaluate::where("hotel_id", $dt->hotel_id)->get();
            $service       = ServiceCharge::where("hotel_id", $dt->hotel_id)->first();
            $rooms         = Room::where('hotel_id', $dt->hotel_id)->get();
            $room_data     = [];
            $gallery_hotel = GalleryHotel::where("hotel_id", $dt->hotel_id)->where('gallery_hotel_type', 1)->get();
            foreach ($rooms as $room) {
                $roomTypes    = TypeRoom::where("room_id", $room->room_id)->get();
                $gallery_room = GalleryRoom::where("room_id", $room->room_id)->get();
                $room_data[]  = [
                    "room_id"               => $room->room_id,
                    "hotel_id"              => $room->hotel_id,
                    "room_name"             => $room->room_name,
                    "gallery_room"          => $gallery_room,
                    "roomTypes"             => $roomTypes,
                    "room_amount_of_people" => $room->room_amount_of_people,
                    "room_acreage"          => $room->room_acreage,
                    "room_view"             => $room->room_view,
                    "room_status"           => $room->room_status,
                    "created_at"            => $room->created_at,
                    "updated_at"            => $room->updated_at,
                    "deleted_at"            => $room->deleted_at,
                ];
            }

            $data[] = [
                "hotel_id"           => $dt->hotel_id,
                "hotel_name"         => $dt->hotel_name,
                "hotel_rank"         => $dt->hotel_rank,
                "hotel_type"         => $dt->hotel_type,
                "brand_id"           => $dt->brand_id,
                "evaluates"          => $evaluates,
                "service_change"     => $service,
                "brand"              => $dt->brand,
                "rooms"              => $room_data,
                "area"               => $dt->area,
                "gallery_hotel"      => $gallery_hotel,
                "hotel_placedetails" => $dt->hotel_placedetails,
                "hotel_linkplace"    => $dt->hotel_linkplace,
                "hotel_jfameplace"   => $dt->hotel_jfameplace,
                "hotel_image"        => $dt->hotel_image,
                "hotel_desc"         => $dt->hotel_desc,
                "hotel_tag_keyword"  => $dt->hotel_tag_keyword,
                "hotel_view"         => $dt->hotel_view,
                "hotel_status"       => $dt->hotel_status,
                "created_at"         => $dt->created_at,
                "updated_at"         => $dt->updated_at,
            ];
        }
        return $data;
    }
}
