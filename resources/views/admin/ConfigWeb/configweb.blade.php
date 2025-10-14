@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Cấu Hình Website
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


        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">



                    <form action="{{ URL::to('/admin/web/insert-config-image') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-8">
                                <label for="formFile" class="form-label">Thêm Ảnh Vào Thư Viện Ảnh</label>
                                <input class="form-control" type="file" name="file[]" id="formFile" accept="image/*"
                                    multiple>
                            </div>
                            <div class="mt-4 col-md-2">
                                <select name="config_type" id="" class="btn btn-outline-secondary">
                                    <option value="2">Slogan</option>
                                    <option value="1">Logo</option>
                                    <option value="3">Thương Hiệu</option>
                                </select>
                            </div>
                        </div>
                        <div>

                            <div class="row ml-2">
                                <button type="submit" class="btn btn-primary col-lg-3">Submit</button>

                            </div>
                        </div>
                    </form>



                </div>
            </div>
        </div>

        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex;justify-content: space-between">
                        <div class="card-title col-sm-9">Logo Trang Web
                        </div>
                        <div class="col-sm-3" style="display: flex;justify-content: center">
                        </div>
                    </div>
                    <div class="col-lg-7 logo-img">

                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">


                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Slogan trang web
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>


                <table style="margin-top:20px " class="table table-bordered tab-gallery">
                    <form>
                        <input type="hidden" value="" id="pro_id" name="pro_id">
                        @csrf
                        <thead>
                            <tr>
                                <th> #STT </th>
                                {{-- <th>Mã Cấu Hình</th> --}}
                                <th>Hình Ảnh</th>
                                <th>Tiêu đề</th>
                                <th>Nội Dung Ảnh</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="loading_gallery">

                        </tbody>
                    </form>
                </table>

            </div>
        </div>
    </div>


    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Thương Hiệu đồng hành
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>



                <table style="margin-top:20px " class="table table-bordered tab-gallery">
                    <form>
                        <input type="hidden" value="" id="pro_id" name="pro_id">
                        @csrf
                        <thead>
                            <tr>
                                <th> #STT </th>
                                {{-- <th>Mã Cấu Hình</th> --}}
                                <th>Hình Ảnh Thương Hiệu</th>
                                <th>Tên Thương Hiệu</th>
                                <th>Link Thương Hiệu</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="loading_brand">

                        </tbody>
                    </form>
                </table>

            </div>
        </div>
    </div>
    {{-- Toàn Bộ Script Liên Quan Đến Gallery --}}
    <script>
        $(document).ready(function() {
         
            loading_config_slogan();
            load_logo();
            loading_config_brand();

            function loading_config_slogan() {
                $.ajax({
                    url: '{{ url('admin/web/load-config-slogan') }}',
                    method: 'get',
                    success: function(data) {
                        $('#loading_gallery').html(data);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });
            }

            function loading_config_brand() {
                $.ajax({
                    url: '{{ url('admin/web/load-config-brand') }}',
                    method: 'get',
                    success: function(data) {
                        $('#loading_brand').html(data);
                    },
                    error: function(data) {
                        alert("Nguyên fix Fix Bug Huhu :<");
                    },
                });
            }

            function load_logo() {
                $.ajax({
                    url: '{{ url('admin/web/load-logo-config') }}',
                    method: 'get',
                    success: function(data) {
                        $('.logo-img').html(data)
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });
            }

            /* Cập Nhật Tiêu Đề */
            $('.tab-gallery').on('blur', '.edit_config_title', function() {
                var config_id = $(this).data('config_id');
                // var _token = $("input[name='_token']").val();
                var config_title = $(this).text();
             
                $.ajax({
                    url: '{{ url('admin/web/edit-config-title') }}',
                    method: 'POST',
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                    
                        config_id: config_id,
                        config_title: config_title,
                    },
                    success: function(data) {
                        message_toastr("success", "Tiêu Đề Đã Được Cập Nhật !");
                        loading_config_slogan();
                        loading_config_brand();
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            /* Cập Nhật Nội Dung Ảnh Gallery */
            $('.tab-gallery').on('blur', '.edit_config_content', function() {
                var config_id = $(this).data('config_id');
                // var _token = $("input[name='_token']").val();
                var config_content = $(this).text();
                // alert(config_id + config_content);
                $.ajax({
                    url: '{{ url('admin/web/edit-config-content') }}',
                    method: 'post',
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        config_id: config_id,
                        config_content: config_content,
                    },
                    success: function(data) {
                        message_toastr("success", "Nội Dung Ảnh Đã Được Cập Nhật !");
                        loading_config_slogan();
                        loading_config_brand();
                        
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });


            /* Xóa Gallery */
            $(document).on('click', '.delete_config_image', function() {
                var config_id = $(this).data('config_id');

                $.ajax({
                    url: '{{ url('admin/web/delete-config') }}',
                    method: 'post',
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        config_id: config_id,
                    },
                    success: function(data) {
                        message_toastr("success", "Ảnh Đã Được Xóa !");
                        loading_config_slogan();
                        loading_config_brand();
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });



            $(document).on('change', '.up_load_file', function() {
                var config_id = $(this).data('config_id');
                // var file = $("input[name='file_image']").val();
                var image = document.getElementById('up_load_file' + config_id).files[0];
                //var _token = $("input[name='_token']").val();
                // alert(config_id + " " + image);
                var form_data = new FormData();
                form_data.append("file", document.getElementById('up_load_file' + config_id).files[0]);
                form_data.append("config_id", config_id);

                $.ajax({
                    url: '{{ url('admin/web/update-image-config') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        message_toastr("success", "Cập Nhật Ảnh Thành Công !");
                        loading_config_slogan();
                        load_logo();
                        loading_config_brand();

                    },
                    error: function(data) {
                        alert("Fix Bug Huhu :<");
                    },
                });
            });


            $('.logo-img').on('change', '.up_load_file', function() {
                var config_id = $(this).data('config_id');
                var image = document.getElementById('up_load_file' + config_id).files[0];

                var form_data = new FormData();
                form_data.append("file", image);
                form_data.append("config_id", config_id);

                $.ajax({
                    url: '{{ url('admin/web/update-image-config') }}',
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        message_toastr("success", "Cập Nhật Ảnh Thành Công !");
                        loading_config_brand();
                        load_logo();
                        loading_config_brand();
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });
            });





            $('#formFile').change(function() {
                var error = '';
                var files = $('#formFile')[0].files;

                if (files.length > 20) {
                    error += 'Bạn Không Được Chọn Quá 20 Ảnh';

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
