<?php
namespace App\Http\Controllers;

use App\Services\Api\HotelOrderService;
use App\Services\Api\OrderService;
use App\Models\Order;
use App\Http\Enums\OrderStatus;
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

    /**
     * Check-in đơn hàng bằng mã check-in
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkinOrder(Request $request)
    {
        $checkin_code = $request->checkin_code;
        
        if (!$checkin_code) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã check-in'
            ], 400);
        }

        $order = Order::where('checkin_code', $checkin_code)->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Mã check-in không hợp lệ'
            ], 404);
        }

        // Kiểm tra trạng thái đơn hàng - chỉ có thể check-in khi status = 0 (đã duyệt, chưa check-in)
        if ($order->order_status != OrderStatus::WAITING_FOR_APPROVAL) {
            $statusMessage = '';
            if ($order->order_status == OrderStatus::CANCELLED_BY_ADMIN) {
                $statusMessage = 'Đơn hàng đã bị hủy bởi admin';
            } elseif ($order->order_status == OrderStatus::CANCELLED_BY_CUSTOMER) {
                $statusMessage = 'Đơn hàng đã bị hủy';
            } elseif ($order->order_status == OrderStatus::CHECK_IN) {
                $statusMessage = 'Đơn hàng đã được check-in rồi';
            } elseif ($order->order_status == OrderStatus::CHECK_OUT) {
                $statusMessage = 'Đơn hàng đã được check-out';
            } elseif ($order->order_status == OrderStatus::COMPLETED) {
                $statusMessage = 'Đơn hàng đã hoàn thành';
            } elseif ($order->order_status == OrderStatus::NO_SHOW) {
                $statusMessage = 'Đơn hàng đã bị hủy do no show';
            }
            
            return response()->json([
                'success' => false,
                'message' => $statusMessage
            ], 400);
        }

        // Kiểm tra xem đơn hàng đã có mã check-in chưa (phải được duyệt trước)
        if (!$order->checkin_code) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa được duyệt hoặc chưa có mã check-in'
            ], 400);
        }

        // Check-in thành công - cập nhật trạng thái từ 0 sang 1
        $order->order_status = OrderStatus::CHECK_IN;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công',
            'data' => [
                'order_code' => $order->order_code,
                'checkin_code' => $order->checkin_code,
                'order_status' => $order->order_status
            ]
        ]);
    }

    /**
     * Check-out đơn hàng
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkoutOrder(Request $request)
    {
        $order_code = $request->order_code;
        
        if (!$order_code) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã đơn hàng'
            ], 400);
        }

        $order = Order::where('order_code', $order_code)->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Mã đơn hàng không hợp lệ'
            ], 404);
        }

        // Kiểm tra trạng thái đơn hàng - chỉ có thể checkout khi đã check-in (status = 1)
        if ($order->order_status != OrderStatus::CHECK_IN) {
            $statusMessage = '';
            if ($order->order_status == OrderStatus::WAITING_FOR_APPROVAL) {
                $statusMessage = 'Đơn hàng chưa được check-in';
            } elseif ($order->order_status == OrderStatus::CANCELLED_BY_ADMIN) {
                $statusMessage = 'Đơn hàng đã bị hủy bởi admin';
            } elseif ($order->order_status == OrderStatus::CANCELLED_BY_CUSTOMER) {
                $statusMessage = 'Đơn hàng đã bị hủy';
            } elseif ($order->order_status == OrderStatus::CHECK_OUT) {
                $statusMessage = 'Đơn hàng đã được check-out rồi';
            } elseif ($order->order_status == OrderStatus::COMPLETED) {
                $statusMessage = 'Đơn hàng đã hoàn thành';
            } elseif ($order->order_status == OrderStatus::NO_SHOW) {
                $statusMessage = 'Đơn hàng đã bị hủy do no show';
            }
            
            return response()->json([
                'success' => false,
                'message' => $statusMessage
            ], 400);
        }

        // Check-out thành công - cập nhật trạng thái từ 1 sang 2
        $order->order_status = OrderStatus::CHECK_OUT;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Check-out thành công',
            'data' => [
                'order_code' => $order->order_code,
                'order_status' => $order->order_status
            ]
        ]);
    }
}
