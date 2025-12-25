<?php
namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use App\Models\Customers;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Nette\Utils\Random;

// Ngân hàng	NCB
// Số thẻ	9704198526191432198
// Tên chủ thẻ	NGUYEN VAN A
// Ngày phát hành	07/15
// Mật khẩu OTP	123456

class VnpayService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('vnpay', []);
    }

    private function createOrderer($customer, $typeRoom, $payload)
    {
        $orderer                               = new \App\Models\Orderer();
        $orderer->customer_id                  = $customer->customer_id;
        $orderer->orderer_name                 = $customer->customer_name;
        $orderer->orderer_phone                = $customer->customer_phone;
        $orderer->orderer_email                = $customer->customer_email;
        $orderer->orderer_type_bed             = $typeRoom->type_room_bed;
        $orderer->orderer_special_requirements = $payload['order_require'] ?? null;
        $orderer->orderer_own_require          = $payload['require_text'] ?? null;
        $orderer->save();
        return $orderer;
    }

    private function createPayment()
    {
        $payment                 = new Payment();
        $payment->payment_method = 2; // 2: Thanh toán bằng VNPay
        $payment->payment_status = 0;
        $payment->save();
        return $payment;
    }

    private function createOrderDetail($orderCode, $hotel, $room, $typeRoom, $finalPrice, $servicePrice)
    {
        $orderDetail               = new OrderDetails();
        $orderDetail->order_code   = $orderCode;
        $orderDetail->hotel_id     = $hotel->hotel_id;
        $orderDetail->hotel_name   = $hotel->hotel_name;
        $orderDetail->room_id      = $room->room_id;
        $orderDetail->room_name    = $room->room_name;
        $orderDetail->type_room_id = $typeRoom->type_room_id;
        $orderDetail->price_room   = $finalPrice;
        $orderDetail->hotel_fee    = $servicePrice;
        $orderDetail->save();
        return $orderDetail;
    }

    private function createOrderForVnpay(array $payload, string $orderCode): array
    {
        $typeRoom = TypeRoom::query()->with(['room.hotel'])->find($payload['type_room_id'] ?? null);
        if (! $typeRoom) {
            return [null, 'Loại phòng không tồn tại'];
        }
        $room  = $typeRoom->room;
        $hotel = $room?->hotel;
        if (! $room || ! $hotel) {
            return [null, 'Phòng hoặc khách sạn không tồn tại'];
        }
        $customer = Customers::query()->find($payload['customer_id'] ?? null);
        if (! $customer instanceof Customers) {
            return [null, 'Khách hàng không tồn tại'];
        }
        $coupon        = ! empty($payload['coupon_id']) ? Coupon::query()->find($payload['coupon_id']) : null;
        $serviceCharge = ServiceCharge::query()->where('hotel_id', $hotel->hotel_id)->first();

        $basePrice = $typeRoom->type_room_price * max(1, (int) ($payload['day'] ?? 1));
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

        // Tạo orderer
        $orderer = $this->createOrderer($customer, $typeRoom, $payload);
        // Tạo payment
        $payment = $this->createPayment();
        // Tạo order detail
        $orderDetail = $this->createOrderDetail($orderCode, $hotel, $room, $typeRoom, $finalPrice, $servicePrice);

        // Tạo order
        $order                    = new Order();
        $order->start_day         = $payload['startDay'] ?? null;
        $order->end_day           = $payload['endDay'] ?? null;
        $order->orderer_id        = $orderer->orderer_id;
        $order->payment_id        = $payment->payment_id;
        $order->order_status      = 0;
        $order->order_code        = $orderCode;
        $order->coupon_name_code  = $coupon?->coupon_name_code ?? 'Không có';
        $order->coupon_sale_price = $couponPrice;
        $order->order_type        = 0;
        $order->total_price       = $finalPrice;
        $order->save();
        return [$order, null];
    }

    // Request
    // {
    //     "type_room_id": 12,
    //     "customer_id": 345,
    //     "day": 3,
    //     "startDay": "2025-11-15",
    //     "endDay": "2025-11-18",
    //     "order_require": "Yêu cầu phòng yên tĩnh",
    //     "require_text": "Xin thêm gối",
    //     "coupon_id": 27,
    //     "order_code": "ORD20251112001",
    //     "order_info": "Thanh toán đơn hàng ORD20251112001",
    //     "bank_code": "NCB"
    //   }
    public function createPaymentUrl(Request $request): JsonResponse
    {
        if (! $this->isConfigured()) {
            return ApiResponse::error('Cấu hình VNPAY không hợp lệ', 500);
        }
        $payload = $request->all();
        // Đảm bảo timezone là Asia/Ho_Chi_Minh để khớp với VNPay
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $orderCode = $payload['order_code'] ?? $now->format('YmdHis') . Random::generate(6, '0123456789');
        $orderInfo = $payload['order_info'] ?? sprintf('Thanh toán đơn hàng %s', $orderCode);
        [$order, $errorMessage] = $this->createOrderForVnpay($payload, $orderCode);
        if ($errorMessage !== null || ! $order instanceof Order) {
            return ApiResponse::error($errorMessage ?? 'Không thể tạo đơn hàng', 400);
        }
        $amount = (float) ($order->total_price ?? 0);
        if ($amount <= 0) {
            return ApiResponse::error('Số tiền thanh toán không hợp lệ', 400);
        }
        // Resolve return URL
        $returnUrl = $this->config['return_url'] ?? $this->resolveReturnUrl($request);
        
        // Log để debug
        Log::info('VNPay create payment URL - Config check', [
            'tmn_code' => $this->config['tmn_code'] ? '***' . substr($this->config['tmn_code'], -4) : 'MISSING',
            'has_hash_secret' => !empty($this->config['hash_secret']),
            'vnp_payment_url' => $this->config['vnp_payment_url'] ?? 'MISSING',
            'return_url' => $returnUrl,
            'return_url_config' => $this->config['return_url'] ?? 'NOT_SET',
        ]);
        
        $vnpParams = [
            'vnp_Version'    => $this->config['version'] ?? '2.1.0',
            'vnp_Command'    => $this->config['command'] ?? 'pay',
            'vnp_TmnCode'    => $this->config['tmn_code'],
            'vnp_Amount'     => (int) round($amount * 100),
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_CurrCode'   => $this->config['curr_code'] ?? 'VND',
            'vnp_IpAddr'     => $request->ip(),
            'vnp_Locale'     => $this->config['locale'] ?? 'vn',
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => $this->config['order_type'] ?? 'other',
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_TxnRef'     => $orderCode,
        ];
        if (! empty($payload['bank_code'])) {
            $vnpParams['vnp_BankCode'] = $payload['bank_code'];
        }
        $expiredMinutes = (int) ($this->config['expired_minutes'] ?? 60);
        if ($expiredMinutes > 0) {
            $vnpParams['vnp_ExpireDate'] = $now->copy()->addMinutes($expiredMinutes)->format('YmdHis');
        }
        ksort($vnpParams);
        $hashData  = [];
        $queryData = [];
        $i = 0;
        
        // Build querystring và hashdata theo chuẩn VNPay 2.1.0
        // Lưu ý: Trong phiên bản 2.1.0, hashdata cũng phải dùng urlencode cho cả key và value
        foreach ($vnpParams as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            
            // Build hashdata với urlencode (phiên bản 2.1.0)
            if ($i == 0) {
                $hashData[] = urlencode($key) . '=' . urlencode((string) $value);
            } else {
                $hashData[] = '&' . urlencode($key) . '=' . urlencode((string) $value);
            }
            $i = 1;
            
            // Build query string
            $queryData[] = urlencode($key) . '=' . urlencode((string) $value);
        }

        // Implode hashData - không dùng '&' vì đã có trong hashData
        $hashString = implode('', $hashData);
        $secureHash = hash_hmac('sha512', $hashString, $this->config['hash_secret']);

        $paymentUrl = rtrim($this->config['vnp_payment_url'], '?') . '?' . implode('&', $queryData) . '&vnp_SecureHash=' . $secureHash;

        return ApiResponse::success([
            'paymentUrl' => $paymentUrl,
            'orderCode'  => $order->order_code,
            'amount'     => $amount,
            'orderInfo'  => $orderInfo,
            'expireDate' => $vnpParams['vnp_ExpireDate'] ?? null,
        ], 'Tạo URL thanh toán thành công');
    }

    // Request
    // {
    //     "vnp_TxnRef": "ORD20251112001",
    //     "order_code": "ORD20251112001",
    //     "vnp_ResponseCode": "00",
    //     "vnp_TransactionStatus": "00",
    //   }
    public function handleReturn(Request $request): JsonResponse
    {
        $request = $request->all();
        //
        $order = Order::query()->where('order_code', $request['order_code'])->first();
        if (! $order) {
            return ApiResponse::error('Đơn hàng không tồn tại', 404);
        }
        if ($request['vnp_ResponseCode'] !== '00' || $request['vnp_TransactionStatus'] !== '00') {
            OrderDetails::query()->where('order_code', $request['order_code'])->delete();
            Orderer::query()->where('orderer_id', $order->orderer_id)->delete();
            Payment::query()->where('payment_id', $order->payment_id)->delete();
            Order::query()->where('order_code', $request['order_code'])->delete();
            $error = $this->handleReturnStatusPayment($request['vnp_ResponseCode'], $request['vnp_TransactionStatus']);
            return ApiResponse::error($error['message_response'], 400);
        }

        Order::query()->where('order_code', $request['order_code'])->update([
            'order_status' => 1,
        ]);
        Payment::query()->where('payment_id', $order->payment_id)->update([
            'payment_status' => 1,
        ]);
        $error = $this->handleReturnStatusPayment($request['vnp_ResponseCode'], $request['vnp_TransactionStatus']);
        return ApiResponse::success($error['message_response'], 200);
    }

    private function handleReturnStatusPayment($code, $status)
    {
        $statusMessages = [
            '00' => 'Giao dịch thành công',
            '01' => 'Giao dịch chưa hoàn tất',
            '02' => 'Giao dịch bị lỗi',
            '04' => 'Giao dịch đảo (Khách hàng đã bị trừ tiền tại Ngân hàng nhưng GD chưa thành công ở VNPAY)',
            '05' => 'VNPAY đang xử lý giao dịch này (GD hoàn tiền)',
            '06' => 'VNPAY đã gửi yêu cầu hoàn tiền sang Ngân hàng (GD hoàn tiền)',
            '07' => 'Giao dịch bị nghi ngờ gian lận',
            '09' => 'GD Hoàn trả bị từ chối',
        ];

        $responseMessages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];

        return [
            'message_status'   => $statusMessages[$status] ?? 'Không xác định',
            'message_response' => $responseMessages[$code] ?? 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];
    }

    public function handleIpn(Request $request): array
    {
        $params = $request->all();
        
        // Log IPN request để debug
        Log::info('VNPay IPN received', [
            'params' => $params,
            'method' => $request->method(),
            'ip' => $request->ip()
        ]);

        if (! $this->validateSignature($params)) {
            Log::error('VNPay IPN: Invalid signature', ['params' => $params]);
            return ['RspCode' => '97', 'Message' => 'Invalid signature'];
        }

        $order = $this->getOrderFromCallback($params);
        if (! $order instanceof Order) {
            Log::warning('VNPay IPN: Order not found', [
                'vnp_TxnRef' => $params['vnp_TxnRef'] ?? null,
                'params' => $params
            ]);
            return ['RspCode' => '01', 'Message' => 'Order not found'];
        }

        if (! $this->validateAmount($order, $params)) {
            Log::warning('VNPay IPN: Invalid amount', [
                'order_code' => $order->order_code,
                'order_amount' => $order->total_price,
                'vnp_Amount' => isset($params['vnp_Amount']) ? ((int) $params['vnp_Amount']) / 100 : null,
                'params' => $params
            ]);
            return ['RspCode' => '04', 'Message' => 'Invalid amount'];
        }

        $payment = $order->payment;
        if ($payment && (int) $payment->payment_status === 1) {
            Log::info('VNPay IPN: Order already confirmed', [
                'order_code' => $order->order_code,
                'payment_status' => $payment->payment_status
            ]);
            return ['RspCode' => '02', 'Message' => 'Order already confirmed'];
        }

        // Kiểm tra thành công: vnp_ResponseCode = '00'
        // vnp_TransactionStatus có thể không có trong IPN callback, chỉ kiểm tra nếu có
        $isSuccess = $params['vnp_ResponseCode'] === '00';
        if (isset($params['vnp_TransactionStatus'])) {
            $isSuccess = $isSuccess && $params['vnp_TransactionStatus'] === '00';
        }

        $this->syncOrderStatus($order, $isSuccess, $params);

        if ($isSuccess) {
            Log::info('VNPay IPN: Payment confirmed successfully', [
                'order_code' => $order->order_code,
                'vnp_TransactionNo' => $params['vnp_TransactionNo'] ?? null
            ]);
            return ['RspCode' => '00', 'Message' => 'Confirm Success'];
        }

        Log::warning('VNPay IPN: Payment failed', [
            'order_code' => $order->order_code,
            'vnp_ResponseCode' => $params['vnp_ResponseCode'] ?? null,
            'vnp_TransactionStatus' => $params['vnp_TransactionStatus'] ?? null
        ]);
        return ['RspCode' => '99', 'Message' => 'Payment failed'];
    }

    private function syncOrderStatus(Order $order, bool $isSuccess, array $params): array
    {
        $payment = $order->payment;

        if (! $payment) {
            Log::warning('VNPAY callback without payment record', [
                'order_id'   => $order->order_id,
                'order_code' => $order->order_code,
            ]);
            return [
                'paymentStatus' => null,
                'orderStatus'   => (int) $order->order_status,
            ];
        }

        $paymentStatusBefore = (int) $payment->payment_status;
        $orderStatusBefore   = (int) $order->order_status;

        if ((int) $payment->payment_method !== 2) {
            $payment->payment_method = 2; // 2: Thanh toán bằng VNPay
        }

        $targetPaymentStatus = $isSuccess ? 1 : 0;
        if ($paymentStatusBefore !== $targetPaymentStatus) {
            $payment->payment_status = $targetPaymentStatus;
        }

        if ($isSuccess) {
            if (! in_array($orderStatusBefore, [1, 2], true)) {
                $order->order_status = 1;
            }
        } else {
            if ($orderStatusBefore === 0) {
                $order->order_status = -1;
            }
        }

        if ($payment->isDirty()) {
            $payment->save();
        }

        if ($order->isDirty()) {
            $order->save();
        }

        Log::info('VNPAY callback processed', [
            'order_id'              => $order->order_id,
            'order_code'            => $order->order_code,
            'is_success'            => $isSuccess,
            'vnp_ResponseCode'      => Arr::get($params, 'vnp_ResponseCode'),
            'vnp_TransactionStatus' => Arr::get($params, 'vnp_TransactionStatus'),
            'vnp_TransactionNo'     => Arr::get($params, 'vnp_TransactionNo'),
        ]);

        return [
            'paymentStatus' => (int) $payment->payment_status,
            'orderStatus'   => (int) $order->order_status,
        ];
    }

    private function validateSignature(array $params): bool
    {
        $secureHash = $params['vnp_SecureHash'] ?? '';
        $secureHashType = $params['vnp_SecureHashType'] ?? '';
        
        if ($secureHash === '' || ! $this->isConfigured()) {
            Log::warning('VNPay signature validation failed: missing secureHash or config', [
                'hasSecureHash' => !empty($secureHash),
                'isConfigured' => $this->isConfigured()
            ]);
            return false;
        }
        
        // Loại bỏ vnp_SecureHash và vnp_SecureHashType khỏi params để tính hash
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        // Chỉ lấy các params bắt đầu bằng vnp_
        $params = array_filter(
            $params,
            static fn($value, $key) => str_starts_with($key, 'vnp_'),
            ARRAY_FILTER_USE_BOTH
        );

        ksort($params);
        $hashData = [];
        $i = 0;

        // Build hashdata theo chuẩn VNPay 2.1.0 (dùng urlencode cho cả key và value)
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            
            if ($i == 0) {
                $hashData[] = urlencode($key) . '=' . urlencode((string) $value);
            } else {
                $hashData[] = '&' . urlencode($key) . '=' . urlencode((string) $value);
            }
            $i = 1;
        }

        $hashString = implode('', $hashData);
        
        // VNPay 2.1.0 dùng SHA512, nhưng cũng hỗ trợ MD5 và SHA256 (từ vnp_SecureHashType)
        $calculatedHash = '';
        if ($secureHashType === 'MD5') {
            $calculatedHash = md5($this->config['hash_secret'] . $hashString);
        } elseif ($secureHashType === 'SHA256') {
            $calculatedHash = hash('sha256', $this->config['hash_secret'] . $hashString);
        } else {
            // Mặc định dùng SHA512 (phiên bản 2.1.0)
            $calculatedHash = hash_hmac('sha512', $hashString, $this->config['hash_secret']);
        }

        $isValid = hash_equals($calculatedHash, $secureHash);
        
        if (!$isValid) {
            Log::warning('VNPay signature validation failed', [
                'calculatedHash' => $calculatedHash,
                'receivedHash' => $secureHash,
                'hashType' => $secureHashType,
                'hashString' => $hashString,
                'params' => $params
            ]);
        }

        return $isValid;
    }

    private function getOrderFromCallback(array $params): ?Order
    {
        $orderCode = $params['vnp_TxnRef'] ?? null;
        if (! $orderCode) {
            return null;
        }

        return Order::query()
            ->where('order_code', $orderCode)
            ->first();
    }

    private function validateAmount(Order $order, array $params): bool
    {
        $amount = isset($params['vnp_Amount']) ? ((int) $params['vnp_Amount']) / 100 : null;

        if ($amount === null) {
            return false;
        }

        return (float) $order->total_price === (float) $amount;
    }

    private function isConfigured(): bool
    {
        // dd($this->config['vnp_payment_url'] , $this->config['tmn_code'] , $this->config['hash_secret']);
        return ! empty($this->config['tmn_code'])
        && ! empty($this->config['hash_secret'])
        && ! empty($this->config['vnp_payment_url']);
    }

    private function resolveReturnUrl(Request $request): string
    {
        // Return URL trỏ về frontend page để xử lý callback
        // Nếu có config return_url thì dùng, không thì tự động resolve
        if (!empty($this->config['return_url'])) {
            return $this->config['return_url'];
        }
        
        // Tự động resolve từ request hoặc env
        $frontendUrl = env("FRONTEND_URL", env("APP_URL", "http://localhost:3000"));
        $returnUrl = rtrim($frontendUrl, '/') . '/payment/vnpay-callback';
        
        // Log để debug
        Log::info('VNPay Return URL resolved', [
            'returnUrl' => $returnUrl,
            'frontendUrl' => $frontendUrl,
            'appUrl' => env("APP_URL")
        ]);
        
        return $returnUrl;
    }
    
    private function resolveIpnUrl(Request $request): string
    {
        // IPN URL trỏ về backend API để xử lý callback từ VNPay
        if (!empty($this->config['ipn_url'])) {
            return $this->config['ipn_url'];
        }
        
        // Tự động resolve từ request
        $backendUrl = env("APP_URL", $request->getSchemeAndHttpHost());
        $ipnUrl = rtrim($backendUrl, '/') . '/api/payment/vnpay/ipn';
        
        // Log để debug
        Log::info('VNPay IPN URL resolved', [
            'ipnUrl' => $ipnUrl,
            'backendUrl' => $backendUrl
        ]);
        
        return $ipnUrl;
    }

    private function buildCallbackUrl(Request $request, string $path): string
    {
        $baseUrl = rtrim(config('app.url') ?? $request->getSchemeAndHttpHost(), '/');
        return 'http://localhost/DoAnCoSo2/api/vnpay-payment-callback';
    }

    public function vnpayPaymentCallback(Request $request): JsonResponse
    {
        $params = $request->all();

        // Define error code maps
        $transactionStatusMap = [
            '00' => 'Giao dịch thành công',
            '01' => 'Giao dịch chưa hoàn tất',
            '02' => 'Giao dịch bị lỗi',
            '04' => 'Giao dịch đảo (Khách hàng đã bị trừ tiền tại Ngân hàng nhưng GD chưa thành công ở VNPAY)',
            '05' => 'VNPAY đang xử lý giao dịch này (GD hoàn tiền)',
            '06' => 'VNPAY đã gửi yêu cầu hoàn tiền sang Ngân hàng (GD hoàn tiền)',
            '07' => 'Giao dịch bị nghi ngờ gian lận',
            '09' => 'GD Hoàn trả bị từ chối',
        ];
        $responseCodeMap = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];

        $responseCode      = $params['vnp_ResponseCode'] ?? null;
        $transactionStatus = $params['vnp_TransactionStatus'] ?? null;

        $responseMessage    = $responseCodeMap[$responseCode] ?? 'Không xác định';
        $transactionMessage = $transactionStatusMap[$transactionStatus] ?? 'Không xác định';

        $isSuccess = $responseCode === '00' && $transactionStatus === '00';

        if ($isSuccess) {
            return ApiResponse::success([
                'orderCode'             => $params['vnp_TxnRef'] ?? null,
                'transactionNo'         => $params['vnp_TransactionNo'] ?? null,
                'amount'                => isset($params['vnp_Amount']) ? ((int) $params['vnp_Amount']) / 100 : null,
                'vnp_ResponseCode'      => $responseCode,
                'vnp_TransactionStatus' => $transactionStatus,
                'message'               => $responseMessage,
                'transactionMessage'    => $transactionMessage,
            ], 'Thanh toán thành công');
        }

        // Return error with mapped message
        return ApiResponse::error('Thanh toán không thành công', 400, [
            'orderCode'             => $params['vnp_TxnRef'] ?? null,
            'transactionNo'         => $params['vnp_TransactionNo'] ?? null,
            'amount'                => isset($params['vnp_Amount']) ? ((int) $params['vnp_Amount']) / 100 : null,
            'vnp_ResponseCode'      => $responseCode,
            'vnp_TransactionStatus' => $transactionStatus,
            'message'               => $responseMessage,
            'transactionMessage'    => $transactionMessage,
        ]);
    }
}
