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
}
