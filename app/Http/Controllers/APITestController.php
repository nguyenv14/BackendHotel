<?php
namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Brand;
use Google\Service\Docs\Request;

class APITestController extends Controller{
    public function getAPi(Request $request){
        $brands = Area::get();
        
        return response()->json([
            'status_code' => 200,
            'message' => 'ok',
            'data' => $brands,
        ]);
    }
}

