<?php

namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SlideService
{
    public function getSlides(): JsonResponse
    {
        $slides = Slider::query()->get();

        if ($slides->isEmpty()) {
            return ApiResponse::error('Thất bại!', 404);
        }

        $host = asset('public/fontend/assets/img/slider');

        $data = $slides->map(function ($slide) use ($host) {
            return [
                'slider_id' => $slide->slider_id,
                'slider_name' => $slide->slider_name,
                'slider_desc' => $slide->slider_desc,
                'slider_status' => $slide->slider_status,
                'slider_image' => $host . '/' . $slide->slider_image,
                'created_at' => $slide->created_at,
                'updated_at' => $slide->updated_at,
            ];
        });

        return ApiResponse::success($data, 'Thành công!');
    }
}

