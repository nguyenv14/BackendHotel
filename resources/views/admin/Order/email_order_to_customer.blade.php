<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $type }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .order-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .checkin-code {
            background-color: #fff3cd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border: 2px solid #ffc107;
            text-align: center;
        }
        .checkin-code h3 {
            margin: 0 0 10px 0;
            color: #856404;
        }
        .checkin-code .code {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 2px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $type }}</h1>
    </div>
    
    <div class="content">
        <p>Xin chào <strong>{{ $order->orderer->orderer_name ?? 'Khách hàng' }}</strong>,</p>
        
        <p>Chúng tôi xin thông báo:</p>
        
        <div class="order-info">
            <h3>Thông Tin Đơn Hàng</h3>
            <p><strong>Mã Đơn Hàng:</strong> {{ $order->order_code }}</p>
            <p><strong>Ngày Nhận Phòng:</strong> {{ $order->start_day }}</p>
            <p><strong>Ngày Trả Phòng:</strong> {{ $order->end_day }}</p>
            <p><strong>Tổng Tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }} đ</p>
        </div>

        @if($order_status == \App\Http\Enums\OrderStatus::WAITING_FOR_APPROVAL && $order->checkin_code)
        <div class="checkin-code">
            <h3>🎉 Mã Check-in Của Bạn</h3>
            <p>Vui lòng sử dụng mã này để check-in khi đến khách sạn:</p>
            <div class="code">{{ $order->checkin_code }}</div>
            <p style="margin-top: 10px; font-size: 14px; color: #856404;">
                <strong>Lưu ý:</strong> Vui lòng giữ mã này cẩn thận và trình cho nhân viên khách sạn khi check-in.
            </p>
        </div>
        @endif

        @if(isset($orderdetails) && $orderdetails->count() > 0)
        <div class="order-info">
            <h3>Chi Tiết Đơn Hàng</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tên Khách Sạn</th>
                        <th>Tên Phòng</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderdetails as $detail)
                    <tr>
                        <td>{{ $detail->hotel_name ?? 'N/A' }}</td>
                        <td>{{ $detail->room_name ?? 'N/A' }}</td>
                        <td>{{ number_format($detail->price_room ?? 0, 0, ',', '.') }} đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
        
        <p>Trân trọng,<br>
        <strong>MyHotel Team</strong></p>
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động từ hệ thống MyHotel.</p>
        <p>Vui lòng không trả lời email này.</p>
    </div>
</body>
</html>

