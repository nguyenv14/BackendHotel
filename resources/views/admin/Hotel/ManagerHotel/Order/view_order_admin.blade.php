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
                    <div class="table-responsive">
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
                    </div>
                @else
                    <div style="display: flex;justify-content: space-between">
                        <div class="card-title col-sm-9">Thông Tin Khách Hàng Liên Hệ</div>
                        <div class="col-sm-3">
                        </div>
                    </div>
                    <div class="table-responsive">
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
                    </div>
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
                <div class="table-responsive">
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
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Chi Tiết Đơn Hàng</div>
                    <div class="col-sm-3">
                    </div>
                </div>
                <div class="table-responsive">
                    <table style="margin-top:20px " class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên Khách Sạn</th>
                                <th>Tên Phòng</th>
                                <th>Loại Giường</th>
                                <th>Nhận Phòng</th>
                                <th>Trả Phòng</th>
                                <th>Giá Phòng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $orderdetails->hotel_name }} </td>
                                <td>{{ $orderdetails->room_name }}</td>
                                <td>
                                    @if(isset($typeRoom) && $typeRoom)
                                        @if($typeRoom->type_room_bed == 1)
                                            1 Giường Đơn
                                        @elseif($typeRoom->type_room_bed == 2)
                                            2 Giường Đơn
                                        @else
                                            {{ $typeRoom->type_room_bed ?? 'N/A' }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $orderdetails->order->start_day }}</td>
                                <td>{{ $orderdetails->order->end_day }}</td>
                                <td>{{ number_format($orderdetails->price_room, 0, ',', '.') }}đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:20px ">
                    <tr>
                        <td>Phí Khách Sạn : {{ number_format($orderdetails->hotel_fee, 0, ',', '.') }}đ</td><br>
                        <td>Mã Giảm Giá : {{ $orderdetails->order->coupon_name_code }}</td><br>
                        <td>Số Tiền Giảm: {{ number_format($orderdetails->order->coupon_sale_price, 0, ',', '.') }}đ</td>
                        <br>
                        <td>Tổng Thanh Toán: {{ number_format($orderdetails->order->total_price, 0, ',', '.') }}đ </td>
                    </tr>
                </div>

                {{-- Hiển thị trạng thái đơn hàng --}}
                @if (isset($order_full))
                    <div style="margin-top:20px; padding: 15px; background-color: #f0f0f0; border-radius: 5px;">
                        <strong>Trạng Thái Đơn Hàng:</strong>
                        @if ($order_full->order_status == \App\Http\Enums\OrderStatus::WAITING_FOR_APPROVAL)
                            <span class="badge badge-info">Đang Chờ Duyệt</span>
                        @elseif($order_full->order_status == \App\Http\Enums\OrderStatus::CHECK_IN)
                            <span class="badge badge-primary">Check-in</span>
                        @elseif($order_full->order_status == \App\Http\Enums\OrderStatus::CHECK_OUT)
                            <span class="badge badge-warning">Check-out</span>
                        @elseif($order_full->order_status == \App\Http\Enums\OrderStatus::COMPLETED)
                            <span class="badge badge-success">Đã Hoàn Thành</span>
                        @elseif($order_full->order_status == \App\Http\Enums\OrderStatus::NO_SHOW)
                            <span class="badge badge-danger">No Show</span>
                        @elseif($order_full->order_status == \App\Http\Enums\OrderStatus::CANCELLED_BY_ADMIN)
                            <span class="badge badge-danger">Đã Hủy (Admin)</span>
                        @elseif($order_full->order_status == \App\Http\Enums\OrderStatus::CANCELLED_BY_CUSTOMER)
                            <span class="badge badge-danger">Đã Hủy (Khách Hàng)</span>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
    <div style="margin-top: 20px;">
        <div class="template-demo" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            {{-- Form Duyệt Đơn (khi đang chờ duyệt) --}}
            @if (isset($order_full) && $order_full->order_status == \App\Http\Enums\OrderStatus::WAITING_FOR_APPROVAL)
                <form method="GET" action="{{ url('admin/order/update-status-order') }}"
                    style="display: inline-block; margin-right: 10px;">
                    <input type="hidden" name="order_code" value="{{ $orderdetails->order_code }}">
                    <input type="hidden" name="order_status"
                        value="{{ \App\Http\Enums\OrderStatus::WAITING_FOR_APPROVAL }}">
                    <button type="submit" class="btn btn-gradient-success btn-icon-text">
                        <i class="mdi mdi-check-circle btn-icon-prepend"></i> Duyệt Đơn
                    </button>
                </form>
            @endif

            {{-- Form Check-in (khi status = CHECK_IN) --}}
            @if (isset($order_full) && $order_full->order_status == \App\Http\Enums\OrderStatus::CHECK_IN)
                <form method="POST" action="{{ url('admin/order/admin-checkin-order') }}"
                    style="display: inline-block; margin-right: 10px;">
                    @csrf
                    <input type="hidden" name="order_code" value="{{ $orderdetails->order_code }}">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="text" name="checkin_code" class="form-control" placeholder="Nhập mã check-in"
                            required style="border-radius: 4px 0 0 4px;">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-gradient-success" style="border-radius: 0 4px 4px 0;">
                                <i class="mdi mdi-check-circle"></i> Check-in
                            </button>
                        </div>
                    </div>
                </form>
            @endif

            {{-- Form Check-out (khi status = CHECK_OUT) --}}
            @if (isset($order_full) && $order_full->order_status == \App\Http\Enums\OrderStatus::CHECK_OUT)
                <form method="POST" action="{{ url('admin/order/admin-checkout-order') }}"
                    style="display: inline-block; margin-right: 10px;">
                    @csrf
                    <input type="hidden" name="order_code" value="{{ $orderdetails->order_code }}">
                    <button type="submit" class="btn btn-gradient-warning btn-icon-text">
                        <i class="mdi mdi-logout btn-icon-prepend"></i> Check-out
                    </button>
                </form>
            @endif

            <a target="_blank" style="text-decoration: none; margin-left: auto;">
                {{-- href="{{ URL::to('admin/order/print-order?checkout_code=' . $orderdetails->order_code) }}"> --}}
                <button type="button" class="btn btn-gradient-info btn-icon-text">
                    <i class="mdi mdi-printer btn-icon-prepend"></i> Xuất Hóa Đơn PDF
                </button>
            </a>
        </div>
    </div>

    {{-- Hiển thị flash message --}}
    @if (session('success'))
        <script>
            $(document).ready(function() {
                message_toastr("success", "{{ session('success') }}");
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            $(document).ready(function() {
                message_toastr("error", "{{ session('error') }}");
            });
        </script>
    @endif

    @if (session('info'))
        <script>
            $(document).ready(function() {
                message_toastr("info", "{{ session('info') }}");
            });
        </script>
    @endif

@endsection
