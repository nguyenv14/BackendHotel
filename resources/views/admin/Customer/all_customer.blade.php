@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-book-variant"></i>
            </span> Quản Lý Khách Hàng
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
                    <div class="card-title col-sm-4">Bảng Danh Sách Khách Hàng</div>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <input id="search" type="text" class="form-control" name="search"
                                placeholder="Tìm Kiếm Tên Người Dùng Hoặc Email">
                            <button type="button" class="btn-md btn-inverse-success btn-icon">
                                <i class="mdi mdi-account-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="btn-group">
                            <button type="button" class="btn btn-gradient-info dropdown-toggle"
                                data-bs-toggle="dropdown">Theo Loại Tài Khoản</button>
                            <div class="dropdown-menu">
                                <span class="dropdown-item" data-type="0">Tất Cả</span>
                                <span class="dropdown-item" data-type="1">Hệ Thống</span>
                                <span class="dropdown-item" data-type="2">Facebook</span>
                                <span class="dropdown-item" data-type="3">Google</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        @hasanyroles(['admin','manager'])
                        <div class="input-group">
                            <a style="text-decoration: none"
                                href="{{ URL::to('admin/customer/list-soft-deleted-customer') }}">
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
                            <th> #ID </th>
                            <th> Tên Khách Hàng </th>
                            <th> Số Điện Thoại </th>
                            <th> Email </th>
                            @hasanyroles(['admin','manager'])
                            <th> Mật Khẩu </th>
                            @endhasanyroles
                            <th> Tài Khoản </th>
                            @hasanyroles(['admin','manager'])
                            <th> Trạng Thái </th>
                            <th> IP </th>
                            {{-- <th> Vị Trí </th> --}}
                            <th> Thiết Bị </th>
                            <th> Thao Tác </th>
                            @endhasanyroles
                        </tr>
                    </thead>
                    <tbody id="loading-table-customers">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Phân Trang Bằng Paginate + Boostraps , Apply view Boostrap trong Provider --}}
    <nav aria-label="Page navigation example">
        {!! $customers->links('admin.pagination') !!}
    </nav>

        {{-- Phân Trang Bằng Ajax --}}
        <script>
            var notePage = 1;
            $('.pagination a').unbind('click').on('click', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                notePage = page;
                getPosts(page);
            });
    
            function getPosts(page) {
                $.ajax({
                    url: '{{ url('/admin/customer/load-all-customer?page=') }}' + page,
                    method: 'get',
                    data: {
    
                    },
                    success: function(data) {
                        $('#loading-table-customers').html(data);
                    },
                    error: function() {
                        alert("Bug Huhu :<<");
                    }
                })
            }
        </script>

    <script>
        load_customers();
        load_count_bin();

        function load_customers() {
            $.ajax({
                url: '{{ url('/admin/customer/load-all-customer') }}',
                method: 'GET',
                success: function(data) {
                    $('#loading-table-customers').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
        function load_count_bin(){
            $.ajax({
                url: '{{ url('/admin/customer/count-bin') }}',
                method: 'GET',
                success: function(data) {
                    if(data == 0){
                        $('#bin').html('<i class="mdi mdi-delete-sweep btn-icon-prepend"></i> Thùng Rác');
                    }else{
                        $('#bin').html('<i class="mdi mdi-delete-sweep btn-icon-prepend"></i> Thùng Rác <div style="width: 20px;height: 20px;background-color:red;display: flex;justify-content: center;align-items: center;position: absolute;border-radius: 10px;right: 10%;top:10%"><b>'+data+'</b></div>');
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }

        $('#search').keyup(function() {

            var key_sreach = $(this).val();

            $.ajax({
                url: '{{ url('/admin/customer/all-customer-sreach') }}',
                method: 'GET',
                data: {
                    key_sreach: key_sreach,
                },
                success: function(data) {
                    $('#loading-table-customers').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });

        $('.dropdown-item').click(function() {
            var type = $(this).data('type');
            $.ajax({
                url: '{{ url('/admin/customer/sort-customer-by-type') }}',
                method: 'GET',
                data: {
                    type: type,
                },
                success: function(data) {
                    $('#loading-table-customers').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });

        $(document).on('click', '.update-status', function() {
            var customer_id = $(this).data('customer_id');
            var customer_status = $(this).data('customer_status');

            $.ajax({
                url: '{{ url('/admin/customer/update-status-customer') }}',
                method: 'GET',
                data: {
                    customer_id: customer_id,
                    customer_status: customer_status,
                },
                success: function(data) {
                    getPosts(notePage);
                    if (customer_status == 1) {
                        message_toastr("success", "Tài Khoản Đã Được Kích Hoạt!");
                    } else if (customer_status == 0) {
                        message_toastr("success", "Tài Khoản Đã Bị Khóa!");
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });

        $(document).on('click', '.btn-delete-customer', function() {
            var customer_id = $(this).data('customer_id');
            message_toastr("success", 'Xác Nhận Xóa Tài Khoản ID ' + customer_id +
                '?<br/><br/><button type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw confirm-delete" data-customer_id="' +
                customer_id + '">Xóa</button>');

        });

        $(document).on('click', '.confirm-delete', function() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });
            var customer_id = $(this).data('customer_id');
            setTimeout(delete_customer(customer_id), 1000);
        });

        function delete_customer(customer_id) {
            $.ajax({
                url: '{{ url('/admin/customer/delete-customer') }}',
                method: 'GET',
                data: {
                    customer_id: customer_id,
                },
                success: function(data) {
                    $(".loading").css({
                        "display": "none"
                    });
                    $(".overlay-loading").css({
                        "display": "none"
                    });
                    getPosts(notePage);
                    load_count_bin();
                    message_toastr("success", 'Tài Khoản ID ' + customer_id + ' Đã Được Đưa Vào Thùng Rác !');
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
