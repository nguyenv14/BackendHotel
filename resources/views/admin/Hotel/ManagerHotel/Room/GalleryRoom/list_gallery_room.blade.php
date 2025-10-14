@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span>Quản Lý Thư Viện Ảnh
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
    <div class="row">


        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <form action="{{ URL::to('admin/hotel/manager/room/insert-gallery') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <label for="formFile" class="form-label">Thêm Ảnh Vào Thư Viện Ảnh</label>
                                <input class="form-control" type="file" name="file[]" id="formFile" accept="image/*"
                                    multiple>
                            </div>
                            <div class="col-md-4 mt-4">
                                <select name="room_id" id="room_id" class="btn btn-outline-secondary">
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->room_id }}">{{ $room->room_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3 ms-1">
                            <button type="submit" class="btn btn-primary col-md-2">Submit</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>


    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body" id="loading_gallery">


            </div>
        </div>
    </div>

    <script>
        var notePage = 1;
        $(document).ready(function() {
            /* Loading Gallrery On Table */

            var room_id = $('#room_id').val();

            load_gallery(notePage, room_id);
            // $('.pagination a').unbind('click').on('click', function(e) {
            //     e.preventDefault();
            //     var page = $(this).attr('href').split('page=')[1];
            //     notePage = page;
            //     load_gallery(page);
            // });

            $('#room_id').change(function() {
                var room_id = $(this).val();
                load_gallery(notePage, room_id);
            })


            /* Cập Nhật Tên Ảnh Gallery */
            $(document).on('blur', '.update_gallery_room_name', function() {
                var room_id = $('#room_id').val();
                var gallery_room_id = $(this).data('gallery_room_id');
                var gallery_name = $(this).text();


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/room/update-nameimg-gallery') }}',
                    method: 'post',
                    data: {
                        gallery_id: gallery_room_id,
                        gallery_name: gallery_name,
                    },
                    success: function(data) {
                        message_toastr("success", "Tên Ảnh Đã Được Cập Nhật !");
                        load_gallery(notePage, room_id);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            /* Cập Nhật Nội Dung Ảnh Gallery */
            $(document).on('blur', '.edit_gallery_room_content', function() {
                var room_id = $('#room_id').val();
                var gallery_room_id = $(this).data('gallery_room_id');
                var gallery_content = $(this).text();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/room/update-content-gallery') }}',
                    method: 'POST',
                    data: {
                        gallery_id: gallery_room_id,
                        gallery_content: gallery_content,
                    },
                    success: function(data) {
                        message_toastr("success", "Nội Dung Ảnh Đã Được Cập Nhật !");
                        load_gallery(notePage,room_id);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            /* Xóa Gallery */
            $(document).on('click', '.delete_gallery_room', function() {
                var room_id = $('#room_id').val();
                var gallery_room_id = $(this).data('gallery_room_id');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/room/delete-gallery') }}',
                    method: 'POST',
                    data: {
                        gallery_id: gallery_room_id,
                    },
                    success: function(data) {
                        if (data == 'true') {
                            message_toastr("success", "Ảnh Đã Được Xóa !");
                            load_gallery(notePage, room_id);
                        }
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            $(document).on('change', '.up_load_file', function() {
                var room_id = $('#room_id').val();
                var gallery_room_id = $(this).data('gallery_room_id');
                var image = document.getElementById('up_load_file' + gallery_room_id).files[0];

                var form_data = new FormData();
                form_data.append("file", document.getElementById('up_load_file' + gallery_room_id).files[0]);
                form_data.append("gallery_id", gallery_room_id);


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/room/update-image-gallery') }}',
                    method: 'post',
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        message_toastr("success", "Cập Nhật Ảnh Thành Công !");
                         load_gallery(notePage,room_id);
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

            function load_gallery(page, room_id) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/hotel/manager/room/loading-gallery?page=') }}' + page,
                    method: 'GET',
                    data: {
                        room_id: room_id,
                    },
                    success: function(data) {
                        $('#loading_gallery').html(data);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            }
        })
    </script>
@endsection
