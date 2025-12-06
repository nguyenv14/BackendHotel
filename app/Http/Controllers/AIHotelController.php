<?php

namespace App\Http\Controllers;

use App\Services\Api\AIService;

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
}