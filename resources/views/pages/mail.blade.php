<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác Nhận Đặt Phòng - MyHotel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #ff6b9d;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ff6b9d;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
        }
        .info-table tr {
            border-bottom: 1px solid #e0e0e0;
        }
        .info-table tr:last-child {
            border-bottom: none;
        }
        .info-table td {
            padding: 12px 15px;
            font-size: 14px;
        }
        .info-table td:first-child {
            font-weight: bold;
            color: #555;
            width: 40%;
            background-color: #f0f0f0;
        }
        .info-table td:last-child {
            color: #333;
        }
        .order-code {
            color: #e74c3c;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
        }
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: #fff;
            border: 2px solid #ff6b9d;
            border-radius: 8px;
            overflow: hidden;
        }
        .price-table th {
            background-color: #ff6b9d;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
            font-weight: bold;
        }
        .price-table td {
            padding: 12px 15px;
            font-size: 14px;
            border-bottom: 1px solid #e0e0e0;
        }
        .price-table tr:last-child td {
            border-bottom: none;
        }
        .price-table .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .price-table .total-row td {
            font-size: 18px;
            color: #e74c3c;
            padding: 15px;
        }
        .price-value {
            text-align: right;
            color: #333;
        }
        .company-intro {
            background: #ff6b9d;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
            text-align: center;
        }
        .company-intro h3 {
            color: #ff6b9d;
            font-size: 20px;
            margin-bottom: 15px;
        }
        .company-intro p {
            color: #555;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 10px;
        }
        .company-features {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .feature-item {
            text-align: center;
            padding: 10px 5px;
        }
        .feature-icon {
            font-size: 28px;
            margin-bottom: 8px;
            line-height: 1;
        }
        .feature-text {
            font-size: 11px;
            color: #666;
            font-weight: bold;
            word-break: break-word;
        }
        .qr-code-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }
        .qr-code-section h3 {
            color: white;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .qr-code-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .qr-code-container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            margin: 10px 0;
        }
        .qr-code-container img {
            max-width: 180px;
            height: auto;
            display: block;
        }
        .qr-code-link {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13px;
            transition: all 0.3s;
        }
        .qr-code-link:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }
        .email-footer {
            background-color: #2c3e50;
            color: white;
            padding: 25px 20px;
            text-align: center;
        }
        .email-footer p {
            font-size: 12px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .email-footer a {
            color: #3498db;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        .highlight-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .highlight-box strong {
            color: #856404;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .company-features {
                flex-direction: row;
                justify-content: center;
            }
            .feature-item {
                flex: 0 0 calc(50% - 10px);
                max-width: calc(50% - 10px);
                min-width: calc(50% - 10px);
                padding: 10px 5px;
            }
            .feature-icon {
                font-size: 24px;
            }
            .feature-text {
                font-size: 10px;
            }
            .qr-code-container img {
                max-width: 150px;
            }
        }
        @media only screen and (max-width: 400px) {
            .company-features {
                flex-direction: column;
                align-items: center;
            }
            .feature-item {
                flex: 0 0 100%;
                max-width: 200px;
                min-width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🏨 MyHotel</h1>
            <p>Tìm Kiếm Khách Sạn Tại Khu Vực Đà Nẵng</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Greeting -->
            <div class="greeting">
                <p>Xin chào <strong>{{ $customer_name }}</strong>,</p>
                @if(isset($order_status) && $order_status == 1)
                    <p>Cảm ơn bạn đã đặt phòng tại MyHotel! Đơn hàng của bạn đã được thanh toán thành công và sẵn sàng để check-in.</p>
                @elseif(isset($order_status) && $order_status == 0)
                    <p>Cảm ơn bạn đã đặt phòng tại MyHotel! Đơn hàng của bạn đã được duyệt và sẵn sàng để check-in.</p>
                @else
                    <p>Cảm ơn bạn đã đặt phòng tại MyHotel! Yêu cầu đặt hàng của bạn đã được ghi nhận và đang chờ xử lý.</p>
                @endif
            </div>

            <!-- Thông Tin Người Đặt Phòng -->
            <div class="section">
                <div class="section-title">👤 Thông Tin Người Đặt Phòng</div>
                <table class="info-table">
                    <tr>
                        <td>Tên Khách Hàng</td>
                        <td>{{ $customer_name }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $customer_email }}</td>
                    </tr>
                    <tr>
                        <td>Số Điện Thoại</td>
                        <td>{{ $customer_phone }}</td>
                    </tr>
                </table>
            </div>

            <!-- Thông Tin Đơn Hàng -->
            <div class="section">
                <div class="section-title">📋 Thông Tin Đơn Đặt Phòng</div>
                <table class="info-table">
                    <tr>
                        <td>Mã Đặt Phòng</td>
                        <td><span class="order-code">{{ $order_details['order_code'] }}</span></td>
                    </tr>
                    <tr>
                        <td>Tên Khách Sạn</td>
                        <td><strong>{{ $order_details['hotel_name'] }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tên Phòng</td>
                        <td>{{ $order_details['room_name'] }}</td>
                    </tr>
                    @if(isset($order_details['type_room_bed']) && $order_details['type_room_bed'] != 'N/A')
                    <tr>
                        <td>Loại Giường</td>
                        <td>
                            @if($order_details['type_room_bed'] == 1)
                                1 Giường Đơn
                            @elseif($order_details['type_room_bed'] == 2)
                                2 Giường Đơn
                            @else
                                {{ $order_details['type_room_bed'] }}
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if(isset($order_details['type_room_condition']) && $order_details['type_room_condition'] != 'N/A')
                    <tr>
                        <td>Điều Kiện Phòng</td>
                        <td>{{ $order_details['type_room_condition'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Mã Check-in -->
            @if(isset($checkin_code) && $checkin_code)
            <div class="section">
                <div class="checkin-code-box" style="background-color: #fff3cd; padding: 20px; border-radius: 8px; border: 2px solid #ffc107; text-align: center; margin: 20px 0;">
                    <h3 style="color: #856404; margin-bottom: 15px; font-size: 18px;">🎉 Mã Check-in Của Bạn</h3>
                    <p style="color: #856404; margin-bottom: 15px;">Vui lòng sử dụng mã này để check-in khi đến khách sạn:</p>
                    <div style="font-size: 28px; font-weight: bold; color: #ff6b9d; letter-spacing: 3px; margin: 15px 0; padding: 15px; background-color: white; border-radius: 5px; display: inline-block;">
                        {{ $checkin_code }}
                    </div>
                    <p style="margin-top: 15px; font-size: 14px; color: #856404;">
                        <strong>Lưu ý:</strong> Vui lòng giữ mã này cẩn thận và trình cho nhân viên khách sạn khi check-in.
                    </p>
                </div>
            </div>
            @endif

            <div class="section">
                <div class="section-title">💰 Chi Tiết Thanh Toán</div>
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Hạng Mục</th>
                            <th class="price-value">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($order_details['base_price']))
                            <tr>
                                <td>Giá Phòng</td>
                                <td class="price-value">{{ number_format($order_details['base_price'], 0, ',', '.') }} đ</td>
                            </tr>
                            @if(isset($order_details['hotel_fee']) && $order_details['hotel_fee'] > 0)
                            <tr>
                                <td>Phí Dịch Vụ</td>
                                <td class="price-value">+ {{ number_format($order_details['hotel_fee'], 0, ',', '.') }} đ</td>
                            </tr>
                            @endif
                            @if(isset($order_details['coupon_name_code']) && $order_details['coupon_name_code'] != 'Không Có' && $order_details['coupon_name_code'] != 'Không có')
                            <tr>
                                <td>Mã Giảm Giá ({{ $order_details['coupon_name_code'] }})</td>
                                <td class="price-value" style="color: #27ae60;">- {{ number_format($order_details['coupon_price_sale'] ?? 0, 0, ',', '.') }} đ</td>
                            </tr>
                            @endif
                        @else
                            <tr>
                                <td>Giá Phòng</td>
                                <td class="price-value">{{ number_format($order_details['price_room'], 0, ',', '.') }} đ</td>
                            </tr>
                            @if(isset($order_details['hotel_fee']) && $order_details['hotel_fee'] > 0)
                            <tr>
                                <td>Phí Dịch Vụ</td>
                                <td class="price-value">+ {{ number_format($order_details['hotel_fee'], 0, ',', '.') }} đ</td>
                            </tr>
                            @endif
                            @if(isset($order_details['coupon_name_code']) && $order_details['coupon_name_code'] != 'Không Có' && $order_details['coupon_name_code'] != 'Không có')
                            <tr>
                                <td>Mã Giảm Giá ({{ $order_details['coupon_name_code'] }})</td>
                                <td class="price-value" style="color: #27ae60;">- {{ number_format($order_details['coupon_price_sale'] ?? 0, 0, ',', '.') }} đ</td>
                            </tr>
                            @endif
                        @endif
                        <tr class="total-row">
                            <td><strong>Tổng Thanh Toán</strong></td>
                            <td class="price-value"><strong>{{ number_format($total_price, 0, ',', '.') }} đ</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Lưu Ý -->
            <div class="highlight-box">
                <strong>📌 Lưu ý quan trọng:</strong>
                <ul style="margin-top: 10px; padding-left: 20px; color: #856404;">
                    <li>Vui lòng giữ mã đặt phòng để tra cứu thông tin đơn hàng</li>
                    @if(isset($checkin_code) && $checkin_code)
                        <li>Mã check-in đã được gửi kèm trong email này</li>
                        <li>Vui lòng mang mã check-in khi đến khách sạn</li>
                    @else
                        <li>Đơn hàng của bạn đang chờ xác nhận từ khách sạn</li>
                        <li>Bạn sẽ nhận được email xác nhận và mã check-in khi đơn hàng được duyệt</li>
                    @endif
                </ul>
            </div>

            <!-- Giới Thiệu Về MyHotel -->
            <div class="company-intro">
                <h3>🌟 Về MyHotel</h3>
                <p>
                    MyHotel là nền tảng đặt phòng khách sạn hàng đầu tại khu vực Đà Nẵng, 
                    mang đến cho bạn những trải nghiệm tuyệt vời với dịch vụ chuyên nghiệp và giá cả hợp lý.
                </p>
                <p style="margin-top: 20px; font-size: 13px;">
                    <strong>Địa chỉ:</strong> Đà Nẵng, Việt Nam<br>
                    <strong>Hotline:</strong> 1900-xxxx | <strong>Email:</strong> support@myhotel.vn
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>MyHotel - Tìm Kiếm Khách Sạn Tại Khu Vực Đà Nẵng</strong></p>
            <p>Email này được gửi tự động từ hệ thống MyHotel. Vui lòng không trả lời email này.</p>
            <p style="margin-top: 15px;">
                <a href="#">Trang chủ</a> | 
                <a href="#">Liên hệ</a> | 
                <a href="#">Hỗ trợ</a>
            </p>
            <p style="margin-top: 15px; font-size: 11px; opacity: 0.7;">
                © {{ date('Y') }} MyHotel. Tất cả quyền được bảo lưu.
            </p>
        </div>
    </div>
</body>
</html>
