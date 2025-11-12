<?php
namespace App\Http\Controllers;

use App\Services\Api\VnpayService;
use Illuminate\Http\Request;

class VnpayController extends Controller
{
    public function ipn(Request $request, VnpayService $vnpayService)
    {
        $result = $vnpayService->handleIpn($request);
        return response()->json($result);
    }
}
