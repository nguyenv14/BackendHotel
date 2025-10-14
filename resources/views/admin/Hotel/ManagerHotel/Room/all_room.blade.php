@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Phòng
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
                    <div class="card-title col-sm-6">Bảng Danh Sách Phòng Khách Sạn {{ $hotel->hotel_name }}</div>
                    <div class="col-sm-3">
                        <input id="hotel_id" type="hidden" name="hotel_id" value="{{ $hotel->hotel_id }}">
                        <div class="input-group">
                            <input id="search" type="text" class="form-control" name="search"
                                placeholder="Tìm Kiếm ID Hoặc Tên Phòng">
                            <button type="button" class="btn-md btn-inverse-success btn-icon">
                                <i class="mdi mdi-account-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        @hasanyroles(['admin','manager'])
                        <div class="input-group">
                            <a style="text-decoration: none" href="{{ URL::to('admin/hotel/manager/room/list-deleted-room?hotel_id='.$hotel->hotel_id) }}">
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
                            <th>#ID </th>
                            <th>Tên Phòng</th>
                            <th>Số Người</th>
                            <th>Diện Tích</th>
                            <th>Tầm Nhìn</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="load_room">

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
        var hotel_id = $("input[name='hotel_id']").val();
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
                url: '{{ url('admin/hotel/manager/room/load-room?page=') }}' + page,
                method: 'get',
                data: {
                    hotel_id:hotel_id,
                },
                success: function(data) {
                    $('#load_room').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }

        function load_count_bin() {
            $.ajax({
                url: '{{ url('admin/hotel/manager/room/count-bin') }}',
                method: 'GET',
                data: {
                    hotel_id:hotel_id,
                },
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
        $('#search').keyup(function() {
            var key_search = $(this).val();
            $.ajax({
                url: '{{ url('admin/hotel/manager/room/search-room') }}',
                method: 'GET',
                data: {
                    hotel_id:hotel_id,
                    key_search: key_search,
                },
                success: function(data) {
                    $('#load_room').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });
        $(document).on('click', '.update-status', function() {
            var item_id = $(this).data('item_id');
            var item_status = $(this).data('item_status');

            $.ajax({
                headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('admin/hotel/manager/room/update-status-room') }}',
                method: 'POST',
                data: {
                   room_id: item_id,
                   room_status: item_status,
                },
                success: function(data) {
                    getPosts(notePage);
                    if (item_status == 1) {
                        message_toastr("success", 'Phòng ID ' + item_id + ' Đã Được Kích Hoạt!');
                    } else if (item_status == 0) {
                        message_toastr("success", 'Phòng ID ' + item_id + ' Đã Bị Vô Hiệu!');
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });

        $(document).on('click', '.btn-delete-item', function() {
            var item_id = $(this).data('item_id');
            message_toastr("success", 'Xác Nhận Xóa Phòng ID ' + item_id +
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
                headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('admin/hotel/manager/room/delete-room') }}',
                method: 'POST',
                data: {
                   room_id: item_id,
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
                    message_toastr("success", 'Phòng ID ' + item_id + ' Đã Được Đưa Vào Thùng Rác !');
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
@endsection
