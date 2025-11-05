<?php
namespace App\Http\Controllers;

use App\Models\ConfigWeb;
use App\Models\Brand;
use App\Models\Customers;
use Illuminate\Http\Request;

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