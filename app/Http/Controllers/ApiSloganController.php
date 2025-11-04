<?php
namespace App\Http\Controllers;

use App\Models\ConfigWeb;
use App\Models\Brand;
use App\Models\Customers;
use Illuminate\Http\Request;

class ApiSloganController extends Controller{
    public function getSlogans(Request $request){
        $result = ConfigWeb::get();
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