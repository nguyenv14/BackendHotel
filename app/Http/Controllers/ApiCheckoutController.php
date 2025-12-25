<?php
namespace App\Http\Controllers;

use App\Services\Api\CheckoutService;
use Illuminate\Http\Request;

class ApiCheckoutController extends Controller
{
    private CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function orderRoom(Request $request)
    {
        return $this->checkoutService->orderRoom($request->all());
    }

    public function orderRestaurant(Request $request)
    {
        return $this->checkoutService->orderRestaurant($request->all());
    }
    public function getTypeRoomByID($typeroom_id)
    {
        return $this->checkoutService->getTypeRoomByID($typeroom_id);
    }
    public function getMyOrders(Request $request)
    {
        return $this->checkoutService->getMyOrders(auth('api')->id());
    }
}
