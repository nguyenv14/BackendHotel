<?php

namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use App\Models\Customers;
use App\Models\Evaluate;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\MenuRestaurant;
use App\Models\Order;
use App\Models\OrderDetailRestaurant;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutService
{
    public function orderRoom(array $payload): JsonResponse
    {
        try {
            return DB::transaction(function () use ($payload) {
                $typeRoom = TypeRoom::query()
                    ->with(['room.hotel'])
                    ->find($payload['type_room_id'] ?? null);

                if (!$typeRoom) {
                    return ApiResponse::error('Loại phòng không tồn tại', 404);
                }

                $room = $typeRoom->room;
                $hotel = $room?->hotel;

                if (!$room || !$hotel) {
                    return ApiResponse::error('Phòng hoặc khách sạn không tồn tại', 404);
                }

                $customer = Customers::query()->find($payload['customer_id'] ?? null);
                if (!$customer instanceof Customers) {
                    return ApiResponse::error('Khách hàng không tồn tại', 404);
                }

                $coupon = !empty($payload['coupon_id'])
                    ? Coupon::query()->find($payload['coupon_id'])
                    : null;

                $serviceCharge = ServiceCharge::query()
                    ->where('hotel_id', $hotel->hotel_id)
                    ->first();

                $pricing = $this->calculateRoomPricing(
                    $typeRoom,
                    $serviceCharge,
                    $coupon,
                    max(1, (int) ($payload['day'] ?? 1))
                );

                $orderer = $this->createOrderer($customer, $typeRoom, $payload);
                $payment = $this->createPayment();

                $orderCode = $payload['order_code'] ?? $this->generateHotelCode();
                $orderDetail = $this->createOrderDetail(
                    $orderCode,
                    $hotel,
                    $room,
                    $typeRoom,
                    $pricing
                );

                $order = $this->createOrder(
                    $payload,
                    $orderer,
                    $payment,
                    $coupon,
                    $pricing
                );

                $galleryRoom = GalleryRoom::query()
                    ->where('room_id', $room->room_id)
                    ->first();

                $this->emailOrderToCustomer($orderer, $orderDetail, $order, $pricing['final_price']);

                return ApiResponse::success(
                    $this->formatOrderData(
                        $order,
                        $orderDetail,
                        $orderer,
                        $payment,
                        $hotel,
                        $room,
                        $typeRoom,
                        $galleryRoom
                    ),
                    'Thành công!'
                );
            });
        } catch (\Throwable $throwable) {
            report($throwable);
            return ApiResponse::error('Đặt phòng thất bại', 500);
        }
    }

    public function orderRestaurant(array $payload): JsonResponse
    {
        try {
            return DB::transaction(function () use ($payload) {
                $menuList = $payload['menuList'] ?? [];

                if (empty($menuList)) {
                    return ApiResponse::error('Danh sách món ăn trống', 422);
                }

                $customer = $payload['customer'] ?? null;
                if (!$customer || empty($customer['customer_id'])) {
                    return ApiResponse::error('Thông tin khách hàng không hợp lệ', 422);
                }

                $orderCode = $this->generateHotelCode();
                $orderDetails = [];
                $totalPrice = 0;

                foreach ($menuList as $item) {
                    $menu = MenuRestaurant::query()
                        ->where('menu_item_id', $item['menu_item_id'] ?? null)
                        ->select('menu_item_price')
                        ->first();

                    if (!$menu) {
                        return ApiResponse::error('Món ăn không tồn tại', 404);
                    }

                    $quantity = (int) ($item['quantity'] ?? 1);
                    $linePrice = $menu->menu_item_price * $quantity;
                    $totalPrice += $linePrice;

                    $orderDetails[] = [
                        'order_code' => $orderCode,
                        'restaurant_id' => $payload['restaurant_id'] ?? null,
                        'restaurant_menu_id' => $item['menu_item_id'],
                        'restaurant_menu_price' => $linePrice,
                        'restaurant_menu_quantity' => $quantity,
                    ];
                }

                OrderDetailRestaurant::query()->insert($orderDetails);

                $payment = Payment::query()->create([
                    'payment_method' => 4,
                    'payment_status' => 0,
                ]);

                $orderer = Orderer::query()->create([
                    'customer_id' => $customer['customer_id'],
                    'orderer_name' => $customer['customer_name'] ?? null,
                    'orderer_phone' => $customer['customer_phone'] ?? null,
                    'orderer_email' => $customer['customer_email'] ?? null,
                    'orderer_type_bed' => $payload['person'] ?? null,
                    'orderer_own_require' => $customer['customer_note'] ?? 'Không có',
                ]);

                $order = Order::query()->create([
                    'start_day' => $payload['date'] ?? null,
                    'orderer_id' => $orderer->orderer_id,
                    'payment_id' => $payment->payment_id,
                    'order_code' => $orderCode,
                    'order_status' => 0,
                    'order_type' => 1,
                    'total_price' => $totalPrice,
                    'restaurant_id' => $payload['restaurant_id'] ?? null,
                ]);

                return ApiResponse::success(
                    $this->formatRestaurantOrderData($order),
                    'Thành công!'
                );
            });
        } catch (\Throwable $throwable) {
            report($throwable);
            return ApiResponse::error('Đặt bàn thất bại', 500);
        }
    }

    private function calculateRoomPricing(
        TypeRoom $typeRoom,
        ?ServiceCharge $serviceCharge,
        ?Coupon $coupon,
        int $day
    ): array {
        $basePrice = $typeRoom->type_room_price * max($day, 1);

        if ((int) $typeRoom->type_room_condition === 1) {
            $basePrice -= ($basePrice * $typeRoom->type_room_price_sale / 100);
        }

        $servicePrice = 0;
        if ($serviceCharge) {
            $servicePrice = (int) $serviceCharge->servicecharge_condition === 1
                ? ($basePrice * $serviceCharge->servicecharge_fee) / 100
                : $serviceCharge->servicecharge_fee;
        }

        $couponPrice = 0;
        if ($coupon) {
            $couponPrice = (int) $coupon->coupon_condition === 1
                ? ($basePrice * $coupon->coupon_price_sale) / 100
                : $coupon->coupon_price_sale;
        }

        $finalPrice = max(0, $basePrice + $servicePrice - $couponPrice);

        return [
            'base_price' => $basePrice,
            'service_price' => $servicePrice,
            'coupon_price' => $couponPrice,
            'coupon_code' => $coupon?->coupon_name_code ?? 'Không có',
            'final_price' => $finalPrice,
        ];
    }

    private function createOrderer(Customers $customer, TypeRoom $typeRoom, array $payload): Orderer
    {
        $orderer = new Orderer();
        $orderer->customer_id = $customer->customer_id;
        $orderer->orderer_name = $customer->customer_name;
        $orderer->orderer_phone = $customer->customer_phone;
        $orderer->orderer_email = $customer->customer_email;
        $orderer->orderer_type_bed = $typeRoom->type_room_bed;
        $orderer->orderer_special_requirements = $payload['order_require'] ?? null;
        $orderer->orderer_own_require = $payload['require_text'] ?? null;
        $orderer->save();

        return $orderer;
    }

    private function createPayment(): Payment
    {
        $payment = new Payment();
        $payment->payment_method = 4;
        $payment->payment_status = 0;
        $payment->save();

        return $payment;
    }

    private function createOrderDetail(
        string $orderCode,
        Hotel $hotel,
        Room $room,
        TypeRoom $typeRoom,
        array $pricing
    ): OrderDetails {
        $orderDetail = new OrderDetails();
        $orderDetail->order_code = $orderCode;
        $orderDetail->hotel_id = $hotel->hotel_id;
        $orderDetail->hotel_name = $hotel->hotel_name;
        $orderDetail->room_id = $room->room_id;
        $orderDetail->room_name = $room->room_name;
        $orderDetail->type_room_id = $typeRoom->type_room_id;
        $orderDetail->price_room = $pricing['final_price'];
        $orderDetail->hotel_fee = $pricing['service_price'];
        $orderDetail->save();

        return $orderDetail;
    }

    private function createOrder(
        array $payload,
        Orderer $orderer,
        Payment $payment,
        ?Coupon $coupon,
        array $pricing
    ): Order {
        $order = new Order();
        $order->start_day = $payload['startDay'] ?? null;
        $order->end_day = $payload['endDay'] ?? null;
        $order->orderer_id = $orderer->orderer_id;
        $order->payment_id = $payment->payment_id;
        $order->order_status = 2;
        $order->order_code = $payload['order_code'] ?? $this->generateHotelCode();
        $order->coupon_name_code = $pricing['coupon_code'];
        $order->coupon_sale_price = $pricing['coupon_price'];
        $order->order_type = 0;
        $order->total_price = $pricing['final_price'];
        $order->save();

        return $order;
    }

    private function emailOrderToCustomer(Orderer $orderer, OrderDetails $orderDetail, Order $order, float $price): void
    {
        $data = [
            'customer_name' => $orderer->orderer_name,
            'customer_email' => $orderer->orderer_email,
            'customer_phone' => $orderer->orderer_phone,
            'order_details' => $orderDetail,
            'coupon_price_sale' => $order->coupon_sale_price,
            'total_payment' => $order->total_price,
            'total_price' => $price,
        ];

        $toName = 'MyHotel - Tìm Kiếm Khách Sạn Tại Khu Vực Đà Nẵng';
        $toEmail = $orderer->orderer_email;

        if (!$toEmail) {
            return;
        }

        Mail::send('pages.mail', $data, function ($message) use ($toName, $toEmail) {
            $message->to($toEmail)
                ->subject('MyHotel - Yêu Cầu Đặt Phòng Của Bạn Đã Được Ghi Nhận Và Đang Chờ Xử Lý!')
                ->from($toEmail, $toName);
        });
    }

    private function formatOrderData(
        Order $order,
        ?OrderDetails $orderDetail = null,
        ?Orderer $orderer = null,
        ?Payment $payment = null,
        ?Hotel $hotel = null,
        ?Room $room = null,
        ?TypeRoom $typeRoom = null,
        ?GalleryRoom $galleryRoom = null
    ): array {
        $orderDetail ??= OrderDetails::query()->where('order_code', $order->order_code)->first();
        $orderer ??= Orderer::query()->find($order->orderer_id);
        $payment ??= Payment::query()->find($order->payment_id);

        if ($orderDetail) {
            $hotel ??= Hotel::query()->find($orderDetail->hotel_id);
            $room ??= Room::query()->find($orderDetail->room_id);
            $typeRoom ??= TypeRoom::query()->find($orderDetail->type_room_id);
        }

        $galleryRoom = $galleryRoom ?? ($orderDetail
            ? GalleryRoom::query()->where('room_id', $orderDetail->room_id)->first()
            : null);

        $hotelData = $hotel ? $this->formatHotelSummary($hotel) : null;
        $orderDetailData = $orderDetail ? [
            'order_details_id' => $orderDetail->order_details_id,
            'order_code' => $orderDetail->order_code,
            'hotel_id' => $orderDetail->hotel_id,
            'hotel_name' => $orderDetail->hotel_name,
            'hotel' => $hotelData,
            'room_id' => $orderDetail->room_id,
            'room_name' => $orderDetail->room_name,
            'room' => $room,
            'type_room_id' => $orderDetail->type_room_id,
            'roomType' => $typeRoom,
            'price_room' => $orderDetail->price_room,
            'hotel_fee' => $orderDetail->hotel_fee,
            'room_image' => $galleryRoom?->gallery_room_image,
            'created_at' => $orderDetail->created_at,
        ] : null;

        return [[
            'orderId' => $order->order_id,
            'startDay' => $order->start_day,
            'endDay' => $order->end_day,
            'ordererId' => $order->orderer_id,
            'paymentId' => $order->payment_id,
            'payment' => $payment,
            'orderer' => $orderer,
            'orderDetail' => $orderDetailData,
            'orderStatus' => $order->order_status,
            'orderCode' => $order->order_code,
            'couponNameCode' => $order->coupon_name_code,
            'couponSalePrice' => $order->coupon_sale_price,
            'createdAt' => $order->created_at,
            'orderType' => 0,
        ]];
    }

    private function formatRestaurantOrderData(Order $order): array
    {
        return [[
            'startDay' => $order->start_day,
            'ordererId' => $order->orderer_id,
            'restaurantId' => $order->restaurant_id,
            'orderCode' => $order->order_code,
            'paymentId' => $order->payment_id,
            'totalPrice' => $order->total_price,
            'orderStatus' => $order->order_status,
            'orderType' => $order->order_type,
        ]];
    }

    private function formatHotelSummary(Hotel $hotel): array
    {
        $evaluates = Evaluate::where('hotel_id', $hotel->hotel_id)->get();
        $service = ServiceCharge::where('hotel_id', $hotel->hotel_id)->first();

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

    private function generateHotelCode(): string
    {
        return 'MYHOTEL' . Carbon::now()->format('YmdHis');
    }
}
