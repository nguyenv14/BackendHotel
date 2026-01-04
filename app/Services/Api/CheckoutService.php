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
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    public function getMyOrders($customerId)
    {
        try {

            if (!$customerId) {
                return ApiResponse::error('Customer không hợp lệ', 400);
            }

            $list_id_orderer = Orderer::where('customer_id', $customerId)
                ->pluck('orderer_id')
                ->toArray();

            if (empty($list_id_orderer)) {
                return ApiResponse::error('Không có đơn hàng', 404);
            }

            $orders = Order::with([
                    'orderer',
                    'payment',
                    'orderDetails.hotel',
                    'orderDetails.room.galleryroom',
                    'orderDetails.room.typeroom'
                ])
                ->whereIn('orderer_id', $list_id_orderer)
                ->orderByDesc('order_id')
                ->take(5)
                ->get();

            return ApiResponse::success($orders, 'Thành công!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return ApiResponse::error('Không tìm thấy dữ liệu', 404);

        } catch (\Illuminate\Database\QueryException $e) {

            Log::error('GetMyOrders SQL error', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error('Lỗi truy vấn dữ liệu', 500);

        } catch (\Throwable $e) {

            Log::error('GetMyOrders error', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'Lỗi hệ thống',
                500
            );
        }
    }
    public function orderRoom(array $payload): JsonResponse
    {
         Log::info('API orderRoom payload:', $payload);
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

                $availableQuantity = $this->getAvailableQuantity(
                    $hotel->hotel_id,
                    $typeRoom->type_room_id,
                    $payload['startDay'] ?? '',
                    $payload['endDay'] ?? ''
                );



                if ($availableQuantity <= 0) {
                    return ApiResponse::error('Phòng đã hết', 400);
                }

                if ($this->hasCustomerBookedRoom($customer->customer_id, $hotel->hotel_id, $typeRoom->type_room_id, $payload['startDay'], $payload['endDay'])) {
                    return ApiResponse::error('Bạn đã đặt phòng trong thời gian này', 400);
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
                
                // Tạo payment dựa trên payment_method
                $paymentMethod = $payload['payment_method'] ?? 'bank-transfer';
                if (in_array($paymentMethod, ['blockchain', 'metamask'])) {
                    $payment = $this->createBlockchainPayment($payload);
                } else {
                    $payment = $this->createPayment($payload);
                }

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

                // Tạo hash và ghi lên blockchain để xác thực đơn hàng
                $this->storeOrderHashOnBlockchain($order, $orderer, $pricing['final_price']);

                // Refresh order để lấy invoice_hash và blockchain_tx_hash mới nhất từ database
                $order->refresh();

                // Luôn gửi mail khi tạo đơn (thông báo đơn đang chờ admin xác nhận, KHÔNG có checkin_code)
                // Sau khi admin duyệt sẽ gửi mail tiếp với checkin_code
                $this->emailOrderToCustomer($orderer, $orderDetail, $order, $pricing['final_price'], false);

                $orderData = $this->formatOrderData(
                    $order,
                    $orderDetail,
                    $orderer,
                    $payment,
                    $hotel,
                    $room,
                    $typeRoom,
                    $galleryRoom
                );

                // Luôn thêm QR URL để verify đơn hàng (dựa trên order_code)
                $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
                $orderData[0]['qr_url'] = $frontendUrl . '/verify/' . $order->order_code;
                $orderData[0]['qrUrl'] = $frontendUrl . '/verify/' . $order->order_code; // camelCase để tương thích với frontend
                
                // Thêm blockchain info nếu có
                if (!empty($order->invoice_hash)) {
                    $orderData[0]['blockchain_tx_hash'] = $order->blockchain_tx_hash;
                    $orderData[0]['invoice_hash'] = $order->invoice_hash;
                }

                return ApiResponse::success(
                    $orderData,
                    'Thành công!'
                );
            });
        } catch (\Throwable $throwable) {
            report($throwable);
            return ApiResponse::error('Lỗi bên trong server !', 500);
        }
    }

    public function hasCustomerBookedRoom(
        int $customerId,
        int $hotelId,
        int $typeRoomId,
        string $checkIn,
        string $checkOut
    ): bool {
        // Chuẩn hoá ngày từ payload (theo format Y-m-d)
        try {
            $checkInDate  = Carbon::createFromFormat('Y-m-d', $checkIn)->format('Y-m-d');
            $checkOutDate = Carbon::createFromFormat('Y-m-d', $checkOut)->format('Y-m-d');
        } catch (\Throwable $e) {
            return false; // Format sai trả về false
        }

        // Kiểm tra trong OrderDetails + tbl_order + tbl_orderer
        $existingBooking = OrderDetails::query()
            ->join('tbl_order', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
            ->join('tbl_orderer', 'tbl_order.orderer_id', '=', 'tbl_orderer.orderer_id')
            ->where('tbl_orderer.customer_id', $customerId)
            ->where('tbl_order_details.hotel_id', $hotelId)
            ->where('tbl_order_details.type_room_id', $typeRoomId)
            ->where('start_day', '<', $checkOutDate)
            ->where('end_day',   '>', $checkInDate)
            ->whereNotIn('tbl_order.order_status', [-2, -1]) // Loại bỏ cancel/deleted
            ->exists();

        return $existingBooking;
    }

    public function getAvailableQuantity(
    int $hotel_id,
    int $type_room_id,
    string $checkIn,
    string $checkOut
    ): int {
        // Chuẩn hoá ngày
        $checkInDate  = Carbon::createFromFormat('Y-m-d', $checkIn)->format('Y-m-d');
        $checkOutDate = Carbon::createFromFormat('Y-m-d', $checkOut)->format('Y-m-d');


        // Lấy loại phòng
        $typeRoom = TypeRoom::query()
            ->where('type_room_id', $type_room_id)
            ->first();

        if (!$typeRoom) {
            return 0;
        }

        // Đếm số phòng đã đặt (trùng ngày)
        $bookedCount = OrderDetails::query()
            ->join('tbl_order', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
            ->where('tbl_order_details.hotel_id', $hotel_id)
            ->where('tbl_order_details.type_room_id', $type_room_id)
            ->where('start_day', '<', $checkOutDate)
            ->where('end_day',   '>', $checkInDate)
            ->whereNotIn('tbl_order.order_status', [-2, -1])
            ->count();

        // Trả về số phòng còn trống
        return max(
            $typeRoom->type_room_quantity - $bookedCount,
            0
        );
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
        // if ($serviceCharge) {
        //     $servicePrice = (int) $serviceCharge->servicecharge_condition === 1
        //         ? ($basePrice * $serviceCharge->servicecharge_fee) / 100
        //         : $serviceCharge->servicecharge_fee;
        // }

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

    /**
     * Convert special requirements string to integer code
     * 0: Không
     * 1: Phòng không hút thuốc
     * 2: Phòng ở tầng cao
     * 3: Phòng không hút thuốc và trên cao
     */
    private function convertSpecialRequirements($specialRequirements): int
    {
        if (empty($specialRequirements)) {
            return 0;
        }

        // Nếu đã là số, trả về luôn
        if (is_numeric($specialRequirements)) {
            return (int) $specialRequirements;
        }

        // Convert string thành lowercase để so sánh
        $requirements = strtolower(trim($specialRequirements));
        
        // Kiểm tra nếu có cả "non-smoking" và "high-floor"
        if (strpos($requirements, 'non-smoking') !== false && strpos($requirements, 'high-floor') !== false) {
            return 3; // Phòng không hút thuốc và trên cao
        }
        
        // Kiểm tra từng yêu cầu riêng lẻ
        if (strpos($requirements, 'non-smoking') !== false) {
            return 1; // Phòng không hút thuốc
        }
        
        if (strpos($requirements, 'high-floor') !== false) {
            return 2; // Phòng ở tầng cao
        }

        // Mặc định là 0 (Không)
        return 0;
    }

    private function createOrderer(Customers $customer, TypeRoom $typeRoom, array $payload): Orderer
    {
        $orderer = new Orderer();
        $orderer->customer_id = $customer->customer_id;
        $orderer->orderer_name = $customer->customer_name;
        $orderer->orderer_phone = $customer->customer_phone;
        $orderer->orderer_email = $customer->customer_email;
        $orderer->orderer_type_bed = $typeRoom->type_room_bed;
        $orderer->orderer_special_requirements = $this->convertSpecialRequirements($payload['order_require'] ?? null);
        $orderer->orderer_own_require = $payload['require_text'] ?? null;
        $orderer->save();

        return $orderer;
    }

    private function createPayment(array $payload = []): Payment
    {
        $payment = new Payment();
        
        // Map payment_method từ string sang số
        // 4: thanh toán khi nhận phòng (direct)
        // 3: thanh toán bằng blockchain (blockchain/metamask) - xử lý ở createBlockchainPayment()
        // 2: thanh toán bằng vnpay (vnpay/qr-pay)
        $paymentMethod = $payload['payment_method'] ?? 'direct';
        $paymentMethodMap = [
            'direct' => 4,           // Thanh toán khi nhận phòng
            'bank-transfer' => 1,    // Chuyển khoản ngân hàng (giữ nguyên cho tương thích)
            'qr-pay' => 2,           // VNPay QR
            'vnpay' => 2,            // VNPay
        ];
        
        $payment->payment_method = $paymentMethodMap[$paymentMethod] ?? 4; // Default: Thanh toán khi nhận phòng
        $payment->payment_status = 0;
        $payment->save();

        return $payment;
    }

    /**
     * Tạo payment cho blockchain/MetaMask
     * Lưu transaction_hash và payment_amount_eth
     */
    private function createBlockchainPayment(array $payload): Payment
    {
        $payment = new Payment();
        
        // Blockchain/MetaMask dùng payment_method = 3
        $payment->payment_method = 3;
        
        // Lưu transaction hash nếu có và cột tồn tại
        if (isset($payload['transaction_hash']) && !empty($payload['transaction_hash'])) {
            try {
                $payment->transaction_hash = $payload['transaction_hash'];
            } catch (\Exception $e) {
                // Nếu cột chưa tồn tại, log lỗi nhưng vẫn tiếp tục
                Log::warning('Column transaction_hash not found: ' . $e->getMessage());
            }
        }
        
        // Lưu số lượng ETH đã thanh toán nếu có và cột tồn tại
        if (isset($payload['payment_amount_eth']) && !empty($payload['payment_amount_eth'])) {
            try {
                $payment->payment_amount_eth = $payload['payment_amount_eth'];
            } catch (\Exception $e) {
                // Nếu cột chưa tồn tại, log lỗi nhưng vẫn tiếp tục
                Log::warning('Column payment_amount_eth not found: ' . $e->getMessage());
            }
        }
        
        $payment->payment_status = 0; // 0 = chưa thanh toán, 1 = đã thanh toán
        
        try {
            $payment->save();
        } catch (\Illuminate\Database\QueryException $e) {
            // Nếu lỗi do thiếu cột, thử save lại không có các trường blockchain
            if (str_contains($e->getMessage(), 'transaction_hash') || str_contains($e->getMessage(), 'payment_amount_eth')) {
                Log::error('Database columns missing. Please run migration: ' . $e->getMessage());
                // Unset các trường blockchain và thử lại
                unset($payment->transaction_hash);
                unset($payment->payment_amount_eth);
                $payment->save();
            } else {
                throw $e;
            }
        }

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
        // Lưu base_price vào price_room (giá phòng gốc, chưa có service và coupon)
        $orderDetail->price_room = $pricing['base_price'];
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
        
        // Tạo order_code trước
        $orderCode = $payload['order_code'] ?? $this->generateHotelCode();
        $order->order_code = $orderCode;
        
        // Tạo mã check-in ngay khi tạo đơn (cho cả 2 trường hợp)
        $order->checkin_code = $this->generateCheckinCode($orderCode);
        
        // Set order_status dựa trên payment_method
        // payment_method = 4 (Khi Nhận Phòng) → order_status = 0 (Chờ duyệt) - KHÔNG gửi mail, chờ admin duyệt
        // payment_method = 2 (VNPAY/blockchain) → order_status = 1 (Đã thanh toán, có thể check-in) - GỬI MAIL ngay
        if ($payment->payment_method == 2 || $payment->payment_method == 3) {
            $order->order_status = 1; // CHECK_IN
        } else {
            $order->order_status = 0; // WAITING_FOR_APPROVAL
        }
        $order->coupon_name_code = $pricing['coupon_code'];
        $order->coupon_sale_price = $pricing['coupon_price'];
        $order->order_type = 0;
        $order->total_price = $pricing['final_price'];
        $order->save();

        return $order;
    }

    private function emailOrderToCustomer(Orderer $orderer, OrderDetails $orderDetail, Order $order, float $price, bool $isApproved = false): void
    {
        $toEmail = $orderer->orderer_email;
        if (!$toEmail) {
            return;
        }

        // Lấy thông tin type room
        $typeRoom = null;
        if ($orderDetail->type_room_id) {
            $typeRoom = TypeRoom::where('type_room_id', $orderDetail->type_room_id)->first();
        }

        // Chuẩn bị order_details dạng array để tương thích với template mail
        // price_room trong OrderDetails đã là final_price (base + service - coupon)
        // price_room đã là base_price (giá phòng gốc)
        // hotel_fee là service_price (phí dịch vụ)
        // total_price trong order đã là tổng cuối cùng (base_price + service_price - coupon_price)
        
        $orderDetailsArray = [
            'order_code' => $order->order_code,
            'hotel_name' => $orderDetail->hotel_name,
            'room_name' => $orderDetail->room_name,
            'type_room_bed' => $typeRoom ? ($typeRoom->type_room_bed ?? 'N/A') : 'N/A',
            'type_room_price' => $typeRoom ? ($typeRoom->type_room_price ?? 0) : 0,
            'type_room_condition' => $typeRoom ? ($typeRoom->type_room_condition ?? 'N/A') : 'N/A',
            'base_price' => $orderDetail->price_room, // Giá phòng gốc (base_price)
            'price_room' => $orderDetail->price_room, // Giá phòng gốc (để tương thích)
            'hotel_fee' => $orderDetail->hotel_fee, // Phí dịch vụ
            'coupon_name_code' => $order->coupon_name_code ?? 'Không Có',
            'coupon_price_sale' => $order->coupon_sale_price ?? 0,
        ];

        // Nếu đã duyệt thì gửi checkin_code, nếu chưa duyệt thì không gửi
        $checkinCode = $isApproved ? $order->checkin_code : null;
        $orderStatus = $isApproved ? 0 : $order->order_status; // 0 = đã duyệt, chờ check-in

        $data = [
            'customer_name' => $orderer->orderer_name,
            'customer_email' => $orderer->orderer_email,
            'customer_phone' => $orderer->orderer_phone,
            'order_details' => $orderDetailsArray,
            'coupon_price_sale' => $order->coupon_sale_price ?? 0,
            'total_price' => $order->total_price,
            'checkin_code' => $checkinCode,
            'order_status' => $orderStatus,
        ];

        $toName = 'MyHotel - Tìm Kiếm Khách Sạn Tại Khu Vực Đà Nẵng';
        
        // Subject khác nhau tùy vào trạng thái
        if ($isApproved) {
            $subject = 'MyHotel - Đơn Hàng Của Bạn Đã Được Duyệt!';
        } else {
            $subject = 'MyHotel - Yêu Cầu Đặt Phòng Của Bạn Đã Được Ghi Nhận Và Đang Chờ Xử Lý!';
        }

        Mail::send('pages.mail', $data, function ($message) use ($toName, $toEmail, $subject) {
            $message->to($toEmail)
                ->subject($subject)
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
        $evaluates = Evaluate::where('hotel_id', $hotel->hotel_id)->limit(5)->get();
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

    /**
     * Tạo mã check-in duy nhất cho đơn hàng
     * 
     * @param string $order_code
     * @return string
     */
    private function generateCheckinCode($order_code)
    {
        // Tạo mã check-in dựa trên order_code và timestamp
        // Format: CHK + 4 ký tự cuối của order_code + 6 số ngẫu nhiên
        $orderSuffix = substr($order_code, -4);
        $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $checkinCode = 'CHK' . strtoupper($orderSuffix) . $randomNumber;
        
        // Kiểm tra mã đã tồn tại chưa (rất hiếm nhưng để đảm bảo)
        while (Order::where('checkin_code', $checkinCode)->exists()) {
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $checkinCode = 'CHK' . strtoupper($orderSuffix) . $randomNumber;
        }
        
        return $checkinCode;
    }

    public function getTypeRoomByID($typeroom_id)
    {
        $typeRoom = TypeRoom::with([
            'room.hotel','room.galleryroom'
        ])->where('type_room_id', $typeroom_id)->first();

        if (!$typeRoom) {
            return ApiResponse::error('Không tìm thấy loại phòng!', 404);
        }

        return ApiResponse::success($typeRoom, 'Thành công!');
    }

    /**
     * Tạo hash và ghi lên blockchain để xác thực đơn hàng
     * 
     * @param Order $order
     * @param Orderer $orderer
     * @param float $finalPrice
     * @return void
     */
    private function storeOrderHashOnBlockchain(Order $order, Orderer $orderer, float $finalPrice): void
    {
        try {
            $workingDir = env('BLOCKCHAIN_PATH');
            $contractAddr = env('SMART_CONTRACT_ADDRESS');

            if (!$workingDir || !$contractAddr) {
                Log::warning('Blockchain configuration missing. Skipping blockchain storage.');
                return;
            }

            // Tạo chuỗi để băm (Thông tin quan trọng không được sửa đổi)
            $dataToHash = sprintf(
                "%s|%s|%s|%s|%.2f",
                $order->order_code,
                $orderer->orderer_name,
                $orderer->orderer_email,
                $orderer->orderer_phone,
                $finalPrice
            );
            $hash = hash('sha256', $dataToHash);

            // Lưu hash vào database
            $order->invoice_hash = $hash;
            $order->save();

            // Kiểm tra xem script có tồn tại không
            $nodeScript = "scripts/store-hash.ts";
            $scriptPath = rtrim($workingDir, '/\\') . DIRECTORY_SEPARATOR . $nodeScript;
            
            if (!file_exists($scriptPath)) {
                Log::warning('Blockchain script not found. Skipping blockchain storage.', [
                    'script_path' => $scriptPath,
                    'order_code' => $order->order_code
                ]);
                return;
            }

            // Gọi script Node.js để ghi lên Blockchain
            // Hardhat không hỗ trợ truyền tham số trực tiếp sau --, nên dùng biến môi trường
            // Script store-hash.ts cần đọc từ process.env.ORDER_CODE, process.env.HASH, process.env.CONTRACT_ADDR, process.env.PRIVATE_KEY
            $privateKey = env('BLOCKCHAIN_PRIVATE_KEY', '');
            
            if (empty($privateKey)) {
                Log::warning('BLOCKCHAIN_PRIVATE_KEY not configured. Skipping blockchain storage.');
                return;
            }
            
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            if ($isWindows) {
                // Windows: Dùng PowerShell để set biến môi trường và chạy command
                $command = sprintf(
                    'cd /d %s && powershell -Command "$env:ORDER_CODE=\'%s\'; $env:HASH=\'%s\'; $env:CONTRACT_ADDR=\'%s\'; $env:PRIVATE_KEY=\'%s\'; npx hardhat run %s --network localhost"',
                    escapeshellarg($workingDir),
                    addslashes($order->order_code),
                    addslashes($hash),
                    addslashes($contractAddr),
                    addslashes($privateKey),
                    $nodeScript
                );
            } else {
                // Linux/Mac: Dùng biến môi trường trực tiếp
                $envVars = sprintf(
                    'ORDER_CODE=%s HASH=%s CONTRACT_ADDR=%s PRIVATE_KEY=%s',
                    escapeshellarg($order->order_code),
                    escapeshellarg($hash),
                    escapeshellarg($contractAddr),
                    escapeshellarg($privateKey)
                );
                $command = sprintf(
                    'cd %s && %s npx hardhat run %s --network localhost',
                    escapeshellarg($workingDir),
                    $envVars,
                    $nodeScript
                );
            }

            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar === 0) {
                // Lấy output JSON từ script JS
                $outputString = implode("\n", $output);
                preg_match('/\{.*\}/s', $outputString, $matches);
                $jsonOutput = json_decode($matches[0] ?? '{}', true);

                if (($jsonOutput['status'] ?? '') === 'success') {
                    // Lưu transaction hash vào database
                    $order->blockchain_tx_hash = $jsonOutput['tx_hash'] ?? null;
                    $order->save();
                    Log::info('Order hash stored on blockchain', [
                        'order_code' => $order->order_code,
                        'tx_hash' => $order->blockchain_tx_hash
                    ]);
                } else {
                    Log::warning('Blockchain storage failed', [
                        'order_code' => $order->order_code,
                        'output' => $jsonOutput
                    ]);
                }
            } else {
                // Kiểm tra xem có phải lỗi HHE506 (tham số không được nhận) không
                $outputString = implode("\n", $output);
                $isHHE506Error = strpos($outputString, 'HHE506') !== false || 
                                strpos($outputString, 'not associated with any task') !== false;
                
                if ($isHHE506Error) {
                    // Lỗi này xảy ra khi script không được viết đúng để nhận tham số
                    // Đơn hàng vẫn được tạo thành công, chỉ là không ghi lên blockchain
                    Log::warning('Blockchain script configuration issue. Order created successfully but not stored on blockchain.', [
                        'order_code' => $order->order_code,
                        'error_type' => 'HHE506',
                        'note' => 'Script store-hash.js may need to be updated to properly receive parameters',
                        'output' => $output
                    ]);
                } else {
                    // Các lỗi khác (network, contract, etc.)
                    Log::warning('Blockchain script execution failed. Order created successfully but not stored on blockchain.', [
                        'order_code' => $order->order_code,
                        'output' => $output,
                        'return_var' => $returnVar
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Không throw exception để không làm gián đoạn quá trình tạo đơn
            Log::error('Error storing order hash on blockchain', [
                'order_code' => $order->order_code ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }
}