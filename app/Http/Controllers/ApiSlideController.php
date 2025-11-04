<?php
namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\Brand;
use App\Models\Customers;
use Illuminate\Http\Request;

class ApiSlideController extends Controller{
    public function getSlides(Request $request){
        $result = Slider::get();
        if(count($result) > 0){
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công!',
                'data' => $result,
            ]) ;
        }else{
            return response()->json([
                'status_code' => 404,
                'message' => 'Thất bại!',
                'data' => null,
            ]) ;
        }
    }
}