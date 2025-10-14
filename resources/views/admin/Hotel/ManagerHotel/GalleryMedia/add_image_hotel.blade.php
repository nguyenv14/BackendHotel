@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Khách Sạn
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
                    <div class="card-title col-sm-9">Thư Viện Ảnh Khách Sạn {{ $hotel->hotel_name }}
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>

                <form action="{{ URL::to('admin/hotel/manager/insert-gallery') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel->hotel_id }}">
                    <input type="hidden" name="type" value="1">
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Thêm Ảnh Vào Thư Viện Ảnh</label>
                        <input class="form-control" type="file" name="file[]" id="formFile" accept="image/*" multiple>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

                <table style="margin-top:20px " class="table table-bordered tab-gallery">

                    <thead>
                        <tr>
                            <th> #STT </th>
                            <th>Tên Ảnh</th>
                            <th>Hình Ảnh</th>
                            <th>Nội Dung Ảnh</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="loading_gallery">

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    {!! $gallery->links('admin.pagination') !!}
    <!-- Toàn Bộ Script Liên Quan Đến Gallery  -->
    <script>
        $(document).ready(function() {
            /* Loading Gallrery On Table */
            var notePage = 1;
            load_gallery(notePage);
            $('.pagination a').unbind('click').on('click', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                notePage = page;
                load_gallery(page);
            });
            function load_gallery(page) {
                var hotel_id = {{ $hotel->hotel_id }};
                var type = $("input[name='type']").val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/loading-gallery?page=') }}' + page,
                    method: 'POST',
                    data: {
                        hotel_id: hotel_id,
                        type: type,
                    },
                    success: function(data) {
                        $('#loading_gallery').html(data);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            }
            /* Cập Nhật Tên Ảnh Gallery */
            $(document).on('blur', '.update_gallery_hotel_name', function() {
                var gallery_hotel_id = $(this).data('gallery_hotel_id');
                var gallery_name = $(this).text();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/update-nameimg-gallery') }}',
                    method: 'post',
                    data: {
                        gallery_id: gallery_hotel_id,
                        gallery_name: gallery_name,
                    },
                    success: function(data) {
                        message_toastr("success", "Tên Ảnh Đã Được Cập Nhật !");
                         load_gallery(notePage);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            /* Cập Nhật Nội Dung Ảnh Gallery */
            $(document).on('blur', '.edit_gallery_hotel_content', function() {
                var gallery_hotel_id = $(this).data('gallery_hotel_id');
                var gallery_content = $(this).text();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/update-content-gallery') }}',
                    method: 'POST',
                    data: {

                        gallery_id: gallery_hotel_id,
                        gallery_content: gallery_content,
                    },
                    success: function(data) {
                        message_toastr("success", "Nội Dung Ảnh Đã Được Cập Nhật !");
                         load_gallery(notePage);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });


            /* Xóa Gallery */
            $(document).on('click', '.delete_gallery_hotel', function() {
                var gallery_hotel_id = $(this).data('gallery_hotel_id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/delete-gallery') }}',
                    method: 'POST',
                    data: {
                        gallery_id: gallery_hotel_id,
                    },
                    success: function(data) {
                        if (data == 'true') {
                            message_toastr("success", "Ảnh Đã Được Xóa !");
                        }
                         load_gallery(notePage);

                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            $(document).on('change', '.up_load_file', function() {
                var gallery_hotel_id = $(this).data('gallery_hotel_id');
                var image = document.getElementById('up_load_file' + gallery_hotel_id).files[0];

                var form_data = new FormData();
                form_data.append("file", document.getElementById('up_load_file' + gallery_hotel_id).files[
                    0]);
                form_data.append("gallery_id", gallery_hotel_id);


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/update-image-gallery') }}',
                    method: 'post',
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        message_toastr("success", "Cập Nhật Ảnh Thành Công !");
                         load_gallery(notePage);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });
            });

            $('#formFile').change(function() {
                var error = '';
                var files = $('#formFile')[0].files;

                if (files.length > 30) {
                    error += 'Bạn Không Được Chọn Quá 30 Ảnh';

                } else if (files.length == '') {
                    error += 'Vui lòng chọn ảnh';

                } else if (files.size > 10000000) {
                    error += 'Ảnh Không Được Lớn Hơn 10Mb';
                }

                if (error == '') {

                } else {
                    $('#formFile').val('');
                    message_toastr("error", '' + error + '');
                    return false;
                }

            });

        });
    </script>
@endsection
