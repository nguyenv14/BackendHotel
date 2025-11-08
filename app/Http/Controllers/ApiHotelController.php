<?php

namespace App\Http\Controllers;

use App\Services\Api\HotelService;
use Illuminate\Http\Request;

class ApiHotelController extends Controller
{
    private HotelService $hotelService;

    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    public function getHotels()
    {
        return $this->hotelService->getHotels();
    }

    public function getFlashSaleHotels()
    {
        return $this->hotelService->getFlashSaleHotels();
    }

    public function getHotelList(Request $request)
    {
        return $this->hotelService->getHotelList((int) $request->hotel_type);
    }

    public function getHotelById(Request $request)
    {
        return $this->hotelService->getHotelById((int) $request->hotel_id);
    }

    public function getHotelListByArea(Request $request)
    {
        return $this->hotelService->getHotelListByArea((int) $request->area_id);
    }

    public function getHotelFavouriteList(Request $request)
    {
        return $this->hotelService->getHotelFavouriteList($request->favourites);
    }

    public function Recommendation(Request $request)
    {
        return $this->hotelService->recommendation((int) $request->customer_id);
    }
}
