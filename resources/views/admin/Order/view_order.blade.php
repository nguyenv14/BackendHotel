@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-clipboard-outline"></i>
            </span> Quản Lý Đơn Hàng
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="mdi mdi-clipboard-outline"></i>
                    <span><?php
                    $today = date('d/m/Y');
                    echo $today;
                    ?></span>
                </li>
            </ul>
        </nav>
    </div>


    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                @if ($orderer['customer_id'] != null)
                    <div style="display: flex;justify-content: space-between">
                        <div class="card-title col-sm-9">Thông Tin Khách Hàng Đăng Nhập</div>
                        <div class="col-sm-3">
                        </div>
                    </div>
                    <table style="margin-top:20px " class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#ID Khách Hàng</th>
                                <th>Tên Khách Hàng</th>
                                <th>Số Điện Thoại</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <td>{{ $orderer->customer->customer_id }}</td>
                            <td>{{ $orderer->customer->customer_name }}</td>
                            <td>{{ $orderer->customer->customer_phone }}</td>
                            <td>{{ $orderer->customer->customer_email }}</td>
                        </tbody>
                    </table>
                @else
                    <div style="display: flex;justify-content: space-between">
                        <div class="card-title col-sm-9">Thông Tin Khách Hàng Liên Hệ</div>
                        <div class="col-sm-3">
                        </div>
                    </div>
                    <table style="margin-top:20px " class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên Khách Hàng</th>
                                <th>Số Điện Thoại</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <td>{{ $orderer['orderer_name'] }}</td>
                            <td>{{ $orderer['orderer_phone'] }}</td>
                            <td>{{ $orderer['orderer_email'] }}</td>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Yêu Cầu Người Dùng</div>
                    <div class="col-sm-3">
                    </div>
                </div>
                <table style="margin-top:20px " class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Loại Giường</th>
                            <th>Yêu Cầu Đặc Biệt</th>
                            <th>Yêu Cầu Riêng</th>
                            <th>Xuất Hóa Đơn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td>
                            @if ($orderer['orderer_type_bed'] == 1)
                                1 Giường Đơn
                            @elseif($orderer['orderer_type_bed'] == 2)
                                2 Giường Đơn
                            @endif
                        </td>
                        <td>
                            @if ($orderer['orderer_special_requirements'] == 0)
                                {{ 'Không' }}
                            @elseif($orderer['orderer_special_requirements'] == 1)
                                {{ 'Phòng không hút thuốc' }}
                            @elseif($orderer['orderer_special_requirements'] == 2)
                                {{ 'Phòng ở tầng cao' }}
                            @elseif($orderer['orderer_special_requirements'] == 3)
                                {{ 'Phòng không hút thuốc và trên cao' }}
                            @endif
                        </td>
                        <td>{{ $orderer['orderer_own_require'] }}</td>
                        <td>
                            @if ($orderer['orderer_bill_require'] == 1)
                                {{ 'Kèm Hóa Đơn' }}
                            @else
                                {{ 'Không' }}
                            @endif
                        </td>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Chi Tiết Đơn Hàng</div>
                    <div class="col-sm-3">
                    </div>
                </div>
                <table style="margin-top:20px " class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên Khách Sạn</th>
                            <th>Tên Phòng</th>
                            <th>Nhận Phòng</th>
                            <th>Trả Phòng</th>
                            <th>Giá Phòng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $orderdetails->hotel_name }} </td>
                            <td>{{ $orderdetails->room_name }}</td>
                            <td>{{ $orderdetails->order->start_day }}</td>
                            <td>{{ $orderdetails->order->end_day }}</td>
                            <td>{{ number_format($orderdetails->price_room, 0, ',', '.') }}đ</td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:20px ">
                    <tr>
                        <td>Phí Khách Sạn : {{ number_format($orderdetails->hotel_fee, 0, ',', '.') }}đ</td><br>
                        <td>Mã Giảm Giá : {{ $orderdetails->order->coupon_name_code }}</td><br>
                        <td>Số Tiền Giảm: {{ number_format( $orderdetails->order->coupon_sale_price, 0, ',', '.') }}đ</td><br>
                        <td>Tổng Thanh Toán: {{ number_format($orderdetails->price_room + $orderdetails->hotel_fee - $orderdetails->order->coupon_sale_price, 0, ',', '.') }}đ </td>
                    </tr>
                </div>

            </div>
        </div>
    </div>
    <div>
        <div class="template-demo">
            <a target="_blank" style="text-decoration: none">
                {{-- href="{{ URL::to('admin/order/print-order?checkout_code=' . $orderdetails->order_code) }}"> --}}
                <button type="button" class="btn btn-gradient-info btn-icon-text"> Xuất Hóa Đơn PDF <i
                        class="mdi mdi-printer btn-icon-append"></i>
                </button>
            </a>
            <a style="text-decoration: none" href="">
                <button type="button" class="btn btn-gradient-danger btn-icon-text">
                    <i class="mdi mdi-upload btn-icon-prepend"></i> Upload </button>
            </a>
            <a style="text-decoration: none" href="">
                <button type="button" class="btn btn-gradient-warning btn-icon-text">
                    <i class="mdi mdi-reload btn-icon-prepend"></i> Reset </button>
            </a>

        </div>
    </div>
@endsection
