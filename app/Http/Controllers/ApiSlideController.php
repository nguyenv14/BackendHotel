<?php
namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\Brand;
use App\Models\Customers;
use Illuminate\Http\Request;

class ApiSlideController extends Controller{
public function getSlides(Request $request)
{
    $result = Slider::get();

    if ($result->count() > 0) {
        $host = asset('public/fontend/assets/img/slider'); 
        $data = $result->map(function ($item) use ($host) {
            $item->slider_image = $host . '/' . $item->slider_image;
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
            'message' => 'Thất bại!',
            'data' => null,
        ]);
    }
}

}