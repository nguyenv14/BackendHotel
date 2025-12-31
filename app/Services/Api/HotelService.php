<?php
namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\CompanyConfig;
use App\Models\ConfigWeb;
use App\Models\Coupon;
use App\Models\Evaluate;
use App\Models\FacilitiesHotel;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Nette\Utils\Random;
use Storage;

class HotelService
{
    public function getRoomHotelByID(int $hotel_id, string $checkIn, string $checkOut, int $nights)
    {
        $checkInDate  = Carbon::createFromFormat('d-m-Y', $checkIn)->format('Y-m-d');
        $checkOutDate = Carbon::createFromFormat('d-m-Y', $checkOut)->format('Y-m-d');

        $bookedRoomTypeCount = OrderDetails::query()
            ->join('tbl_order', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
            ->where('tbl_order_details.hotel_id', $hotel_id)
            ->where('start_day', '<', $checkOutDate)
            ->where('end_day', '>', $checkInDate)
            ->whereNotIn('tbl_order.order_status', [-2, -1])
            ->groupBy('type_room_id')
            ->select(
                'type_room_id',
                DB::raw('COUNT(*) as booked_count')
            )
            ->get()
            ->keyBy('type_room_id');

        $rooms = Room::with(['typesroom', 'galleriesroom'])
            ->where('hotel_id', $hotel_id)
            ->get();

        foreach ($rooms as $room) {
            foreach ($room->typesroom as $type) {
                $bookedCount                        = $bookedRoomTypeCount[$type->type_room_id]->booked_count ?? 0;
                $type->type_room_available_quantity = max($type->type_room_quantity - $bookedCount, 0);
            }
        }

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
        $reviews = $evaluate_hotel->map(function ($item) {
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
            if (optional($item->room->typeroom)->type_room_bed) {
                $roomType .= ' - ' . $item->room->typeroom->type_room_bed . ' giường';
            }

            return [
                'evaluate_id'   => $item->evaluate_id,
                'customer_id'   => $item->customer_id,
                'customer_name' => $item->customer_name,
                'evaluate'      => [
                    'evaluate_title'   => $item->evaluate_title,
                    'evaluate_content' => $item->evaluate_content,
                    'created_at'       => $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : null,
                    'room_type'        => $roomType,
                    'rating'           => $avgPoint,
                    'rating_label'     => $ratingLabel,
                ],
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
        $facilities = FacilitiesHotel::query()->whereIn('facilitieshotel_id', $hotel->facilities)->get();
        $hotel->facilities = $facilities->map(function ($item) {
            return ['facilitieshotel_id' => $item->facilitieshotel_id, 'facilitieshotel_name' => $item->facilitieshotel_name,'facilitieshotel_image' => $item->getFacilitiesHotelImageAttribute()];
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

    /**
     * Process and format semantic search results
     *
     * @param array $searchResults Raw search results from AI service
     * @param string $query Original search query
     * @return JsonResponse
     */
    public function processSemanticSearchResults(array $searchResults, string $query): JsonResponse
    {
        // If no results from semantic search
        if (empty($searchResults)) {
            Log::warning('Semantic search returned no results', ['query' => $query]);

            return ApiResponse::success([
                'query' => $query,
                'count' => 0,
                'items' => [],
            ], 'Không tìm thấy kết quả phù hợp. Vui lòng thử lại với từ khóa khác.');
        }

        try {
            // Extract hotel IDs from search results
            $hotelIds = collect($searchResults)
                ->pluck('hotel_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($hotelIds)) {
                return ApiResponse::success([
                    'query' => $query,
                    'count' => 0,
                    'items' => [],
                ], 'Không tìm thấy kết quả phù hợp.');
            }

            // Fetch full hotel data from database
            $hotels = Hotel::with(['area'])
                ->whereIn('hotel_id', $hotelIds)
                ->get();

            // Maintain order from search results
            $hotels = $hotels->sortBy(function ($hotel) use ($hotelIds) {
                return array_search($hotel->hotel_id, $hotelIds);
            })->values();

            // Get active coupons
            $coupons = $this->getActiveCoupons();

            // Format hotels data using the same format as other methods
            $formattedData = $this->formatHotelsData($hotels, $coupons);

            // Add relevance scores from search results
            $relevanceScores = collect($searchResults)
                ->keyBy('hotel_id')
                ->map(fn($result) => $result['relevance_score'] ?? 0)
                ->toArray();

            // Merge relevance scores into formatted data
            $formattedData = collect($formattedData)->map(function ($item) use ($relevanceScores) {
                $item['relevance_score'] = $relevanceScores[$item['hotel_id']] ?? 0;
                return $item;
            })->values()->all();

            return ApiResponse::success([
                'count' => count($formattedData),
                'items' => $formattedData,
            ], 'Tìm kiếm thành công');

        } catch (\Exception $e) {
            Log::error('Semantic search processing error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Lỗi khi xử lý kết quả tìm kiếm: ' . $e->getMessage(), 500);
        }
    }

    public function putFileMinio($file, int $companyId = 11): string
    {
        $originalName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $extension = $file->getClientOriginalExtension();

        $fileName = $originalName
        . '_' . Carbon::now()->timestamp
            . '.' . $extension;

        $path = 'hotel/' . $fileName;

        // 1. Upload trước
        FacadesStorage::disk('minio')->putFileAs(
            'hotel',
            $file,
            $fileName
        );
        
        // 2. Lấy hoặc tạo ConfigWeb record
        $config = CompanyConfig::where('company_id', $companyId)->first();
        if (!$config) {
            // Tạo mới nếu chưa có
            $config = new CompanyConfig();
            $config->company_id = $companyId;
            $config->policies = '[]';
            $config->save();
        }
        
        $policies = json_decode($config->policies, true) ?? [];
        // 3. Append policy mới (lưu cả tên đầy đủ có extension)
        $policies[] = [
            'name' => $originalName . '.' . $extension,  // Lưu tên đầy đủ có extension
            'path' => $path,
        ];
        $config->policies = json_encode($policies);
        $config->save();
        return $path;
    }

    public function getAllPolicyFiles(int $companyId): array
    {
        $config = CompanyConfig::where('company_id', $companyId)->firstOrFail();

        $policies = json_decode($config->policies, true);

        if (! is_array($policies)) {
            return [];
        }

        return collect($policies)
            ->filter(fn($item) => isset($item['name'], $item['path']))
            ->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'path' => $item['path'],
                    'url'  => FacadesStorage::disk('minio')->temporaryUrl(
                        $item['path'],
                        now()->addMinutes(15)
                    ),
                    'parsed' => $item['parsed'] ?? false,
                    'parsing' => $item['parsing'] ?? false,
                ];
            })
            ->values()
            ->toArray();
    }

    public function markPolicyFileAsParsing(int $companyId, string $filePath): bool
    {
        try {
            $config = CompanyConfig::where('company_id', $companyId)->first();
            if (!$config) {
                return false;
            }

            $policies = json_decode($config->policies, true) ?? [];
            
            // Find and update the policy file
            foreach ($policies as &$policy) {
                if (isset($policy['path']) && $policy['path'] === $filePath) {
                    $policy['parsing'] = true;
                    $policy['parsing_at'] = now()->toDateTimeString();
                    break;
                }
            }
            
            $config->policies = json_encode($policies);
            $config->save();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error marking policy file as parsing', [
                'company_id' => $companyId,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function markPolicyFileAsParsed(int $companyId, string $filePath): bool
    {
        try {
            $config = CompanyConfig::where('company_id', $companyId)->first();
            if (!$config) {
                return false;
            }

            $policies = json_decode($config->policies, true) ?? [];
            
            // Find and update the policy file
            foreach ($policies as &$policy) {
                if (isset($policy['path']) && $policy['path'] === $filePath) {
                    $policy['parsed'] = true;
                    $policy['parsing'] = false; // Clear parsing status
                    $policy['parsed_at'] = now()->toDateTimeString();
                    break;
                }
            }
            
            $config->policies = json_encode($policies);
            $config->save();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error marking policy file as parsed', [
                'company_id' => $companyId,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function clearPolicyFileParsingStatus(int $companyId, string $filePath): bool
    {
        try {
            $config = CompanyConfig::where('company_id', $companyId)->first();
            if (!$config) {
                return false;
            }

            $policies = json_decode($config->policies, true) ?? [];
            
            // Find and clear parsing status
            foreach ($policies as &$policy) {
                if (isset($policy['path']) && $policy['path'] === $filePath) {
                    $policy['parsing'] = false;
                    break;
                }
            }
            
            $config->policies = json_encode($policies);
            $config->save();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error clearing policy file parsing status', [
                'company_id' => $companyId,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

}
