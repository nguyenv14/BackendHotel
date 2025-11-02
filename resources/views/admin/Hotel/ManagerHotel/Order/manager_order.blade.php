@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Đơn Đặt Phòng
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="mdi mdi-timetable"></i>
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
                <form method="GET" action="{{ url('admin/hotel/manager/order/search-order') }}"
                    class="d-flex flex-nowrap align-items-center gap-2 mb-3">
                    @if (isset($hotel) && $hotel)
                        <input type="hidden" name="hotel_id" value="{{ $hotel->hotel_id }}">
                    @endif
                    <div class="card-title col-sm-6">Bảng Danh Sách Đơn Đặt Phòng</div>
                    <div class="col-sm-2">
                        <div class="btn-group">
                            <button type="button" class="btn btn-gradient-info dropdown-toggle"
                                data-bs-toggle="dropdown">Sắp Xếp Theo</button>
                            <div class="dropdown-menu">
                                <span class="dropdown-item sort-order" data-type="0">Tất Cả</span>
                                <span class="dropdown-item sort-order" data-type="1">Đang Chờ Duyệt</span>
                                <span class="dropdown-item sort-order" data-type="2">Đơn Phòng Bị Từ Chối</span>
                                <span class="dropdown-item sort-order" data-type="3">Khách Hủy Đơn</span>
                                <span class="dropdown-item sort-order" data-type="4">Hoàn Thành Đơn Phòng</span>
                                <span class="dropdown-item sort-order" data-type="5">Đã Thanh Toán</span>
                                <span class="dropdown-item sort-order" data-type="6">Chưa Thanh Toán</span>
                                <span class="dropdown-item sort-order" data-type="7">Thanh Toán Khi Nhận Phòng</span>
                                <span class="dropdown-item sort-order" data-type="8">Thanh Toán Momo</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <input id="search" type="text" class="form-control" name="search"
                                placeholder="Tìm Kiếm Mã Đặt Phòng">
                            <button type="button" class="btn-md btn-inverse-success btn-icon">
                                <i class="mdi mdi-account-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-2" style="margin-left: 30px">
                        @hasanyroles(['admin','manager'])
                        <div class="input-group">
                            <a style="text-decoration: none"
                                href="{{ URL::to('admin/hotel/manager/order/list-deleted-order' . ($hotel ? '?hotel_id=' . $hotel->hotel_id : '')) }}">
                                <button id="bin" type="button" class="btn btn-gradient-danger btn-icon-text">
                                </button>
                            </a>
                        </div>
                        @endhasanyroles
                    </div>
                </form>

                <table style="margin-top:20px " class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#Mã Đặt Phòng </th>
                            <th>Ngày Nhận Phòng</th>
                            <th>Ngày Trả Phòng</th>
                            <th>Trạng Thái</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái TT</th>
                            <th>Ngày Tạo Đơn</th>
                            <th> Thao Tác </th>
                        </tr>
                    </thead>
                    <tbody id="load_order">
                        @foreach ($items as $order)
                            <tr>
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->start_day }}</td>
                                <td>{{ $order->end_day }}</td>

                                {{-- Trạng thái đơn hàng --}}
                                <td>
                                    @if ($order->order_status == 0)
                                        <span class="text-info"><b>Đang Chờ Duyệt</b></span>
                                    @elseif($order->order_status == -1)
                                        <span class="text-danger"><b>Đơn Phòng Bị Từ Chối</b></span>
                                    @elseif($order->order_status == -2)
                                        <span class="text-danger"><b>Khách Hàng Hủy Đơn</b></span>
                                    @elseif(in_array($order->order_status, [1, 2]))
                                        <span class="text-warning"><b>Hoàn Thành Đơn Phòng</b></span>
                                    @endif
                                </td>

                                {{-- Phương thức thanh toán --}}
                                <td>
                                    @if ($order->payment)
                                        @if ($order->payment->payment_method == 4)
                                            Khi Nhận Phòng
                                        @elseif($order->payment->payment_method == 1)
                                            Thanh Toán Momo
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>

                                {{-- Trạng thái thanh toán --}}
                                <td>
                                    @if ($order->payment)
                                        @if ($order->payment->payment_status == 0)
                                            Chưa Thanh Toán
                                        @elseif($order->payment->payment_status == 1)
                                            Đã Thanh Toán
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>{{ $order->created_at }}</td>

                                {{-- Hành động --}}
                                <td>
                                    @if ($order->order_status == 0)
                                        <button style="margin-top:10px"
                                            class="btn-sm btn-gradient-success btn-rounded btn-fw btn-order-status"
                                            data-order_code="{{ $order->order_code }}" data-order_status="1">
                                            Duyệt Đơn <i class="mdi mdi-calendar-check"></i>
                                        </button>
                                        <br>
                                        <button style="margin-top:10px"
                                            class="btn-sm btn-gradient-danger btn-fw btn-order-status"
                                            data-order_code="{{ $order->order_code }}" data-order_status="-1">
                                            Từ Chối <i class="mdi mdi-calendar-remove"></i>
                                        </button>
                                        <br>
                                    @endif

                                    @if (in_array($order->order_status, [-1, 1, 2, -2]))
                                        @hasanyroles(['admin', 'manager'])
                                        <button type="button"
                                            class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2"
                                            data-item_id="{{ $order->order_id }}">
                                            <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa Đơn
                                        </button>
                                        <br>
                                        @endhasanyroles
                                    @endif

                                    <a
                                        href="{{ url('admin/hotel/manager/order/view-order?order_id=' . $order->order_id) }}">
                                        <button style="margin-top:10px" class="btn-sm btn-gradient-info btn-rounded btn-fw">
                                            Xem Đơn <i class="mdi mdi-eye"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    {{-- Phân Trang Bằng Paginate + Boostraps , Apply view Boostrap trong Provider --}}
    <nav aria-label="Page navigation example">
        {!! $items->links('admin.pagination') !!}
    </nav>
    {{-- Phân Trang Bằng Ajax --}}
    <script>
        var hotelId = {{ $hotel->hotel_id ?? 'null' }};
        var notePage = 1;
        // getPosts(notePage);
        // load_count_bin();
        // $('.pagination a').unbind('click').on('click', function(e) {
        //     e.preventDefault();
        //     var page = $(this).attr('href').split('page=')[1];
        //     notePage = page;
        //     getPosts(page);
        // });

        // function getPosts(page) {
        //     var url = '{{ url('admin/hotel/manager/order/load-order?page=') }}' + page;
        //     if (hotelId) {
        //         url += '&hotel_id=' + hotelId;
        //     }
        //     $.ajax({
        //         url: url,
        //         method: 'get',
        //         data: {

        //         },
        //         success: function(data) {
        //             $('#load_order').html(data);
        //         },
        //         error: function() {
        //             alert("Bug Huhu :<<");
        //         }
        //     })
        // }

        function load_count_bin() {
            var url = '{{ url('admin/hotel/manager/order/count-bin') }}';
            if (hotelId) {
                url += '?hotel_id=' + hotelId;
            }
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    if (data == 0) {
                        $('#bin').html('<i class="mdi mdi-delete-sweep btn-icon-prepend"></i> Thùng Rác');
                    } else {
                        $('#bin').html(
                            '<i class="mdi mdi-delete-sweep btn-icon-prepend"></i> Thùng Rác <div style="width: 20px;height: 20px;background-color:red;display: flex;justify-content: center;align-items: center;position: absolute;border-radius: 10px;right: 10%;top:10%"><b>' +
                            data + '</b></div>');
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>

    <script>
        $(document).on('click', '.btn-order-status', function() {
            var order_code = $(this).data('order_code');
            var order_status = $(this).data('order_status');
            var _token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{ url('admin/ /update-status-order') }}',
                method: 'GET',
                data: {
                    _token: _token,
                    order_code: order_code,
                    order_status: order_status,
                },
                success: function(data) {
                    getPosts(notePage);
                    if (data == "refuse") {
                        message_toastr("success", 'Mã ' + order_code + ' Đã Bị Từ Chối !');
                    } else if (data == "browser") {
                        message_toastr("success", 'Mã ' + order_code + ' Đã Được Duyệt !');
                    }
                },
                error: function(data) {
                    alert("Nhân Ơi Fix Bug Huhu :<");
                },
            });

        });

        // $('#search').keyup(function() {
        //     var key_sreach = $(this).val();
        //     var url = '{{ url('admin/hotel/manager/order/search-order') }}';
        //     var data = {
        //         key_sreach: key_sreach
        //     };
        //     if (hotelId) {
        //         data.hotel_id = hotelId;
        //     }
        //     $.ajax({
        //         url: url,
        //         method: 'GET',
        //         data: data,
        //         success: function(data) {
        //             $('#load_order').html(data);
        //         },
        //         error: function() {
        //             alert("Bug Huhu :<<");
        //         }
        //     })
        // });

        $(document).on('click', '.btn-delete-item', function() {
            var item_id = $(this).data('item_id');
            message_toastr("success", 'Xác Nhận Xóa Đơn Đặt Phòng ' +
                '?<br/><br/><button type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw confirm-delete" data-item_id="' +
                item_id + '">Xóa</button>');

        });

        $(document).on('click', '.confirm-delete', function() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });
            var item_id = $(this).data('item_id');
            setTimeout(move_to_bin(item_id), 1000);
        });

        function move_to_bin(item_id) {
            var data = {
                order_id: item_id
            };
            if (hotelId) {
                data.hotel_id = hotelId;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('admin/hotel/manager/order/delete-order') }}',
                method: 'POST',
                data: data,
                success: function(data) {
                    $(".loading").css({
                        "display": "none"
                    });
                    $(".overlay-loading").css({
                        "display": "none"
                    });
                    load_count_bin();
                    getPosts(notePage);
                    message_toastr("success", 'Đơn Đặt Phòng Đã Được Đưa Vào Thùng Rác !');
                },
                error: function() {
                    $(".loading").css({
                        "display": "none"
                    });
                    $(".overlay-loading").css({
                        "display": "none"
                    });
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>

    <script>
        $('.sort-order').click(function() {
            var type = $(this).data('type');
            var data = {
                type: type
            };
            if (hotelId) {
                data.hotel_id = hotelId;
            }
            $.ajax({
                url: '{{ url('admin/hotel/manager/order/sort-order') }}',
                method: 'get',
                data: data,
                success: function(data) {
                    $('#load_order').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        })
    </script>
@endsection
