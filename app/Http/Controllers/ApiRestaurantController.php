<?php

namespace App\Http\Controllers;

use App\Models\Evaluate;
use App\Models\GalleryHotel;
use App\Models\GalleryRestaurant;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\MenuRestaurant;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ApiRestaurantController extends Controller
{
    public function getRestaurantByArea(Request $request)
    {
        $restaurant = Restaurant::query()->where('area_id', $request->area_id)->get();
        $data = $this->convertDataToJson($restaurant);
        return response()->json([
            'status_code' => 200,
            'message' => 'Thành công!',
            'data' => $data,
        ]);
    }

    public function getRestaurantById(Request $request)
    {
        $result = Restaurant::where("restaurant_id", $request->restaurant_id)->get();
        if (count($result) > 0) {
            $data = $this->convertDataToJson($result);
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công!',
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'message' => 'Không truy xuất được dữ liệu',
                'data' => null,
            ]);
        }
    }

    public function getRestaurantFavourite(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->favourites != 1) {
            $favourites = json_decode($request->favourites, true);
//            $favourites = $request->favourites;
            $hotel_id = array();

            foreach ($favourites as $key => $value) {
                $hotel_id[$key] = $value['restaurant_id'];
            }
            $hotels = Restaurant::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_restaurant.area_id')
                ->whereIn('restaurant_id', $hotel_id)->get();
            if ($hotels) {
                $data = $this->convertFavouriteJsonData($hotels);
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Thanh cong!',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không truy xuất được dữ liệu',
                    'data' => null,
                ]);
            }
        } else {
            return response()->json([
                'status_code' => 404,
                'message' => 'Không truy xuất được dữ liệu',
                'data' => null,
            ]);
        }
    }

    public function convertFavouriteJsonData($data): array
    {
        $dataReturn = [];
        foreach ($data as $item) {
            $value['id'] = $item->restaurant_id;
            $value['searchName'] = $item->restaurant_name;
            $value['searchArea'] = $item->area_name;
            $value['searchImage'] = 'restaurant/' . $item->restaurant_image;
            $value['searchRank'] = $item->restaurant_rank;
            $value['type'] = 1;
            $dataReturn[] = $value;
        }
        return $dataReturn;
    }

    public function convertDataToJson($result)
    {
        $data = [];
        foreach ($result as $dt) {
//            $evaluates = Evaluate::where("hotel_id", $dt->hotel_id)->get();
//            $service = ServiceCharge::where("hotel_id", $dt->hotel_id)->first();
//            $rooms = Room::where('hotel_id', $dt->hotel_id)->get();
//            $room_data = [];
            $gallerys = GalleryRestaurant::query()->where("restaurant_id", $dt->restaurant_id)->get();
            $menus = MenuRestaurant::query()->where("restaurant_id", $dt->restaurant_id)->get();
//            foreach ($rooms as $room) {
//                $roomTypes = TypeRoom::where("room_id", $room->room_id)->get();
//                $gallery_room = GalleryRoom::where("room_id", $room->room_id)->get();
//                $room_data[] = array(
//                    "room_id" => $room->room_id,
//                    "hotel_id" => $room->hotel_id,
//                    "room_name" => $room->room_name,
//                    "gallery_room" => $gallery_room,
//                    "roomTypes" => $roomTypes,
//                    "room_amount_of_people" => $room->room_amount_of_people,
//                    "room_acreage" => $room->room_acreage,
//                    "room_view" => $room->room_view,
//                    "room_status" => $room->room_status,
//                    "created_at" => $room->created_at,
//                    "updated_at" => $room->updated_at,
//                    "deleted_at" => $room->deleted_at,
//                );
//            }

            $data[] = array(
                "restaurant_id" => $dt->restaurant_id,
                "restaurant_name" => $dt->restaurant_name,
                "restaurant_rank" => $dt->restaurant_rank,
//                "hotel_type" => $dt->hotel_type,
//                "brand_id" => $dt->brand_id,
//                "evaluates" => $evaluates,
//                "service_change" => $service,
//                "brand" => $dt->brand,
//                "rooms" => $room_data,
                "area" => $dt->area,
                "gallery_restaurant" => $gallerys,
                "restaurant_placedetails" => $dt->restaurant_placedetails,
                "restaurant_linkplace" => $dt->restaurant_linkplace,
//                "hotel_jfameplace" => $dt->hotel_jfameplace,
                "restaurant_image" => $dt->restaurant_image,
                "restaurant_desc" => $dt->restaurant_desc,
//                "hotel_tag_keyword" => $dt->hotel_tag_keyword,
//                "hotel_view" => $dt->hotel_view,
                "menus" => $menus,
                "restaurant_status" => $dt->restaurant_status,
                "created_at" => $dt->created_at,
                "updated_at" => $dt->updated_at,
            );
        }
        return $data;
    }
}
