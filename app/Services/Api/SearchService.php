<?php

namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Evaluate;
use App\Models\GalleryHotel;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Room;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class SearchService
{

    private HotelService $hotelService;

    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }
    
    public function search(?string $text, ?int $typeSearch): JsonResponse
    {
        $queryText = trim((string) $text);
        $type = $typeSearch ?? 1;

        $results = $this->searchHotels($queryText);

        return ApiResponse::success(
            $this->convertSearchCollection($results),
            'Thành công'
        );
    }

    public function filterSearch(array $filters): JsonResponse
    {
        $results = $this->filterHotels($filters);

        return ApiResponse::success(
            $this->convertSearchCollection($results),
            'Thành công'
        );
    }

    public function getFavourites(array $favourites): JsonResponse
    {
        $ids = collect($favourites)
            ->pluck('hotel_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return ApiResponse::error('Không có dữ liệu yêu thích', 404);
        }

        $data = Hotel::query()
            ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id')
            ->whereIn('hotel_id', $ids)
            ->get();

        return ApiResponse::success(
            $this->convertSearchCollection($data),
            'Khách Sạn'
        );
    }

    public function masterSearch(array $filters): JsonResponse
    {
        // Parse filters
        $inputText = trim((string) ($filters['input_text'] ?? $filters['hotel_name'] ?? ''));
        $sortType = $filters['sort'] ?? $filters['sortType'] ?? 'relevant';
        
        // Pagination
        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : 1;
        $perPage = isset($filters['per_page']) ? max(1, min(100, (int) $filters['per_page'])) : 9;
        
        // Handle area_id - can be single value or array
        $areaIds = $this->parseFilterValue($filters['area_id'] ?? $filters['location_id'] ?? 0, function() {
            return Area::query()->pluck('area_id')->all();
        });
        
        // Handle hotel_type - can be single value or array
        $hotelTypes = $this->parseFilterValue($filters['hotel_type'] ?? $filters['type_hotel'] ?? 0, function() {
            return [1, 2, 3, 4, 5];
        });
        
        // Handle hotel_rank - can be single value or array
        $hotelRanks = $this->parseFilterValue($filters['hotel_rank'] ?? 0, function() {
            return [1, 2, 3, 4, 5];
        });
        
        // Handle brand_id - can be single value or array
        $brandIds = $this->parseFilterValue($filters['brand_id'] ?? 0, function() {
            return Brand::query()->pluck('brand_id')->all();
        });
        
        // Handle price range - only max price (price_end)
        $priceMax = isset($filters['price_end']) ? (float) $filters['price_end'] : null;
        $price = $filters['price'] ?? null;
        
        // If price is provided as single value, use it as max
        if ($price !== null && $priceMax === null) {
            $priceMax = (float) $price;
        }
        
        // Build query
        $query = Hotel::query()
            ->select('tbl_hotel.*')
            ->distinct()
            ->join('tbl_room', 'tbl_hotel.hotel_id', '=', 'tbl_room.hotel_id')
            ->join('tbl_area', 'tbl_hotel.area_id', '=', 'tbl_area.area_id')
            ->join('tbl_type_room', 'tbl_type_room.room_id', '=', 'tbl_room.room_id')
            ->where('tbl_hotel.hotel_status', 1); // Only active hotels
        
        // Filter by area_id
        if (!empty($areaIds)) {
            $query->whereIn('tbl_hotel.area_id', $areaIds);
        }
        
        // Filter by hotel_type
        if (!empty($hotelTypes)) {
            $query->whereIn('tbl_hotel.hotel_type', $hotelTypes);
        }
        
        // Filter by hotel_rank
        if (!empty($hotelRanks)) {
            $query->whereIn('tbl_hotel.hotel_rank', $hotelRanks);
        }
        
        // Filter by brand_id
        if (!empty($brandIds)) {
            $query->whereIn('tbl_hotel.brand_id', $brandIds);
        }
        
        // Filter by hotel name (input_text)
        if (!empty($inputText)) {
            $query->where('tbl_hotel.hotel_name', 'like', '%' . $inputText . '%');
        }
        
        // Note: Price filter will be applied after formatting data (by hotel_price_final)
        // because we need to filter by final price (after discount), not base price
        
        // Apply sorting (only for non-price sorts, price will be sorted after formatting)
        if (!in_array($sortType, ['price-low', 'price-high'])) {
            $this->applyMasterSearchSort($query, $sortType);
        } else {
            // For price sorting, we'll sort after formatting by final price
            $this->applyMasterSearchSort($query, 'relevant');
        }
        
        // Get unique hotel IDs first for counting
        $hotelIds = $query->pluck('tbl_hotel.hotel_id')->unique()->values();
        $totalCount = $hotelIds->count();
        
        if ($totalCount === 0) {
            return ApiResponse::success([
                'data' => [],
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => 0,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0,
                ],
            ], 'Không tìm thấy khách sạn nào');
        }
        
        // For price sorting or price filtering, we need to get all hotels first, format, filter/sort, then paginate
        if (in_array($sortType, ['price-low', 'price-high']) || $priceMax !== null) {
            // Get all hotels that match filters
            $allHotels = Hotel::whereIn('hotel_id', $hotelIds->all())
                ->with('area')
                ->get();
            
            // Get active coupons for formatting
            $coupons = $this->hotelService->getActiveCoupons();
            
            // Format hotels data
            $formattedData = $this->hotelService->formatHotelsData($allHotels, $coupons);
            
            // Filter by price (max price only - filter by final price)
            if ($priceMax !== null && $priceMax > 0) {
                $formattedData = array_filter($formattedData, function($hotel) use ($priceMax) {
                    $finalPrice = $hotel['hotel_price_final'] ?? 0;
                    return $finalPrice <= $priceMax;
                });
                // Re-index array after filter
                $formattedData = array_values($formattedData);
            }
            
            // Sort by final price if needed
            if (in_array($sortType, ['price-low', 'price-high'])) {
                usort($formattedData, function($a, $b) use ($sortType) {
                    $priceA = $a['hotel_price_final'] ?? 0;
                    $priceB = $b['hotel_price_final'] ?? 0;
                    
                    if ($sortType === 'price-low') {
                        return $priceA <=> $priceB; // ASC
                    } else {
                        return $priceB <=> $priceA; // DESC
                    }
                });
            }
            
            // Paginate after filtering and sorting
            $totalCount = count($formattedData);
            $lastPage = (int) ceil($totalCount / $perPage);
            $offset = ($page - 1) * $perPage;
            $paginatedData = array_slice($formattedData, $offset, $perPage);
            
            return ApiResponse::success([
                'data' => $paginatedData,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $totalCount,
                    'last_page' => $lastPage,
                    'from' => $totalCount > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $totalCount),
                ],
            ], 'Thành công!');
        }
        
        // For other sorts, check if we need to filter by price
        // If price filter exists, we need to format all hotels first, then filter and paginate
        if ($priceMax !== null && $priceMax > 0) {
            // Get all hotels that match filters
            $allHotels = Hotel::whereIn('hotel_id', $hotelIds->all())
                ->with('area')
                ->get();
            
            // Get active coupons for formatting
            $coupons = $this->hotelService->getActiveCoupons();
            
            // Format hotels data
            $formattedData = $this->hotelService->formatHotelsData($allHotels, $coupons);
            
            // Filter by price (max price only - filter by final price)
            $formattedData = array_filter($formattedData, function($hotel) use ($priceMax) {
                $finalPrice = $hotel['hotel_price_final'] ?? 0;
                return $finalPrice <= $priceMax;
            });
            // Re-index array after filter
            $formattedData = array_values($formattedData);
            
            // If sorting by rating, sort after filtering
            if ($sortType === 'rating') {
                usort($formattedData, function($a, $b) {
                    $ratingA = $a['evaluate']['avg'] ?? 0;
                    $ratingB = $b['evaluate']['avg'] ?? 0;
                    return $ratingB <=> $ratingA; // DESC
                });
            }
            
            // Paginate after filtering and sorting
            $totalCount = count($formattedData);
            $lastPage = (int) ceil($totalCount / $perPage);
            $offset = ($page - 1) * $perPage;
            $paginatedData = array_slice($formattedData, $offset, $perPage);
            
            return ApiResponse::success([
                'data' => $paginatedData,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $totalCount,
                    'last_page' => $lastPage,
                    'from' => $totalCount > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $totalCount),
                ],
            ], 'Thành công!');
        }
        
        // For other sorts without price filter, paginate IDs first
        $paginatedIds = $hotelIds->slice(($page - 1) * $perPage, $perPage)->all();
        
        // Get hotels by IDs
        $hotels = Hotel::whereIn('hotel_id', $paginatedIds)
            ->with('area')
            ->get();
        
        // If sorting by rating, we need to sort after getting data
        if ($sortType === 'rating') {
            $hotels = $this->sortByRating($hotels);
        }
        
        // Get active coupons for formatting
        $coupons = $this->hotelService->getActiveCoupons();
        
        // Format hotels data
        $formattedData = $this->hotelService->formatHotelsData($hotels, $coupons);
        
        $lastPage = (int) ceil($totalCount / $perPage);
        
        return ApiResponse::success([
            'data' => $formattedData,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalCount,
                'last_page' => $lastPage,
                'from' => $totalCount > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => min($page * $perPage, $totalCount),
            ],
        ], 'Thành công!');
    }
    
    /**
     * Parse filter value - handle single value, array, or 0 (all)
     */
    private function parseFilterValue($value, callable $getAllCallback): array
    {
        if ($value === 0 || $value === null || $value === '') {
            return $getAllCallback();
        }
        
        if (is_array($value)) {
            return array_filter(array_map('intval', $value));
        }
        
        return [(int) $value];
    }
    
    /**
     * Apply sorting for master search
     */
    private function applyMasterSearchSort($query, string $sortType): void
    {
        switch ($sortType) {
            case 'price-low':
                $query->orderBy('tbl_type_room.type_room_price', 'ASC');
                break;
            case 'price-high':
                $query->orderBy('tbl_type_room.type_room_price', 'DESC');
                break;
            case 'stars':
                $query->orderBy('tbl_hotel.hotel_rank', 'DESC');
                break;
            case 'rating':
                // Will be sorted after fetching data
                break;
            case 'relevant':
            default:
                // Keep default order (usually by hotel_id or created_at)
                $query->orderBy('tbl_hotel.hotel_id', 'ASC');
                break;
        }
    }
    
    /**
     * Sort hotels by rating (average evaluation score)
     */
    private function sortByRating($hotels)
    {
        return $hotels->sortByDesc(function ($hotel) {
            $evaluates = Evaluate::where('hotel_id', $hotel->hotel_id)->get();
            
            if ($evaluates->isEmpty()) {
                return 0;
            }
            
            $avg = (
                $evaluates->avg('evaluate_loaction_point') +
                $evaluates->avg('evaluate_service_point') +
                $evaluates->avg('evaluate_price_point') +
                $evaluates->avg('evaluate_sanitary_point') +
                $evaluates->avg('evaluate_convenient_point')
            ) / 5;
            
            return round($avg, 1);
        })->values();
    }

    private function searchHotels(string $text)
    {
        return Hotel::query()
            ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id')
            ->where(function ($query) use ($text) {
                $query->where('area_name', 'like', '%' . $text . '%')
                    ->orWhere('hotel_name', 'like', '%' . $text . '%')
                    ->orWhere('hotel_price_average', 'like', '%' . $text . '%');
            })
            ->get();
    }

    private function filterHotels(array $filters)
    {
        $priceMin = (float) ($filters['priceMin'] ?? 0) * 1000;
        $priceMax = (float) ($filters['priceMax'] ?? 0) * 1000;
        $typeHotel = (int) ($filters['typeHotel'] ?? 0);
        $areaId = (int) ($filters['areaId'] ?? 0);
        $ranking = $filters['ranking'] ?? null;
        $sortType = (int) ($filters['sortType'] ?? 0);

        $query = Hotel::query()
            ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id');

        if ($areaId !== 0) {
            $query->where('tbl_hotel.area_id', $areaId);
        }

        $this->applyHotelSort($query, $sortType);

        $query->whereBetween('hotel_price_average', [$priceMin, $priceMax]);

        if (!empty($ranking)) {
            $query->where('hotel_rank', $ranking);
        }

        if ($typeHotel !== 0) {
            $query->where('hotel_type', $typeHotel);
        }

        return $query->get();
    }

    private function applyHotelSort($query, int $sortType): void
    {
        match ($sortType) {
            1 => $query->orderByDesc('hotel_price_average'),
            2 => $query->orderBy('hotel_price_average', 'ASC'),
            3 => $query->orderByDesc('hotel_name'),
            4 => $query->orderBy('hotel_name', 'ASC'),
            default => null,
        };
    }

    private function convertSearchCollection($collection): array
    {
        return $collection->map(function ($item) {
            return [
                'id' => $item->hotel_id,
                'searchName' => $item->hotel_name,
                'searchPrice' => $item->hotel_price_average,
                'searchArea' => $item->area_name,
                'searchImage' => 'hotel/' . $item->hotel_image,
                'searchRank' => $item->hotel_rank,
                'type' => 1,
            ];
        })->values()->all();
    }

    public function getBrands(): JsonResponse
    {
        $brands = Brand::where('brand_status', 1)
            ->select('brand_id', 'brand_name', 'brand_desc')
            ->get();

        return ApiResponse::success($brands, 'Thành công!');
    }

    public function getHotelTypes(): JsonResponse
    {
        $hotelTypes = [
            ['id' => 1, 'name' => 'Khách sạn', 'value' => 1],
            ['id' => 2, 'name' => 'Khách sạn căn hộ', 'value' => 2],
            ['id' => 3, 'name' => 'Khu nghỉ dưỡng', 'value' => 3],
            ['id' => 4, 'name' => 'Căn hộ cao cấp', 'value' => 4],
            ['id' => 5, 'name' => 'Nhà nguyên căn', 'value' => 5],
        ];

        return ApiResponse::success($hotelTypes, 'Thành công!');
    }

    private function convertHotelDetails($hotels): array
    {
        return $hotels->map(function ($hotel) {
            $evaluates = Evaluate::query()->where('hotel_id', $hotel->hotel_id)->get();
            $service = ServiceCharge::query()->where('hotel_id', $hotel->hotel_id)->first();
            $rooms = Room::query()->where('hotel_id', $hotel->hotel_id)->get();
            $galleryHotel = GalleryHotel::query()->where('hotel_id', $hotel->hotel_id)->get();

            $roomData = $rooms->map(function ($room) {
                $roomTypes = TypeRoom::query()->where('room_id', $room->room_id)->get();
                $galleryRoom = GalleryRoom::query()->where('room_id', $room->room_id)->get();

                return [
                    'room_id' => $room->room_id,
                    'hotel_id' => $room->hotel_id,
                    'room_name' => $room->room_name,
                    'gallery_room' => $galleryRoom,
                    'roomTypes' => $roomTypes,
                    'room_amount_of_people' => $room->room_amount_of_people,
                    'room_acreage' => $room->room_acreage,
                    'room_view' => $room->room_view,
                    'room_status' => $room->room_status,
                    'created_at' => $room->created_at,
                    'updated_at' => $room->updated_at,
                    'deleted_at' => $room->deleted_at,
                ];
            });

            return [
                'hotel_id' => $hotel->hotel_id,
                'hotel_name' => $hotel->hotel_name,
                'hotel_rank' => $hotel->hotel_rank,
                'hotel_type' => $hotel->hotel_type,
                'brand_id' => $hotel->brand_id,
                'evaluates' => $evaluates,
                'service_change' => $service,
                'brand' => $hotel->brand,
                'rooms' => $roomData,
                'area' => $hotel->area,
                'gallery_hotel' => $galleryHotel,
                'hotel_placedetails' => $hotel->hotel_placedetails,
                'hotel_linkplace' => $hotel->hotel_linkplace,
                'hotel_jfameplace' => $hotel->hotel_jfameplace,
                'hotel_image' => $hotel->hotel_image,
                'hotel_desc' => $hotel->hotel_desc,
                'hotel_tag_keyword' => $hotel->hotel_tag_keyword,
                'hotel_view' => $hotel->hotel_view,
                'hotel_status' => $hotel->hotel_status,
                'created_at' => $hotel->created_at,
                'updated_at' => $hotel->updated_at,
            ];
        })->values()->all();
    }
}

