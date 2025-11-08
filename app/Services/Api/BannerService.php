<?php

namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\BannerADS;
use Illuminate\Http\JsonResponse;

class BannerService
{
    public function getBannerList(): JsonResponse
    {
        $banners = BannerADS::all();

        if ($banners->isEmpty()) {
            return ApiResponse::error('Thất bại!', 404);
        }

        return ApiResponse::success($banners, 'Thành công!');
    }
}

