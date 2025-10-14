<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;

class ApiSearchController extends Controller
{
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $input = $request->searchText;
        $type = $request->typeSearch;
        if($type == 1){
            $data = Hotel::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id')
                ->orWhere('area_name', 'like', '%'.$input.'%')
                ->orWhere('hotel_name', 'LIKE', '%' . $input . '%')
                ->orWhere('hotel_price_average', 'LIKE', '%' . $input . '%')
                ->get();
        } else {
            $data = Restaurant::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_restaurant.area_id')
                ->orWhere('area_name', 'like', '%'.$input.'%')
                ->orWhere('restaurant_name', 'LIKE', '%' . $input . '%')
                ->get();
        }
        
        if($type == 1){
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công',
                'data' => $this->convertJsonData($data, $type),
            ]);
        }else{
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công',
                'data' => $this->convertJsonData($data, $type),
            ]);
        }
    }

    public function filterSearch(Request $request): \Illuminate\Http\JsonResponse
    {
        $input = $request->searchText;
        $type = $request->typeSearch;
        $sortType = $request->sortType;
        $areaId = $request->areaId;
        $ranking = $request->ranking;
        if($type == 1){
            $priceMin = $request->priceMin * 1000;
            $priceMax = $request->priceMax * 1000;
            $typeHotel = $request->typeHotel;
            $data = Hotel::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id');

            if($areaId != 0){
                $data = $data->where('tbl_hotel.area_id', $areaId);
            }
            if($sortType != 0){
                if($sortType == 1){
                    $data = $data->orderByDesc('hotel_price_average');
                }else if($sortType == 2){
                    $data = $data->orderBy('hotel_price_average', "ASC");
                }else if($sortType == 3){
                    $data = $data->orderByDesc('hotel_name');
                }else if($sortType == 4){
                    $data = $data->orderBy('hotel_name', "ASC");
                }
            }
            $data = $data->whereBetween('hotel_price_average', [$priceMin, $priceMax]);
            if($ranking){
                $data = $data->where('hotel_rank', $ranking);
            }
            if($typeHotel != 0){
                $data = $data->where('hotel_type', $typeHotel);
            }
            $data = $data->get();
        }else{
            $data = Restaurant::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_restaurant.area_id')
                ->where('restaurant_name', 'LIKE', '%' . $input . '%');

            if($areaId != 0){
                $data = $data->where('tbl_restaurant.area_id', $areaId);
            }

            if($sortType != 0){
                if($sortType == 1){
                    $data = $data->orderByDesc('restaurant_name');
                }else if($sortType == 2){
                    $data = $data->orderBy('restaurant_name', "ASC");
                }
            }
            if($ranking){
                $data = $data->where('restaurant_rank', $ranking);
            }
            $data = $data->get();
        }
        if($type == 0){
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công',
                'data' => $this->convertJsonData($data, $type),
            ]);
        }else{
            return response()->json([
                'status_code' => 201,
                'message' => 'Thành công',
                'data' => $this->convertJsonData($data, $type),
            ]);
        }
    }

    public function getRestaurantOrHotelFavourite(Request $request): \Illuminate\Http\JsonResponse
    {
        $favourites = json_decode($request->favourites, true);
        $type = $request->type;
        $restaurant_id = [];
        foreach ($favourites as $key => $value) {
            $restaurant_id[$key] = $value['hotel_id'];
        }
        if($type == 0){
            $data = Restaurant::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_restaurant.area_id')
                ->whereIn('restaurant_id', $restaurant_id)->get();
        }else{
            $data = Hotel::query()
                ->join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id')
                ->whereIn('hotel_id', $restaurant_id)->get();
        }
        if($type == 1){
            return response()->json([
                'status_code' => 200,
                'message' => 'Khách Sạn',
                'data' => $this->convertJsonData($data, $type),
            ]);
        }else{
            return response()->json([
                'status_code' => 201,
                'message' => 'Nhà Hàng',
                'data' => $this->convertJsonData($data, $type),
            ]);
        }
    }

    public function convertJsonData($data, int $type): array
    {
        $dataReturn = [];
        if($type == 1){
            foreach($data as $item){
                $value['id'] = $item->hotel_id;
                $value['searchName'] = $item->hotel_name;
                $value['searchPrice'] = $item->hotel_price_average;
                $value['searchArea'] = $item->area_name;
                $value['searchImage'] = 'hotel/' . $item->hotel_image;
                $value['searchRank'] = $item->hotel_rank;
                $value['type'] = 1;
                $dataReturn[] = $value;
            }
        }else{
            foreach($data as $item){
                $value['id'] = $item->restaurant_id;
                $value['searchName'] = $item->restaurant_name;
                $value['searchArea'] = $item->area_name;
                $value['searchImage'] = 'restaurant/' . $item->restaurant_image;
                $value['searchRank'] = $item->restaurant_rank;
                $value['type'] = 0;
                $dataReturn[] = $value;
            }
        }
        return $dataReturn;
    }

    public function handle_mastersearch(Request $request)
    {
        $type_hotel = $request->type_hotel;
        $location = $request->location_id;
        $hotel_name = $request->hotel_name;
        $brand_id = $request->brand_id;
        $hotel_type = [];
        $list_location = [];
        $brand_list = [];
        if ($location == 0) {
            $areas = Area::get();
            foreach ($areas as $key => $area) {
                $list_location[$key] = $area->area_id;
            }
        } else {
            $list_location = [$location];
        }
        if ($type_hotel == 0) {
            $hotel_type = [1, 2, 3];
        } else {
            $hotel_type = [$type_hotel];
        }

        if ($brand_id == 0) {
            $brands = Brand::get();
            foreach ($brands as $key => $brand) {
                $brand_list[$key] = $brand->brand_id;
            }
        } else {
            $brand_list = [$brand_id];
        }

        // dd($list_location);

        $hotels = Hotel::join('tbl_room', 'tbl_hotel.hotel_id', '=', 'tbl_room.hotel_id')
            ->join('tbl_area', 'tbl_hotel.area_id', '=', 'tbl_area.area_id')
            ->join('tbl_type_room', 'tbl_type_room.room_id', '=', 'tbl_room.room_id')
            ->whereIn('tbl_hotel.area_id', $list_location)
            ->whereIn('tbl_hotel.brand_id', $brand_list)
            ->whereIn('tbl_hotel.hotel_type', $hotel_type)
            ->where('tbl_hotel.hotel_name', 'like', '%' . $hotel_name . '%')
            // ->whereBetween('tbl_type_room.type_room_price',[$price_start,$price_end])
            ->get();

        $hotels = $this->super_unique($hotels, 'hotel_name');

        if (count($hotels) > 0) {
            $data = $this->convertDataToJson($hotels);
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

    function super_unique($array, $key)
    {
        $temp_array = [];
        foreach ($array as $v) {
            if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] = $v;
        }
        $array = array_values($temp_array);
        return $array;
    }

    public function convertDataToJson($result)
    {
        foreach ($result as $dt) {
            $evaluates = Evaluate::where("hotel_id", $dt->hotel_id)->get();
            $service = ServiceCharge::where("hotel_id", $dt->hotel_id)->first();
            $rooms = Room::where('hotel_id', $dt->hotel_id)->get();
            $room_data = [];
            $gallery_hotel = GalleryHotel::where("hotel_id", $dt->hotel_id)->get();
            foreach ($rooms as $room) {
                $roomTypes = TypeRoom::where("room_id", $room->room_id)->get();
                $gallery_room = GalleryRoom::where("room_id", $room->room_id)->get();
                $room_data[] = array(
                    "room_id" => $room->room_id,
                    "hotel_id" => $room->hotel_id,
                    "room_name" => $room->room_name,
                    "gallery_room" => $gallery_room,
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
                "evaluates" => $evaluates,
                "service_change" => $service,
                "brand" => $dt->brand,
                "rooms" => $room_data,
                "area" => $dt->area,
                "gallery_hotel" => $gallery_hotel,
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

    // public function getProductBySearch(Request $request){
    //     $type_hotel = $request->type_hotel;
    //     $location = $request->location_id;
    //     // $category = Category::where("category_name", $category_name)->first();
    //     if($number == 0){
    //         if($category != null){
    //             $all_product = Product::where('product_name', 'like', $searchbyname_format)->where("category_id", $category->category_id)->where('product_price', '>=', $priceMin)->where("product_price", '<=', $priceMax)->get();
    //         }else{
    //             $all_product = Product::where('product_name', 'like', $searchbyname_format)->where('product_price', '>=', $priceMin)->where("product_price", '<=', $priceMax)->get();
    //         }
    //     }else if($number == 1){
    //         if($category != null){
    //             $all_product = Product::where('product_name', 'like', $searchbyname_format)->where("category_id", $category->category_id)->where('product_price', '>=', $priceMin)->where("product_price", '<=', $priceMax)->orderby("product_price", "DESC")->get();
    //         }else{
    //             $all_product = Product::where('product_name', 'like', $searchbyname_format)->where('product_price', '>=', $priceMin)->where("product_price", '<=', $priceMax)->orderby("product_price", "DESC")->get();
    //         }
    //     }else if($number == 2){
    //         if($category != null){
    //             $all_product = Product::where('product_name', 'like', $searchbyname_format)->where("category_id", $category->category_id)->where('product_price', '>=', $priceMin)->where("product_price", '<=', $priceMax)->orderby("product_price", "ASC")->get();
    //         }else{
    //             $all_product = Product::where('product_name', 'like', $searchbyname_format)->where('product_price', '>=', $priceMin)->where("product_price", '<=', $priceMax)->orderby("product_price", "ASC")->get();
    //         }
    //     }
    //     return $this->fetchJsonProduct($all_product);
    // }
}
