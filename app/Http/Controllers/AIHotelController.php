<?php

namespace App\Http\Controllers;

use App\Services\Api\AIService;
use Illuminate\Http\Request;

class AIHotelController extends Controller
{
  private AIService $aiService;
  public function __construct(AIService $aiService)
  {
    $this->aiService = $aiService;
  }
  public function getHotelRecommendationPopular()
  {
    return $this->aiService->getHotelRecommendationPopular();
  }

  public function getHotelRecommendationForSimilar($hotel_id)
  {
    return $this->aiService->getHotelRecommendationForSimilar($hotel_id);
  }

  public function chatbotHotel(Request $request){
    return $this->aiService->chatbotHotel($request->all());
  }
}