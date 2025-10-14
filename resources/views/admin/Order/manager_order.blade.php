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
                <div style="display: flex;justify-content: space-between">
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
                            <a style="text-decoration: none" href="{{ URL::to('admin/order/list-deleted-order') }}">
                                <button id="bin" type="button" class="btn btn-gradient-danger btn-icon-text">
                                </button>

                            </a>
                        </div>
                        @endhasanyroles
                    </div>
                </div>

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
        var notePage = 1;
        getPosts(notePage);
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
        $(document).on('click', '.btn-order-status', function() {
            var order_code = $(this).data('order_code');
            var order_status = $(this).data('order_status');
            var _token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{ url('admin/order/update-status-order') }}',
                method: 'GET',
                data: {
                    _token: _token,
                    order_code: order_code,
                    order_status: order_status,
                },
                success: function(data) {
                    getPosts(notePage);
                    if (data == "refuse") {
                        message_toastr("success", 'Mã '+order_code+' Đã Bị Từ Chối !');
                    } else if (data == "browser") {
                        message_toastr("success", 'Mã '+order_code+' Đã Được Duyệt !');
                    }
                },
                error: function(data) {
                    alert("Nhân Ơi Fix Bug Huhu :<");
                },
            });

        });

        $('#search').keyup(function() {
            var key_sreach = $(this).val();
            $.ajax({
                url: '{{ url('admin/order/search-order') }}',
                method: 'GET',
                data: {
                    key_sreach: key_sreach,
                },
                success: function(data) {
                    $('#load_order').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });

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
        $('.sort-order').click(function(){
            var type = $(this).data('type');
            $.ajax({
                url: '{{ url('admin/order/sort-order') }}',
                method: 'get',
                data: {
                    type:type,
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
