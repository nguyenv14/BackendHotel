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

class SearchService
{
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
        $typeHotel = (int) ($filters['type_hotel'] ?? 0);
        $location = (int) ($filters['location_id'] ?? 0);
        $hotelName = trim((string) ($filters['hotel_name'] ?? ''));
        $brandId = (int) ($filters['brand_id'] ?? 0);

        $locations = $location === 0
            ? Area::query()->pluck('area_id')->all()
            : [$location];

        $hotelTypes = $typeHotel === 0 ? [1, 2, 3] : [$typeHotel];

        $brandIds = $brandId === 0
            ? Brand::query()->pluck('brand_id')->all()
            : [$brandId];

        $hotels = Hotel::query()
            ->join('tbl_room', 'tbl_hotel.hotel_id', '=', 'tbl_room.hotel_id')
            ->join('tbl_area', 'tbl_hotel.area_id', '=', 'tbl_area.area_id')
            ->join('tbl_type_room', 'tbl_type_room.room_id', '=', 'tbl_room.room_id')
            ->whereIn('tbl_hotel.area_id', $locations)
            ->whereIn('tbl_hotel.brand_id', $brandIds)
            ->whereIn('tbl_hotel.hotel_type', $hotelTypes)
            ->where('tbl_hotel.hotel_name', 'like', '%' . $hotelName . '%')
            ->get();

        $unique = $hotels->unique('hotel_id')->values();

        if ($unique->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        return ApiResponse::success(
            $this->convertHotelDetails($unique),
            'Thành công!'
        );
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

