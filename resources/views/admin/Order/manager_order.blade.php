{{-- 
    DEPRECATED: File này không còn được sử dụng
    Đã di chuyển sang: resources/views/admin/Hotel/ManagerHotel/Order/manager_order.blade.php
    Giữ lại để backup
--}}
@extends('admin.admin_layout')
@section('admin_content')
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
                <div class="card-title flex-grow-1">Bảng Danh Sách Đơn Đặt Phòng</div>
                <form method="GET" action="{{ url('admin/hotel/manager/order/search-order') }}"
                    class="d-flex flex-nowrap align-items-center gap-2 mb-3">
                    <!-- Search Input -->
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <input id="search" type="text" class="form-control" name="search"
                            placeholder="Nhập mã đặt phòng..." />
                    </div>

                    <!-- Dropdown 1: Trạng Thái Đơn Hàng -->
                    <div class="dropdown min-w-120">
                        <button
                            class="btn btn-outline-success dropdown-toggle w-120 text-start d-flex justify-content-between align-items-center shadow-sm"
                            type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="statusLabel" class="fw-medium">Trạng thái</span>
                        </button>
                        <ul class="dropdown-menu w-120 py-2" aria-labelledby="statusDropdown">
                            <li class="px-3 mb-1">
                                <small class="text-uppercase text-muted fw-bold">Trạng thái đơn</small>
                            </li>
                            <li><a class="dropdown-item ps-4" data-value="0" href="#">Tất cả</a></li>
                            <li><a class="dropdown-item ps-4" data-value="1" href="#">Đang chờ duyệt</a></li>
                            <li><a class="dropdown-item ps-4" data-value="2" href="#">Đơn phòng bị từ chối</a></li>
                            <li><a class="dropdown-item ps-4" data-value="3" href="#">Khách hủy đơn</a></li>
                            <li><a class="dropdown-item ps-4" data-value="4" href="#">Hoàn thành đơn phòng</a></li>
                            <li><a class="dropdown-item ps-4" data-value="5" href="#">Đã thanh toán</a></li>
                            <li><a class="dropdown-item ps-4" data-value="6" href="#">Chưa thanh toán</a></li>
                        </ul>
                        <input type="hidden" name="status" id="sortTypeInput" value="{{ request('status') }}">
                    </div>

                    <!-- Dropdown 2: Phương Thức Thanh Toán -->
                    <div class="dropdown min-w-120">
                        <button
                            class="btn btn-outline-info dropdown-toggle w-120 text-start d-flex justify-content-between align-items-center shadow-sm"
                            type="button" id="paymentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="paymentLabel" class="fw-medium">Thanh toán</span>
                        </button>
                        <ul class="dropdown-menu w-120 py-2" aria-labelledby="paymentDropdown">
                            <li class="px-3 mb-1">
                                <small class="text-uppercase text-muted fw-bold">Phương thức</small>
                            </li>
                            <li><a class="dropdown-item ps-4" data-value="0" href="#">Tất cả</a></li>
                            <li><a class="dropdown-item ps-4" data-value="7" href="#">Thanh toán khi nhận phòng</a>
                            </li>
                            <li><a class="dropdown-item ps-4" data-value="8" href="#">Thanh toán Momo</a></li>
                        </ul>
                        <input type="hidden" name="payment" id="sortTypeInput" value="{{ request('payment') }}">
                    </div>

                    <div class="dropdown min-w-150">
                        <button
                            class="btn btn-outline-primary dropdown-toggle w-200 text-start d-flex justify-content-between align-items-center shadow-sm"
                            type="button" id="hotelDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="hotelLabel" class="fw-medium">Khách sạn</span>
                        </button>
                        <ul class="dropdown-menu w-200 py-2" aria-labelledby="hotelDropdown" id="hotelDropdownMenu"
                            style="max-height: 300px; overflow-y: auto;">
                            <li class="px-3 mb-1">
                                <small class="text-uppercase text-muted fw-bold">Lọc theo khách sạn</small>
                            </li>
                            <li><a class="dropdown-item ps-4" data-hotel-id="0" href="#">Tất cả</a></li>
                        </ul>
                        <input type="hidden" name="hotel_id" id="hotelIdInput" value="0">
                    </div>

                    <!-- Nút Tìm -->
                    <button id="btnSearch" class="btn btn-outline-success" type="button" title="Tìm kiếm">
                        <i class="mdi mdi-magnify"></i> Tìm
                    </button>

                    <!-- Nút Xóa -->
                    <button id="btnClear" class="btn btn-outline-secondary" type="button" title="Xóa tìm kiếm">
                        <i class="mdi mdi-close"></i> Xóa tìm kiếm
                    </button>

                    <!-- Nút Thùng rác (chỉ admin/manager) -->
                    {{-- @hasanyroles(['admin','manager'])
                    <a href="{{ URL::to('admin/order/list-deleted-order') }}" class="btn btn-gradient-danger"
                        title="Xem đơn đã xóa">
                        <i class="mdi mdi-delete"></i> Thùng rác
                    </a>
                    @endhasanyroles --}}
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
                                        <button style="margin-top:10px"
                                            class="btn-sm btn-gradient-info btn-rounded btn-fw">
                                            Xem Đơn <i class="mdi mdi-eye"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="loading" style="display:none; text-align:center; padding:10px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p>Đang xử lý...</p>
                </div>
            </div>
        </div>
    </div>
    <style>
        .min-w-150 {
            min-width: 150px !important;
        }

        .w-150 {
            width: 150px !important;
        }
    </style>
    {{-- Phân Trang Bằng Paginate + Boostraps , Apply view Boostrap trong Provider --}}
    <nav aria-label="Page navigation example">
        {!! $items->links('admin.pagination') !!}
    </nav>
    {{-- Phân Trang Bằng Ajax --}}
    <script>
        var notePage = 1;
        getPosts(notePage);
        getHotels()
        load_count_bin();
        $('.pagination a').unbind('click').on('click', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            notePage = page;
            getPosts(page);
        });

        function getPosts(page) {
            $.ajax({
                url: '{{ url('admin/order/load-order?page=') }}' + page,
                method: 'get',
                data: {

                },
                success: function(data) {
                    $('#load_order').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }

        function getHotels() {
            $.ajax({
                url: '{{ url('admin/order/get-hotels') }}',
                method: 'GET',
                success: function(hotels) {
                    const menu = document.getElementById('hotelDropdownMenu');
                    // Giữ lại 2 phần tử đầu: tiêu đề + "Tất cả"
                    while (menu.children.length > 2) {
                        menu.removeChild(menu.lastChild);
                    }

                    // Thêm từng khách sạn
                    hotels.forEach(hotel => {
                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        a.href = '#';
                        a.className = 'dropdown-item ps-4';
                        a.setAttribute('data-hotel-id', hotel.hotel_id);
                        a.textContent = hotel.hotel_name;
                        li.appendChild(a);
                        menu.appendChild(li);
                    });

                    // Gắn sự kiện click
                    attachHotelEvents();
                },
                error: function() {
                    console.error("Không thể tải danh sách khách sạn.");
                }
            });
        }

        function attachHotelEvents() {
            const items = document.querySelectorAll('#hotelDropdownMenu .dropdown-item[data-hotel-id]');
            const label = document.getElementById('hotelLabel');
            const input = document.getElementById('hotelIdInput');

            items.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-hotel-id');
                    const name = this.textContent.trim();

                    label.textContent = name;
                    input.value = id;

                    // Highlight active
                    items.forEach(el => el.classList.remove('active', 'bg-primary', 'text-white'));
                    this.classList.add('active', 'bg-primary', 'text-white');
                });
            });
        }

        function load_count_bin() {
            $.ajax({
                url: '{{ url('admin/order/count-bin') }}',
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
        // === BIẾN TOÀN CỤC CHO FILTER ===
        let currentStatusValue = '0';
        let currentPaymentValue = '0';
        let currentHotelId = '0'; // (tuỳ chọn)
        document.addEventListener('DOMContentLoaded', function() {
            // === TRẠNG THÁI ĐƠN HÀNG ===
            const statusItems = document.querySelectorAll('#statusDropdown ~ .dropdown-menu .dropdown-item');
            const statusLabel = document.getElementById('statusLabel');

            if (statusItems.length > 0) {
                const firstItem = statusItems[0];
                currentStatusValue = firstItem.getAttribute('data-value'); // ✅ không dùng let
                statusLabel.textContent = firstItem.textContent.trim();
                firstItem.classList.add('active', 'bg-success', 'text-white');
            }

            statusItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const value = this.getAttribute('data-value');
                    const text = this.textContent.trim();
                    currentStatusValue = value; // ✅ gán vào biến toàn cục
                    statusLabel.textContent = text;

                    statusItems.forEach(el => el.classList.remove('active', 'bg-success',
                        'text-white'));
                    this.classList.add('active', 'bg-success', 'text-white');
                });
            });

            // === PHƯƠNG THỨC THANH TOÁN ===
            const paymentItems = document.querySelectorAll('#paymentDropdown ~ .dropdown-menu .dropdown-item');
            const paymentLabel = document.getElementById('paymentLabel');

            if (paymentItems.length > 0) {
                const firstItem = paymentItems[0];
                currentPaymentValue = firstItem.getAttribute('data-value'); // ✅ không dùng let
                paymentLabel.textContent = firstItem.textContent.trim();
                firstItem.classList.add('active', 'bg-info', 'text-white');
            }

            paymentItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const value = this.getAttribute('data-value');
                    const text = this.textContent.trim();
                    currentPaymentValue = value; // ✅ gán vào biến toàn cục
                    paymentLabel.textContent = text;

                    paymentItems.forEach(el => el.classList.remove('active', 'bg-info',
                        'text-white'));
                    this.classList.add('active', 'bg-info', 'text-white');
                });
            });

            document.getElementById('btnSearch').addEventListener('click', function() {
                const searchTerm = document.getElementById('search').value.trim();
                const hotelId = document.getElementById('hotelIdInput').value;

                console.log('Tìm kiếm:', {
                    searchTerm,
                    status: currentStatusValue,
                    payment: currentPaymentValue,
                    hotel_id: hotelId
                });

                document.getElementById('load_order').innerHTML = '';
                document.getElementById('loading').style.display = 'block';

                $.ajax({
                    url: '{{ url('admin/order/search-order') }}',
                    method: 'GET',
                    data: {
                        key_sreach: searchTerm,
                        status: currentStatusValue,
                        payment: currentPaymentValue,
                        hotel_id: hotelId
                    },
                    success: function(data) {
                        $('#load_order').html(data);
                    },
                    error: function() {
                        alert("Bug Huhu :<<");
                    },
                    complete: function() {
                        document.getElementById('loading').style.display = 'none';
                    }
                });
            });

            // === XÓA TÌM KIẾM ===
            document.getElementById('btnClear').addEventListener('click', function() {
                document.getElementById('search').value = '';
                document.getElementById('hotelLabel').textContent = 'Khách sạn';
                document.getElementById('hotelIdInput').value = '0';

                currentStatusValue = '0';
                currentPaymentValue = '0';

                statusLabel.textContent = 'Trạng thái';
                paymentLabel.textContent = 'Thanh toán';

                statusItems.forEach(el => el.classList.remove('active', 'bg-success', 'text-white'));
                paymentItems.forEach(el => el.classList.remove('active', 'bg-info', 'text-white'));
                if (statusItems[0]) statusItems[0].classList.add('active', 'bg-success', 'text-white');
                if (paymentItems[0]) paymentItems[0].classList.add('active', 'bg-info', 'text-white');

                // Reset hotel dropdown
                const hotelItems = document.querySelectorAll('#hotelDropdownMenu .dropdown-item');
                hotelItems.forEach((el, i) => {
                    if (i === 0) {
                        el.classList.add('active', 'bg-primary', 'text-white');
                    } else {
                        el.classList.remove('active', 'bg-primary', 'text-white');
                    }
                });
            });
        });
        // === TÌM KIẾM ===

        // $(document).on('click', '.btn-order-status', function() {
        //     var order_code = $(this).data('order_code');
        //     var order_status = $(this).data('order_status');
        //     var _token = $('meta[name="csrf-token"]').attr('content');

        //     $.ajax({
        //         url: '{{ url('admin/order/update-status-order') }}',
        //         method: 'GET',
        //         data: {
        //             _token: _token,
        //             order_code: order_code,
        //             order_status: order_status,
        //         },
        //         success: function(data) {
        //             getPosts(notePage);
        //             if (data == "refuse") {
        //                 message_toastr("success", 'Mã ' + order_code + ' Đã Bị Từ Chối !');
        //             } else if (data == "browser") {
        //                 message_toastr("success", 'Mã ' + order_code + ' Đã Được Duyệt !');
        //             }
        //         },
        //         error: function(data) {
        //             alert("Nhân Ơi Fix Bug Huhu :<");
        //         },
        //     });

        // });

        // $('#search').keyup(function() {
        //     var key_sreach = $(this).val();
        // $.ajax({
        //     url: '{{ url('admin/order/search-order') }}',
        //     method: 'GET',
        //     data: {
        //         key_sreach: key_sreach,
        //     },
        //     success: function(data) {
        //         $('#load_order').html(data);
        //     },
        //     error: function() {
        //         alert("Bug Huhu :<<");
        //     }
        // })
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
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('admin/order/delete-order') }}',
                method: 'POST',
                data: {
                    order_id: item_id,
                },
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
            $.ajax({
                url: '{{ url('admin/order/sort-order') }}',
                method: 'get',
                data: {
                    type: type,
                },
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
