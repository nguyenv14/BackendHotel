<?php

namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\TypeRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * List orders with search, filters, and pagination
     * 
     * @param array $filters
     * @return JsonResponse
     */
    public function listOrders(array $filters): JsonResponse
    {
        // Pagination
        $page = isset($filters['page']) ? max(1, (int) $filters['page']) : 1;
        $perPage = isset($filters['per_page']) ? max(1, min(100, (int) $filters['per_page'])) : 15;
        
        // Search keyword
        $search = trim((string) ($filters['search'] ?? $filters['keyword'] ?? ''));
        
        // Filters
        $orderStatus = isset($filters['order_status']) ? (int) $filters['order_status'] : null;
        $paymentStatus = isset($filters['payment_status']) ? (int) $filters['payment_status'] : null;
        $paymentMethod = isset($filters['payment_method']) ? (int) $filters['payment_method'] : null;
        $hotelId = isset($filters['hotel_id']) ? (int) $filters['hotel_id'] : null;
        $customerId = isset($filters['customer_id']) ? (int) $filters['customer_id'] : null;
        $orderType = isset($filters['order_type']) ? (int) $filters['order_type'] : null;
        
        // Date range filters
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;
        
        // Build query
        $query = Order::query()
            ->with(['payment', 'orderer', 'restaurant'])
            ->select('tbl_order.*');
        
        // Search by order_code, hotel_name, customer name
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tbl_order.order_code', 'like', '%' . $search . '%')
                    ->orWhereHas('orderer', function ($subQ) use ($search) {
                        $subQ->where('orderer_name', 'like', '%' . $search . '%')
                            ->orWhere('orderer_phone', 'like', '%' . $search . '%')
                            ->orWhere('orderer_email', 'like', '%' . $search . '%');
                    });
                
                // Search in order details (hotel_name)
                $q->orWhereExists(function ($subQ) use ($search) {
                    $subQ->select(DB::raw(1))
                        ->from('tbl_order_details')
                        ->whereColumn('tbl_order_details.order_code', 'tbl_order.order_code')
                        ->where('tbl_order_details.hotel_name', 'like', '%' . $search . '%');
                });
            });
        }
        
        // Filter by order_status
        if ($orderStatus !== null) {
            if ($orderStatus === 0) {
                // Chờ xử lý
                $query->where('tbl_order.order_status', 0);
            } elseif ($orderStatus === 1) {
                // Hoàn thành
                $query->whereIn('tbl_order.order_status', [1, 2]);
            } elseif ($orderStatus === -1) {
                // Đã từ chối
                $query->where('tbl_order.order_status', -1);
            } elseif ($orderStatus === -2) {
                // Đã hủy
                $query->where('tbl_order.order_status', -2);
            } else {
                // Multiple statuses (array)
                if (is_array($orderStatus)) {
                    $query->whereIn('tbl_order.order_status', $orderStatus);
                }
            }
        }
        
        // Filter by payment_status
        if ($paymentStatus !== null) {
            $query->whereHas('payment', function ($q) use ($paymentStatus) {
                $q->where('payment_status', $paymentStatus);
            });
        }
        
        // Filter by payment_method
        if ($paymentMethod !== null) {
            $query->whereHas('payment', function ($q) use ($paymentMethod) {
                $q->where('payment_method', $paymentMethod);
            });
        }
        
        // Filter by hotel_id
        if ($hotelId !== null && $hotelId > 0) {
            $query->whereExists(function ($q) use ($hotelId) {
                $q->select(DB::raw(1))
                    ->from('tbl_order_details')
                    ->whereColumn('tbl_order_details.order_code', 'tbl_order.order_code')
                    ->where('tbl_order_details.hotel_id', $hotelId);
            });
        }
        
        // Filter by customer_id
        if ($customerId !== null && $customerId > 0) {
            $query->whereHas('orderer', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }
        
        // Filter by order_type (0: hotel, 1: restaurant)
        if ($orderType !== null) {
            $query->where('tbl_order.order_type', $orderType);
        }
        
        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('tbl_order.start_day', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('tbl_order.end_day', '<=', $dateTo);
        }
        
        // Order by
        $query->orderBy('tbl_order.order_id', 'DESC');
        
        // Paginate
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);
        
        // Format data
        $formattedData = $this->formatOrdersData($paginated->items());
        
        return ApiResponse::success([
            'data' => $formattedData,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ],
        ], 'Thành công!');
    }
    
    /**
     * Format orders data and handle null values
     * 
     * @param array $orders
     * @return array
     */
    private function formatOrdersData(array $orders): array
    {
        return collect($orders)->map(function (Order $order) {
            // Get related data
            $ordererId = $order->orderer_id ?? null;
            $paymentId = $order->payment_id ?? null;
            $orderer = $ordererId ? Orderer::query()->find($ordererId) : null;
            $payment = $paymentId ? Payment::query()->find($paymentId) : null;
            
            // Get order details
            $orderDetails = null;
            $orderType = $order->order_type ?? null;
            if ($orderType == 0) {
                // Hotel order
                $orderCode = $order->order_code ?? null;
                if ($orderCode) {
                    $orderDetails = OrderDetails::query()
                        ->where('order_code', $orderCode)
                        ->first();
                }
            }
            
            // Get hotel info if exists
            $hotel = null;
            if ($orderDetails) {
                $hotelId = $orderDetails->hotel_id ?? null;
                if ($hotelId) {
                    $hotel = Hotel::query()->find($hotelId);
                }
            }
            
            // Format order status
            $orderStatus = $order->order_status ?? 0;
            $orderStatusText = $this->getOrderStatusText($orderStatus);
            
            // Format payment info
            $paymentInfo = $this->formatPaymentInfo($payment);
            
            // Format orderer info
            $ordererInfo = $this->formatOrdererInfo($orderer);
            
            return [
                'order_id' => $order->order_id ?? 0,
                'order_code' => $order->order_code ?? '',
                'start_day' => $order->start_day ?? '',
                'end_day' => $order->end_day ?? '',
                'order_status' => $order->order_status ?? 0,
                'order_status_text' => $orderStatusText,
                'order_type' => $orderType ?? 0,
                'order_type_text' => ($orderType ?? 0) == 0 ? 'Khách sạn' : 'Nhà hàng',
                'total_price' => (float) ($order->total_price ?? 0),
                'coupon_name_code' => $order->coupon_name_code ?? 'Không có',
                'coupon_sale_price' => (float) ($order->coupon_sale_price ?? 0),
                'created_at' => $order->created_at ?? null,
                'updated_at' => $order->updated_at ?? null,
                'payment' => $paymentInfo,
                'orderer' => $ordererInfo,
                'order_details' => $this->formatOrderDetails($orderDetails, $hotel),
                'hotel' => $hotel ? [
                    'hotel_id' => $hotel->hotel_id ?? 0,
                    'hotel_name' => $hotel->hotel_name ?? '',
                    'hotel_image' => $hotel->hotel_image ?? '',
                    'hotel_rank' => $hotel->hotel_rank ?? 0,
                ] : null,
            ];
        })->toArray();
    }
    
    /**
     * Format payment info and handle null values
     * 
     * @param Payment|null $payment
     * @return array
     */
    private function formatPaymentInfo(?Payment $payment): array
    {
        if (!$payment) {
            return [
                'payment_id' => 0,
                'payment_method' => 0,
                'payment_method_text' => 'Chưa xác định',
                'payment_status' => 0,
                'payment_status_text' => 'Chưa thanh toán',
            ];
        }
        
        $methodMap = [
            1 => 'Thanh toán khi nhận phòng',
            2 => 'Chuyển khoản',
            3 => 'Thẻ tín dụng',
            4 => 'Ví điện tử (Momo)',
        ];
        
        return [
            'payment_id' => $payment->payment_id ?? 0,
            'payment_method' => $payment->payment_method ?? 0,
            'payment_method_text' => $methodMap[$payment->payment_method ?? 0] ?? 'Chưa xác định',
            'payment_status' => $payment->payment_status ?? 0,
            'payment_status_text' => ($payment->payment_status ?? 0) == 1 ? 'Đã thanh toán' : 'Chưa thanh toán',
        ];
    }
    
    /**
     * Format orderer info and handle null values
     * 
     * @param Orderer|null $orderer
     * @return array
     */
    private function formatOrdererInfo(?Orderer $orderer): array
    {
        if (!$orderer) {
            return [
                'orderer_id' => 0,
                'customer_id' => null,
                'orderer_name' => '',
                'orderer_phone' => '',
                'orderer_email' => '',
                'orderer_type_bed' => '',
                'orderer_special_requirements' => '',
                'orderer_own_require' => '',
                'orderer_bill_require' => '',
            ];
        }
        
        return [
            'orderer_id' => $orderer->orderer_id ?? 0,
            'customer_id' => $orderer->customer_id ?? null,
            'orderer_name' => $orderer->orderer_name ?? '',
            'orderer_phone' => $orderer->orderer_phone ?? '',
            'orderer_email' => $orderer->orderer_email ?? '',
            'orderer_type_bed' => $orderer->orderer_type_bed ?? '',
            'orderer_special_requirements' => $orderer->orderer_special_requirements ?? '',
            'orderer_own_require' => $orderer->orderer_own_require ?? '',
            'orderer_bill_require' => $orderer->orderer_bill_require ?? '',
        ];
    }
    
    /**
     * Format order details and handle null values
     * 
     * @param OrderDetails|null $orderDetails
     * @param Hotel|null $hotel
     * @return array|null
     */
    private function formatOrderDetails(?OrderDetails $orderDetails, ?Hotel $hotel): ?array
    {
        if (!$orderDetails) {
            return null;
        }
        
        return [
            'order_details_id' => $orderDetails->order_details_id ?? 0,
            'order_code' => $orderDetails->order_code ?? '',
            'hotel_id' => $orderDetails->hotel_id ?? 0,
            'hotel_name' => $orderDetails->hotel_name ?? ($hotel ? ($hotel->hotel_name ?? '') : ''),
            'room_id' => $orderDetails->room_id ?? 0,
            'room_name' => $orderDetails->room_name ?? '',
            'type_room_id' => $orderDetails->type_room_id ?? 0,
            'price_room' => (float) ($orderDetails->price_room ?? 0),
            'hotel_fee' => (float) ($orderDetails->hotel_fee ?? 0),
            'created_at' => $orderDetails->created_at ?? null,
        ];
    }
    
    /**
     * Cancel order
     * 
     * @param array $data
     * @return JsonResponse
     */
    public function cancelOrder(array $data): JsonResponse
    {
        $orderId = isset($data['order_id']) ? (int) $data['order_id'] : 0;
        $orderCode = isset($data['order_code']) ? trim((string) $data['order_code']) : '';
        $customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;
        
        // Validate input
        if ($orderId <= 0 && empty($orderCode)) {
            return ApiResponse::error('Vui lòng cung cấp order_id hoặc order_code', 400);
        }
        
        // Find order
        $order = null;
        if ($orderId > 0) {
            $order = Order::query()->find($orderId);
        } elseif (!empty($orderCode)) {
            $order = Order::query()->where('order_code', $orderCode)->first();
        }
        
        if (!$order) {
            return ApiResponse::error('Đơn hàng không tồn tại', 404);
        }
        
        // Validate order status - chỉ có thể cancel khi đang chờ xử lý
        $orderStatus = $order->order_status ?? 0;
        if ($orderStatus != 0) {
            $statusText = $this->getOrderStatusText($orderStatus);
            return ApiResponse::error(
                "Không thể hủy đơn hàng. Đơn hàng đang ở trạng thái: {$statusText}",
                400
            );
        }
        
        // Validate ownership - nếu có customer_id, kiểm tra order thuộc về customer
        if ($customerId !== null && $customerId > 0) {
            $ordererId = $order->orderer_id ?? null;
            if ($ordererId) {
                $orderer = Orderer::query()->find($ordererId);
                if (!$orderer || ($orderer->customer_id ?? null) != $customerId) {
                    return ApiResponse::error('Bạn không có quyền hủy đơn hàng này', 403);
                }
            }
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Hoàn lại coupon nếu có
            $couponNameCode = $order->coupon_name_code ?? null;
            if (!empty($couponNameCode) && $couponNameCode !== 'Không có') {
                $coupon = Coupon::query()
                    ->where('coupon_name_code', $couponNameCode)
                    ->first();
                
                if ($coupon) {
                    $coupon->coupon_qty_code = ($coupon->coupon_qty_code ?? 0) + 1;
                    $coupon->save();
                }
            }
            
            // Hoàn lại số lượng phòng nếu là hotel order
            $orderType = $order->order_type ?? null;
            if ($orderType == 0) {
                $orderCode = $order->order_code ?? null;
                if ($orderCode) {
                    $orderDetails = OrderDetails::query()
                        ->where('order_code', $orderCode)
                        ->first();
                    
                    if ($orderDetails) {
                        $typeRoomId = $orderDetails->type_room_id ?? null;
                        if ($typeRoomId) {
                            $typeRoom = TypeRoom::query()->find($typeRoomId);
                            if ($typeRoom) {
                                $typeRoom->type_room_quantity = ($typeRoom->type_room_quantity ?? 0) + 1;
                                $typeRoom->save();
                            }
                        }
                    }
                }
            }
            
            // Update order status
            $order->order_status = -2; // Đã hủy
            $order->save();
            
            DB::commit();
            
            // Format và return order đã cancel
            $formattedOrder = $this->formatSingleOrder($order);
            
            return ApiResponse::success(
                $formattedOrder,
                'Hủy đơn hàng thành công!'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage(),
                500
            );
        }
    }
    
    /**
     * Format single order data
     * 
     * @param Order $order
     * @return array
     */
    private function formatSingleOrder(Order $order): array
    {
        // Get related data
        $ordererId = $order->orderer_id ?? null;
        $paymentId = $order->payment_id ?? null;
        $orderer = $ordererId ? Orderer::query()->find($ordererId) : null;
        $payment = $paymentId ? Payment::query()->find($paymentId) : null;
        
        // Get order details
        $orderDetails = null;
        $orderType = $order->order_type ?? null;
        if ($orderType == 0) {
            $orderCode = $order->order_code ?? null;
            if ($orderCode) {
                $orderDetails = OrderDetails::query()
                    ->where('order_code', $orderCode)
                    ->first();
            }
        }
        
        // Get hotel info if exists
        $hotel = null;
        if ($orderDetails) {
            $hotelId = $orderDetails->hotel_id ?? null;
            if ($hotelId) {
                $hotel = Hotel::query()->find($hotelId);
            }
        }
        
        // Format order status
        $orderStatus = $order->order_status ?? 0;
        $orderStatusText = $this->getOrderStatusText($orderStatus);
        
        // Format payment info
        $paymentInfo = $this->formatPaymentInfo($payment);
        
        // Format orderer info
        $ordererInfo = $this->formatOrdererInfo($orderer);
        
        return [
            'order_id' => $order->order_id ?? 0,
            'order_code' => $order->order_code ?? '',
            'start_day' => $order->start_day ?? '',
            'end_day' => $order->end_day ?? '',
            'order_status' => $order->order_status ?? 0,
            'order_status_text' => $orderStatusText,
            'order_type' => $orderType ?? 0,
            'order_type_text' => ($orderType ?? 0) == 0 ? 'Khách sạn' : 'Nhà hàng',
            'total_price' => (float) ($order->total_price ?? 0),
            'coupon_name_code' => $order->coupon_name_code ?? 'Không có',
            'coupon_sale_price' => (float) ($order->coupon_sale_price ?? 0),
            'created_at' => $order->created_at ?? null,
            'updated_at' => $order->updated_at ?? null,
            'payment' => $paymentInfo,
            'orderer' => $ordererInfo,
            'order_details' => $this->formatOrderDetails($orderDetails, $hotel),
            'hotel' => $hotel ? [
                'hotel_id' => $hotel->hotel_id ?? 0,
                'hotel_name' => $hotel->hotel_name ?? '',
                'hotel_image' => $hotel->hotel_image ?? '',
                'hotel_rank' => $hotel->hotel_rank ?? 0,
            ] : null,
        ];
    }
    
    /**
     * Get order detail by ID or code
     * 
     * @param array $data
     * @return JsonResponse
     */
    public function getOrderDetail(array $data): JsonResponse
    {
        $orderId = isset($data['order_id']) ? (int) $data['order_id'] : 0;
        $orderCode = isset($data['order_code']) ? trim((string) $data['order_code']) : '';
        $customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;
        
        // Validate input
        if ($orderId <= 0 && empty($orderCode)) {
            return ApiResponse::error('Vui lòng cung cấp order_id hoặc order_code', 400);
        }
        
        // Find order
        $order = null;
        if ($orderId > 0) {
            $order = Order::query()
                ->with(['payment', 'orderer', 'restaurant'])
                ->find($orderId);
        } elseif (!empty($orderCode)) {
            $order = Order::query()
                ->with(['payment', 'orderer', 'restaurant'])
                ->where('order_code', $orderCode)
                ->first();
        }
        
        if (!$order) {
            return ApiResponse::error('Đơn hàng không tồn tại', 404);
        }
        
        // Validate ownership - nếu có customer_id, kiểm tra order thuộc về customer
        if ($customerId !== null && $customerId > 0) {
            $ordererId = $order->orderer_id ?? null;
            if ($ordererId) {
                $orderer = Orderer::query()->find($ordererId);
                if (!$orderer || ($orderer->customer_id ?? null) != $customerId) {
                    return ApiResponse::error('Bạn không có quyền xem đơn hàng này', 403);
                }
            }
        }
        
        // Format order data
        $formattedOrder = $this->formatSingleOrder($order);
        
        return ApiResponse::success($formattedOrder, 'Thành công!');
    }
    
    /**
     * Update order status (for admin/hotel manager)
     * 
     * @param array $data
     * @return JsonResponse
     */
    public function updateOrderStatus(array $data): JsonResponse
    {
        $orderId = isset($data['order_id']) ? (int) $data['order_id'] : 0;
        $orderCode = isset($data['order_code']) ? trim((string) $data['order_code']) : '';
        $newStatus = isset($data['order_status']) ? (int) $data['order_status'] : null;
        
        // Validate input
        if ($orderId <= 0 && empty($orderCode)) {
            return ApiResponse::error('Vui lòng cung cấp order_id hoặc order_code', 400);
        }
        
        if ($newStatus === null) {
            return ApiResponse::error('Vui lòng cung cấp order_status', 400);
        }
        
        // Validate status value
        $validStatuses = [-2, -1, 0, 1, 2];
        if (!in_array($newStatus, $validStatuses)) {
            return ApiResponse::error('Trạng thái đơn hàng không hợp lệ', 400);
        }
        
        // Find order
        $order = null;
        if ($orderId > 0) {
            $order = Order::query()->find($orderId);
        } elseif (!empty($orderCode)) {
            $order = Order::query()->where('order_code', $orderCode)->first();
        }
        
        if (!$order) {
            return ApiResponse::error('Đơn hàng không tồn tại', 404);
        }
        
        // Chỉ cho phép update hotel orders
        $orderType = $order->order_type ?? null;
        if ($orderType != 0) {
            return ApiResponse::error('Chỉ có thể cập nhật đơn hàng khách sạn', 400);
        }
        
        // Update status
        $oldStatus = $order->order_status ?? 0;
        $order->order_status = $newStatus;
        $order->save();
        
        // Format and return updated order
        $formattedOrder = $this->formatSingleOrder($order);
        
        return ApiResponse::success([
            'order' => $formattedOrder,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_status_text' => $this->getOrderStatusText($oldStatus),
            'new_status_text' => $this->getOrderStatusText($newStatus),
        ], 'Cập nhật trạng thái đơn hàng thành công!');
    }
    
    /**
     * Get order statistics
     * 
     * @param array $filters
     * @return JsonResponse
     */
    public function getOrderStatistics(array $filters): JsonResponse
    {
        $hotelId = isset($filters['hotel_id']) ? (int) $filters['hotel_id'] : null;
        $customerId = isset($filters['customer_id']) ? (int) $filters['customer_id'] : null;
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;
        
        $query = Order::query()
            ->where('order_type', 0); // Chỉ hotel orders
        
        // Filter by hotel_id
        if ($hotelId !== null && $hotelId > 0) {
            $query->whereExists(function ($q) use ($hotelId) {
                $q->select(DB::raw(1))
                    ->from('tbl_order_details')
                    ->whereColumn('tbl_order_details.order_code', 'tbl_order.order_code')
                    ->where('tbl_order_details.hotel_id', $hotelId);
            });
        }
        
        // Filter by customer_id
        if ($customerId !== null && $customerId > 0) {
            $query->whereHas('orderer', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }
        
        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('tbl_order.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('tbl_order.created_at', '<=', $dateTo);
        }
        
        // Get statistics
        $totalOrders = (clone $query)->count();
        $pendingOrders = (clone $query)->where('order_status', 0)->count();
        $completedOrders = (clone $query)->whereIn('order_status', [1, 2])->count();
        $cancelledOrders = (clone $query)->where('order_status', -2)->count();
        $rejectedOrders = (clone $query)->where('order_status', -1)->count();
        
        // Calculate total revenue (completed orders only)
        $totalRevenue = (clone $query)
            ->whereIn('order_status', [1, 2])
            ->sum('total_price');
        
        // Get orders by status
        $ordersByStatus = [
            'pending' => $pendingOrders,
            'completed' => $completedOrders,
            'cancelled' => $cancelledOrders,
            'rejected' => $rejectedOrders,
        ];
        
        return ApiResponse::success([
            'total_orders' => $totalOrders,
            'orders_by_status' => $ordersByStatus,
            'total_revenue' => (float) $totalRevenue,
            'average_order_value' => $completedOrders > 0 ? (float) ($totalRevenue / $completedOrders) : 0,
        ], 'Thành công!');
    }
    
    /**
     * Get order status text
     * 
     * @param int $status
     * @return string
     */
    private function getOrderStatusText(?int $status): string
    {
        if ($status === null) {
            return 'Không xác định';
        }
        
        return match ($status) {
            0 => 'Chờ xử lý',
            1 => 'Hoàn thành',
            2 => 'Đã hoàn thành và đánh giá',
            -1 => 'Đã từ chối',
            -2 => 'Đã hủy',
            default => 'Không xác định',
        };
    }
}

