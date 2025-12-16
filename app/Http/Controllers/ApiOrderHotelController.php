<?php
namespace App\Http\Controllers;

use App\Services\Api\HotelOrderService;
use App\Services\Api\OrderService;
use Illuminate\Http\Request;

class ApiOrderHotelController extends Controller
{
    private HotelOrderService $hotelOrderService;
    private OrderService $orderService;

    public function __construct(HotelOrderService $hotelOrderService, OrderService $orderService)
    {
        $this->hotelOrderService = $hotelOrderService;
        $this->orderService = $orderService;
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

    /**
     * List orders with search, filters, and pagination
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listOrders(Request $request)
    {
        return $this->orderService->listOrders($request->all());
    }

    /**
     * Cancel order
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrder(Request $request)
    {
        return $this->orderService->cancelOrder($request->all());
    }

    /**
     * Get order detail
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderDetail(Request $request)
    {
        return $this->orderService->getOrderDetail($request->all());
    }

    /**
     * Update order status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrderStatus(Request $request)
    {
        return $this->orderService->updateOrderStatus($request->all());
    }

    /**
     * Get order statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderStatistics(Request $request)
    {
        return $this->orderService->getOrderStatistics($request->all());
    }
}
