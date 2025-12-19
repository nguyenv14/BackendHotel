<?php
namespace App\Http\Controllers;

use App\Models\ConfigWeb;
use App\Models\Brand;
use App\Models\Customers;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class ApiSloganController extends Controller{
public function getSlogans(Request $request)
    {
        $result = ConfigWeb::where('config_type', 2)->get();

        if ($result->count() > 0) {
            $host = asset('public/fontend/assets/img/config'); 

            $data = $result->map(function ($item) use ($host) {
                $item->config_image = $host . '/' . $item->config_image;
                return $item;
            });
            return ApiResponse::success($data, 'Thành công!');
        } else {
            return ApiResponse::error('Thất bại!', 404);
        }
    }
}