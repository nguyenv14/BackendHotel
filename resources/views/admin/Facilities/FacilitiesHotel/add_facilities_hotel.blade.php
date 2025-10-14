@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Tiện Ích Khách Sạn
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
                <h4 style="margin-top: -15px" class="card-title">Thêm Tiện Ích Khách Sạn</h4>
                <form id="form-hotel-facilities" action="{{ 'save-hotel-facilities' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">Nhóm Tiện Ích</label>
                        <select class="form-control" name="facilitieshotel_group">
                            <option value="1">Hình thức thanh toán</option>
                            <option value="2">Phương tiện đi lại</option>
                            <option value="3">Thể thao</option>
                            <option value="4">Dịch vụ lễ tân</option>
                            <option value="5">Internet</option>
                            <option value="6">Thư giãn và làm đẹp</option>
                            <option value="7">Dịch vụ lau dọn</option>
                            <option value="8">Hỗ trợ người khuyết tật</option>
                            <option value="9">Dịch vụ nhà hàng</option>
                            <option value="10">Dịch vụ cho doanh nhân</option>
                            <option value="11">Tiện ích tổng quát cho khách sạn</option>
                            <option value="12">Dịch vụ hồ bơi và chăm sóc sức khoẻ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Tên Tiện Ích</label>
                        <input id="facilitieshotel_name" type="text" name="facilitieshotel_name" class="form-control" id=""
                            placeholder="Tên Tiện Ích">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="facilitieshotel_image" class="form-label">Tải Ảnh Lên</label>
                        <input id="facilitieshotel_image" class="form-control" type="file" id="facilitieshotel_image" type="file"
                            name="facilitieshotel_image">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="facilitieshotel_status">
                            <option value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Tiện Ích</label>
                        <textarea id="facilitieshotel_desc" rows="8" class="form-control" name="facilitieshotel_desc"></textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-hotel-facilities-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#facilitieshotel_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
    <script>
        Validator({
            form: '#form-hotel-facilities',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#facilitieshotel_name', 'Vui lòng nhập tên tiện ích'),
                Validator.isRequired('#facilitieshotel_image', 'Vui lòng tải lên ảnh tiện ích'),
                Validator.isRequired('#facilitieshotel_desc', 'Vui lòng nhập mô tả tiện ích'),
            ]
        });

        $('.form-hotel-facilities-submit').click(function() {
            if ($('#facilitieshotel_name').val() == '' || $('#facilitieshotel_image').val() == ''  || $('#facilitieshotel_desc').val() == '' || $('.form-message').text() != '') {
                    $("#form-hotel-facilities").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
