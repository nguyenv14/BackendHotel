<?php

namespace App\Http\Controllers;

use App\Services\Api\SlideService;

class ApiSlideController extends Controller
{
    private SlideService $slideService;

    public function __construct(SlideService $slideService)
    {
        $this->slideService = $slideService;
    }

    public function getSlides()
    {
        return $this->slideService->getSlides();
    }
}

