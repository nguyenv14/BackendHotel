@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Tiện Ích
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
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 style="margin-top: -15px" class="card-title">Thêm Tiện Ích</h4>
                <form id="form-room-facilities" action="{{ 'save-room-facilities' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">Tên Tiện Ích</label>
                        <input id="facilitiesroom_name" type="text" name="facilitiesroom_name" class="form-control" id=""
                            placeholder="Tên Tiện Ích">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="facilitiesroom_image" class="form-label">Tải Ảnh Lên</label>
                        <input id="facilitiesroom_image" class="form-control" type="file" id="facilitiesroom_image" type="file"
                            name="facilitiesroom_image">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="facilitiesroom_status">
                            <option value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Tiện Ích</label>
                        <textarea id="facilitiesroom_desc" rows="8" class="form-control" name="facilitiesroom_desc"></textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-room-facilities-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#facilitiesroom_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
    <script>
        Validator({
            form: '#form-room-facilities',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#facilitiesroom_name', 'Vui lòng nhập tên tiện ích'),
                Validator.isRequired('#facilitiesroom_image', 'Vui lòng tải lên ảnh tiện ích'),
                Validator.isRequired('#facilitiesroom_desc', 'Vui lòng nhập mô tả tiện ích'),
            ]
        });

        $('.form-room-facilities-submit').click(function() {
            if ($('#facilitiesroom_name').val() == '' || $('#facilitiesroom_image').val() == ''  || $('#facilitiesroom_desc').val() == '' || $('.form-message').text() != '') {
                    $("#form-room-facilities").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
