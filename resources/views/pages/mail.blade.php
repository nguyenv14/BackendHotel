<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
        MyhoTel xin chào {{ $customer_name }}, Yêu cầu đặt hàng của bạn đã được ghi nhận.
    <br>
        <h3>Thông Tin Người Đặt Phòng</h3>
        <span>Tên Khách Hàng: {{ $customer_name  }}</span>
        <span>Email Của Bạn: {{ $customer_email  }}</span>
        <span>Số Điện Thoại: {{ $customer_phone  }}</span>
    <div>
        <span>  Mã Đặt Phòng : <span style="color: red; font-weight: bold">{{ $order_details['order_code'] }}</span></span> <br>
        <span> Tên Khách Sạn: {{ $order_details['hotel_name'] }}</span> <br>
        <span>  Tên Phòng: {{ $order_details['room_name'] }}</span> <br>
        <span>  Giá Phòng: {{ number_format($order_details['price_room'],0,',','.0') }} đ</span> <br>
        <?php
            if($order_details['coupon_name_code'] == 'Không Có'){
        ?>
        <?php
            }else{
        ?>
            <span>Mã Giảm Giá: {{ $order_details['coupon_price_sale'] }}</span> <br>
        <?php
            }
        ?>
      <span>  Phí Dịch Vụ: {{ number_format($coupon_price_sale,0,',','.') }} đ</span> <br>
        <h2>  Tổng Thanh Toán: {{ number_format( $total_price,0,',','.') }} đ</h2>
    </div>
    <br>
</body>
</html>
