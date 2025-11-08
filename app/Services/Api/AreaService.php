<?php
namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Area;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\TypeRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class AreaService
{
    public function getAreas(): JsonResponse
    {
        $areas = Area::all();

        if ($areas->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        $host = asset('public/fontend/assets/img/area');
        $data = $areas
            ->map(function ($item) use ($host) {
                $item->area_image = $host . '/' . $item->area_image;
                return $item;
            })
            ->values();

        return ApiResponse::success($data, 'Thành công!');
    }

    public function getAreaListHaveHotel(): JsonResponse
    {
        $areas = Area::all();

        $data = $areas
            ->filter(fn($area) => Hotel::where('area_id', $area->area_id)->exists())
            ->values();

        if ($data->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }
        return ApiResponse::success($data, 'Thành công!');
    }

    /**
     * Helper exposed for reuse by other services/controllers.
     */
    public function convertDataToJson(Collection $result): array
    {
        $data = [];
        foreach ($result as $dt) {
            $rooms    = Room::where('hotel_id', $dt->hotel_id)->get();
            $roomData = [];

            foreach ($rooms as $room) {
                $roomTypes = TypeRoom::where('room_id', $room->room_id)->get();

                $roomData[] = [
                    'room_id'               => $room->room_id,
                    'hotel_id'              => $room->hotel_id,
                    'room_name'             => $room->room_name,
                    'roomTypes'             => $roomTypes,
                    'room_amount_of_people' => $room->room_amount_of_people,
                    'room_acreage'          => $room->room_acreage,
                    'room_view'             => $room->room_view,
                    'room_status'           => $room->room_status,
                    'created_at'            => $room->created_at,
                    'updated_at'            => $room->updated_at,
                    'deleted_at'            => $room->deleted_at,
                ];
            }

            $data[] = [
                'hotel_id'           => $dt->hotel_id,
                'hotel_name'         => $dt->hotel_name,
                'hotel_rank'         => $dt->hotel_rank,
                'hotel_type'         => $dt->hotel_type,
                'brand_id'           => $dt->brand_id,
                'brand'              => $dt->brand,
                'rooms'              => $roomData,
                'area'               => $dt->area,
                'hotel_placedetails' => $dt->hotel_placedetails,
                'hotel_linkplace'    => $dt->hotel_linkplace,
                'hotel_jfameplace'   => $dt->hotel_jfameplace,
                'hotel_image'        => $dt->hotel_image,
                'hotel_desc'         => $dt->hotel_desc,
                'hotel_tag_keyword'  => $dt->hotel_tag_keyword,
                'hotel_view'         => $dt->hotel_view,
                'hotel_status'       => $dt->hotel_status,
                'created_at'         => $dt->created_at,
                'updated_at'         => $dt->updated_at,
            ];
        }

        return $data;
    }
}
