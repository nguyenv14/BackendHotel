<?php

namespace App\Http\Controllers;

use App\Services\Api\HotelService;
use App\Services\Api\AIService;
use Illuminate\Http\Request;

class ApiHotelController extends Controller
{
    private HotelService $hotelService;
    private AIService $aiService;

    public function __construct(HotelService $hotelService, AIService $aiService)
    {
        $this->hotelService = $hotelService;
        $this->aiService = $aiService;
    }

    public function getRoomHotelByID(Request $request, $hotel_id)
    {
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $nights = $request->input('nights');

        return $this->hotelService->getRoomHotelByID((int) $hotel_id, $checkIn, $checkOut, (int) $nights);
    }

    public function getDetailsHotelByID($hotel_id)
    {
        return $this->hotelService->getDetailsHotelByID((int) $hotel_id);
    }

    public function getEvaluateHotelByID($hotel_id)
    {
        return $this->hotelService->getEvaluateHotelByID((int) $hotel_id);
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

    public function semanticSearch(Request $request)
    {
        $query = $request->input('query', '');
        $topK = $request->input('top_k', 10);
        
        // Extract filters from query or request
        $filters = [
            'area_id' => $request->input('area_id'),
            'max_price' => $request->input('max_price'),
            'min_rank' => $request->input('min_rank'),
        ];
        
        // Remove null filters
        $filters = array_filter($filters, fn($value) => $value !== null);
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query is required',
                'data' => null
            ], 400);
        }
        
        // Get semantic search results from AI service
        $searchResults = $this->aiService->semanticSearchHotels($query, $topK, $filters);
        
        // Process and format results using HotelService
        return $this->hotelService->processSemanticSearchResults($searchResults, $query);
    }
}
