@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Thùng Rác
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
                    <div class="card-title col-sm-9">Bảng Danh Sách Khách Hàng</div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input id="search" type="text" class="form-control" name="search"
                                placeholder="Tìm Kiếm Tên Người Dùng Hoặc Email">
                            <button type="button" class="btn-md btn-inverse-success btn-icon">
                                <i class="mdi mdi-account-search"></i>
                            </button>
                        </div>
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
                            <th> Ngày Xóa </th>
                            @hasanyroles(['admin','manager'])
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

    <nav aria-label="Page navigation example">
        {!! $customers->links('admin.pagination') !!}
    </nav>
      {{-- Phân Trang Bằng Ajax --}}
      <script>
        var notePage = 1;
        $('.pagination a').unbind('click').on('click', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var notePage = page;
            getPosts(page);
        });

        function getPosts(page) {
            $.ajax({
                url: '{{ url('/admin/customer/load-list-soft-deleted?page=') }}' + page,
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

        function load_customers() {
            $.ajax({
                url: '{{ url('/admin/customer/load-list-soft-deleted') }}',
                method: 'GET',
                success: function(data) {
                    $('#loading-table-customers').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }

        $('#search').keyup(function() {
          var key_sreach = $(this).val();
          $.ajax({
              url: '{{ url('/admin/customer/search-bin') }}',
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

        $(document).on("click", ".btn-restore-item", function() {
           
            var customer_id = $(this).data('customer_id');
            message_toastr("success", 'Xác Khôi Phục Tài Khoản ID ' + customer_id +
                '?<br/><br/><button type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw confirm-restore" data-customer_id="' +
                customer_id + '">Khôi Phục</button>');
              
        })
        $(document).on("click", ".confirm-restore", function() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });
            var customer_id = $(this).data('customer_id');
            setTimeout(restore_customer(customer_id), 1000);
        })

        $(document).on("click", ".btn-delete-item", function() {
            var customer_id = $(this).data('customer_id');
            message_toastr("success", 'Xác Xóa Vĩnh Viễn Tài Khoản ID ' + customer_id +
                '?<br/><br/><button type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw confirm-delete-bin" data-customer_id="' +
                customer_id + '">Xóa Vĩnh Viễn</button>');
           
        })

        $(document).on("click", ".confirm-delete-bin", function() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });
            var customer_id = $(this).data('customer_id');
            setTimeout(delete_trash_customer(customer_id), 1000);
        })

        function restore_customer(customer_id) {
            $.ajax({
                url: '{{ url('/admin/customer/restore-customer') }}',
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
                    message_toastr("success", "Tài Khoản Có ID " + customer_id + " Đã Được Khôi Phục!");
                    getPosts(notePage);
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

        function delete_trash_customer(customer_id) {
            $.ajax({
                url: '{{ url('/admin/customer/delete-trash-customer') }}',
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
                    message_toastr("success", "Tài Khoản Có ID " + customer_id + " Đã Được Xóa Vĩnh Viển!");
                    getPosts(notePage);
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
