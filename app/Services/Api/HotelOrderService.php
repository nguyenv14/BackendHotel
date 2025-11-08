<?php

namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Customers;
use App\Models\Evaluate;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class HotelOrderService
{
    public function getOrderListByCustomerId(int $customerId, ?int $status = null): JsonResponse
    {
        $ordererIds = $this->getOrdererIdsByCustomer($customerId);
        if (empty($ordererIds)) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        $orders = Order::query()
            ->whereIn('orderer_id', $ordererIds)
            ->where('order_type', 0)
            ->when($status === 0, fn ($query) => $query->where('order_status', 0))
            ->when($status === 1, fn ($query) => $query->whereIn('order_status', [1, 2]))
            ->when(!in_array($status, [0, 1], true), fn ($query) => $query->whereIn('order_status', [-1, -2]))
            ->orderByDesc('order_id')
            ->get();

        if ($orders->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        return ApiResponse::success(
            $this->convertOrdersToArray($orders),
            'Thành công!'
        );
    }

    public function cancelOrderByCustomer(int $customerId, int $orderId): JsonResponse
    {
        $order = Order::query()->where('order_id', $orderId)->first();
        if (!$order || $order->order_type !== 0) {
            return ApiResponse::error('Đơn hàng không tồn tại', 404);
        }

        $order->order_status = -2;
        $order->save();

        $ordererIds = $this->getOrdererIdsByCustomer($customerId);
        if (empty($ordererIds)) {
            return ApiResponse::success([], 'Thành công!');
        }

        $orders = Order::query()
            ->whereIn('orderer_id', $ordererIds)
            ->where('order_type', 0)
            ->where('order_status', 0)
            ->orderByDesc('order_id')
            ->get();

        if ($orders->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        return ApiResponse::success(
            $this->convertOrdersToArray($orders),
            'Thành công!'
        );
    }

    public function evaluateCustomer(array $payload): JsonResponse
    {
        $customer = Customers::query()->find($payload['customer_id'] ?? null);
        if (!$customer instanceof Customers) {
            return ApiResponse::error('Khách hàng không tồn tại', 404);
        }

        $order = Order::query()->find($payload['order_id'] ?? null);
        if (!$order || $order->order_type !== 0) {
            return ApiResponse::error('Đơn hàng không tồn tại', 404);
        }

        $this->createEvaluation($customer, $payload);

        $order->order_status = 2;
        $order->save();

        $ordererIds = $this->getOrdererIdsByCustomer($customer->customer_id);
        if (empty($ordererIds)) {
            return ApiResponse::success([], 'Thành công!');
        }

        $orders = Order::query()
            ->whereIn('orderer_id', $ordererIds)
            ->where('order_type', 0)
            ->whereIn('order_status', [1, 2])
            ->orderByDesc('order_id')
            ->get();

        if ($orders->isEmpty()) {
            return ApiResponse::error('Không truy xuất được dữ liệu', 404);
        }

        return ApiResponse::success(
            $this->convertOrdersToArray($orders),
            'Thành công!'
        );
    }

    private function getOrdererIdsByCustomer(int $customerId): array
    {
        return Orderer::query()
            ->where('customer_id', $customerId)
            ->pluck('orderer_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function convertOrdersToArray(Collection $orders): array
    {
        return $orders->map(function (Order $order) {
            $orderer = Orderer::query()->find($order->orderer_id);
            $payment = Payment::query()->find($order->payment_id);
            $orderDetail = OrderDetails::query()
                ->where('order_code', $order->order_code)
                ->first();

            if (!$orderDetail) {
                return null;
            }

            $hotel = Hotel::query()->find($orderDetail->hotel_id);
            $room = Room::query()->find($orderDetail->room_id);
            $typeRoom = TypeRoom::query()->find($orderDetail->type_room_id);
            $galleryRoom = GalleryRoom::query()->where('room_id', $orderDetail->room_id)->first();

            $dataOrder = [
                'order_details_id' => $orderDetail->order_details_id,
                'order_code' => $orderDetail->order_code,
                'hotel_id' => $orderDetail->hotel_id,
                'hotel_name' => $orderDetail->hotel_name,
                'hotel' => $hotel ? $this->convertHotelToArray($hotel) : null,
                'room_id' => $orderDetail->room_id,
                'room_name' => $orderDetail->room_name,
                'room' => $room,
                'type_room_id' => $orderDetail->type_room_id,
                'roomType' => $typeRoom,
                'price_room' => $orderDetail->price_room,
                'hotel_fee' => $orderDetail->hotel_fee,
                'room_image' => $galleryRoom?->gallery_room_image,
                'created_at' => $orderDetail->created_at,
            ];

            return [
                'orderId' => $order->order_id,
                'startDay' => $order->start_day,
                'endDay' => $order->end_day,
                'ordererId' => $order->orderer_id,
                'paymentId' => $order->payment_id,
                'payment' => $payment,
                'orderer' => $orderer,
                'order_type' => 0,
                'orderDetail' => $dataOrder,
                'orderStatus' => $order->order_status,
                'orderCode' => $order->order_code,
                'couponNameCode' => $order->coupon_name_code,
                'couponSalePrice' => $order->coupon_sale_price,
                'total_price' => $order->total_price,
                'createdAt' => $order->created_at,
            ];
        })
            ->filter()
            ->values()
            ->all();
    }

    private function convertHotelToArray(Hotel $hotel): array
    {
        $evaluates = Evaluate::query()->where('hotel_id', $hotel->hotel_id)->get();
        $service = ServiceCharge::query()->where('hotel_id', $hotel->hotel_id)->first();

        return [
            'hotel_id' => $hotel->hotel_id,
            'hotel_name' => $hotel->hotel_name,
            'hotel_rank' => $hotel->hotel_rank,
            'hotel_type' => $hotel->hotel_type,
            'brand_id' => $hotel->brand_id,
            'evaluates' => $evaluates,
            'service_change' => $service,
            'brand' => $hotel->brand,
            'area' => $hotel->area,
            'hotel_placedetails' => $hotel->hotel_placedetails,
            'hotel_linkplace' => $hotel->hotel_linkplace,
            'hotel_jfameplace' => $hotel->hotel_jfameplace,
            'hotel_image' => $hotel->hotel_image,
            'hotel_desc' => $hotel->hotel_desc,
            'hotel_tag_keyword' => $hotel->hotel_tag_keyword,
            'hotel_view' => $hotel->hotel_view,
            'hotel_status' => $hotel->hotel_status,
            'created_at' => $hotel->created_at,
            'updated_at' => $hotel->updated_at,
        ];
    }

    private function createEvaluation(Customers $customer, array $payload): void
    {
        $evaluation = new Evaluate();
        $evaluation->customer_id = $customer->customer_id;
        $evaluation->customer_name = $customer->customer_name;
        $evaluation->hotel_id = $payload['hotel_id'] ?? null;
        $evaluation->room_id = $payload['room_id'] ?? null;
        $evaluation->type_room_id = $payload['type_room_id'] ?? null;
        $evaluation->evaluate_title = $payload['evaluate_content'] ?? null;
        $evaluation->evaluate_content = $payload['evaluate_content'] ?? null;
        $evaluation->evaluate_loaction_point = $payload['evaluate_loaction_point'] ?? null;
        $evaluation->evaluate_service_point = $payload['evaluate_service_point'] ?? null;
        $evaluation->evaluate_price_point = $payload['evaluate_price_point'] ?? null;
        $evaluation->evaluate_sanitary_point = $payload['evaluate_sanitary_point'] ?? null;
        $evaluation->evaluate_convenient_point = $payload['evaluate_convenient_point'] ?? null;
        $evaluation->save();
    }
}

