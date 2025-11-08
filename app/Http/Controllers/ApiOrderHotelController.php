<?php
namespace App\Http\Controllers;

use App\Services\Api\HotelOrderService;
use Illuminate\Http\Request;

class ApiOrderHotelController extends Controller
{
    private HotelOrderService $hotelOrderService;

    public function __construct(HotelOrderService $hotelOrderService)
    {
        $this->hotelOrderService = $hotelOrderService;
    }

    public function getOrderListByCustomerId(Request $request)
    {
        $customerId = (int) $request->customer_id;
        $status     = $request->has('order_status') ? (int) $request->order_status : null;

        return $this->hotelOrderService->getOrderListByCustomerId($customerId, $status);
    }

    public function cancelOrderByCustomer(Request $request)
    {
        return $this->hotelOrderService->cancelOrderByCustomer(
            (int) $request->customer_id,
            (int) $request->order_id
        );
    }

    public function evaluateCustomer(Request $request)
    {
        return $this->hotelOrderService->evaluateCustomer($request->all());
    }
}
