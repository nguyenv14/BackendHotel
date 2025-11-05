<?php
namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\TypeRoom;
use Google\Service\HangoutsChat\Resource\Rooms;
use Illuminate\Http\Request;

class ApiAreaController extends Controller{
    public function getAreas(Request $request)
    {
        $result = Area::get();

        if ($result->count() > 0) {
    
            $host = asset('public/fontend/assets/img/area');
            
            $data = $result->map(function ($item) use ($host) {
                $item->area_image = $host . '/' . $item->area_image;
                return $item;
            });

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


    public function getAreaListHaveHotel(Request $request){
        $result = Area::get();
        if($result){
            $data = [];
            foreach($result as $dt){
                $hotel = Hotel::where("area_id", $dt->area_id)->first();
                if($hotel){
                    $data[] = $dt;
                }
            }
            // $data = $this->convertDataToJson($result);
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công!',
                'data' => $data,
            ]);
        }else{
            return response()->json([
                'status_code' => 404, 
                'message' => 'Không truy xuất được dữ liệu',
                'data' => null,
            ]) ;
        }
    }
    
    public function convertDataToJson($result){
        foreach($result as $dt){
            
            $rooms = Room::where('hotel_id', $dt->hotel_id)->get();
            $room_data = [];
            foreach($rooms as $room){
                $roomTypes = TypeRoom::where("room_id", $room->room_id)->get();
                
                $room_data[] = array(
                    "room_id" => $room->room_id,
                    "hotel_id" => $room->hotel_id,
                    "room_name" => $room->room_name,
                    "roomTypes" => $roomTypes,
                    "room_amount_of_people" => $room->room_amount_of_people,
                    "room_acreage" => $room->room_acreage,
                    "room_view" => $room->room_view,
                    "room_status" => $room->room_status,
                    "created_at" => $room->created_at,
                    "updated_at" => $room->updated_at,
                    "deleted_at" => $room->deleted_at,
                );
            }

            $data[] = array(
                "hotel_id" => $dt->hotel_id,
                "hotel_name" => $dt->hotel_name,
                "hotel_rank" => $dt->hotel_rank,
                "hotel_type" => $dt->hotel_type,
                "brand_id" => $dt->brand_id,
                "brand" => $dt->brand,
                "rooms" => $room_data,
                "area" => $dt->area,
                "hotel_placedetails" => $dt->hotel_placedetails,
                "hotel_linkplace" => $dt->hotel_linkplace,
                "hotel_jfameplace" => $dt->hotel_jfameplace,
                "hotel_image" => $dt->hotel_image,
                "hotel_desc" => $dt->hotel_desc,
                "hotel_tag_keyword" => $dt->hotel_tag_keyword,
                "hotel_view" => $dt->hotel_view,
                "hotel_status" => $dt->hotel_status,
                "created_at" => $dt->created_at,
                "updated_at" => $dt->updated_at,
            );

        }
        return $data;
    }
}



