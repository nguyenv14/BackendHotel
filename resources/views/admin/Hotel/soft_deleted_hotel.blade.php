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
                    <div class="card-title col-sm-9">Bảng Danh Sách Khách Sạn</div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input id="search" type="text" class="form-control" name="search"
                                placeholder="Tìm Kiếm ID Hoặc Tên Khách Sạn">
                            <button type="button" class="btn-md btn-inverse-success btn-icon">
                                <i class="mdi mdi-account-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <table style="margin-top:20px " class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#ID </th>
                            <th>Tên</th>
                            <th>Hạng</th>
                            <th>Loại</th>
                            <th>Thương Hiệu</th>
                            <th>Ảnh</th>
                            <th>Khu Vực</th>
                            <th>Ngày Xóa </th>
                            <th>Thao Tác </th>
                        </tr>
                    </thead>
                    <tbody id="loading-table-hotel">

                    </tbody>
                </table>
            </div>
        </div>
    </div>




    {!! $items->links('admin.pagination') !!}
    {{-- Phân Trang Bằng Ajax --}}
    <script>
        var notePage = 1;
        getPosts(notePage);
        $('.pagination a').unbind('click').on('click', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var notePage = page;
            getPosts(page);
        });

        function getPosts(page) {
            $.ajax({
                url: '{{ url('/admin/hotel/load-deleted-hotel?page=') }}' + page,
                method: 'get',
                data: {

                },
                success: function(data) {
                    $('#loading-table-hotel').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>

    <script>
        $('#search').keyup(function() {
            var key_sreach = $(this).val();
            $.ajax({
                url: '{{ url('/admin/hotel/search-bin') }}',
                method: 'GET',
                data: {
                    key_sreach: key_sreach,
                },
                success: function(data) {
                    $('#loading-table-hotel').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });

        $(document).on("click", ".btn-restore-item", function() {
            var item_id = $(this).data('item_id');
            message_toastr("success", 'Xác Khôi Phục Khách Sạn ID ' + item_id +
                '?<br/><br/><button type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw confirm-restore" data-item_id="' +
                item_id + '">Khôi Phục</button>');

        })
        
        $(document).on("click", ".confirm-restore", function() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });
            var item_id = $(this).data('item_id');
            setTimeout(restore_item(item_id), 1000);
        })

        function restore_item(item_id) {
            $.ajax({
                headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('/admin/hotel/restore-hotel') }}',
                method: 'POST',
                data: {
                    hotel_id: item_id,
                },
                success: function(data) {
                    $(".loading").css({
                        "display": "none"
                    });
                    $(".overlay-loading").css({
                        "display": "none"
                    });
                    message_toastr("success", "Khách Sạn Có ID " + item_id + " Đã Được Khôi Phục!");
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


        $(document).on("click", ".btn-delete-item", function() {
            var item_id = $(this).data('item_id');
            message_toastr("success", 'Xác Xóa Vĩnh Viễn Khách Sạn ID ' + item_id +
                '?<br/><br/><button type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw confirm-delete-bin" data-item_id="' +
                item_id + '">Xóa Vĩnh Viễn</button>');

        })

        $(document).on("click", ".confirm-delete-bin", function() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });
            var item_id = $(this).data('item_id');
            setTimeout(delete_trash_item(item_id), 1000);
        })

        function delete_trash_item(item_id) {
            $.ajax({
                headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('/admin/hotel/delete-trash-hotel') }}',
                method: 'POST',
                data: {
                    hotel_id: item_id,
                },
                success: function(data) {
                    $(".loading").css({
                        "display": "none"
                    });
                    $(".overlay-loading").css({
                        "display": "none"
                    });
                    message_toastr("success", "Khách Sạn Có ID " + item_id + " Đã Được Xóa Vĩnh Viển!");
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
