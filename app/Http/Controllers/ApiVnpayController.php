<?php
namespace App\Http\Controllers;

use App\Services\Api\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiVnpayController extends Controller
{
    private VnpayService $vnpayService;

    public function __construct(VnpayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    public function createPayment(Request $request): JsonResponse
    {
        return $this->vnpayService->createPaymentUrl($request);
    }

    public function handleReturn(Request $request): JsonResponse
    {
        return $this->vnpayService->handleReturn($request);
    }

    public function handleIpn(Request $request)
    {
        $result = $this->vnpayService->handleIpn($request);

        return response()->json($result);
    }

    public function vnpayPaymentCallback(Request $request): JsonResponse
    {
        return $this->vnpayService->vnpayPaymentCallback($request);
    }
}
