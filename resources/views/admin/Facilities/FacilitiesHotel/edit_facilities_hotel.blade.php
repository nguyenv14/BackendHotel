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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Tiện Ích</h4>
                <form id="form-facilitieshotel" action="{{ 'update-hotel-facilities' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $facilitieshotel_old['facilitieshotel_id'] }}" name="facilitieshotel_id">

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
                        <input id="facilitieshotel_name" type="text" name="facilitieshotel_name" value="{{ $facilitieshotel_old['facilitieshotel_name'] }}" class="form-control" id="" placeholder="Tên Tiện Ích" >
                        <span class="text-danger form-message"></span>
                    </div>
            
                    <div class="form-group">
                        <label>Tải Ảnh Lên</label>
                        <div>
                            <img style="object-fit: cover; margin: 30px 0px 30px 0px" width="120px" height="120px"
                                src="{{ URL::to('public/fontend/assets/img/facilitieshotel/'.$facilitieshotel_old->facilitieshotel_image) }}"
                                alt="">
                        </div>
                        <input id="facilitieshotel_image" type="file" name="facilitieshotel_image" class="file-upload-default">
                        <div class="form-group">
                            <input id="facilitieshotel_image" class="form-control" type="file"  id="facilitieshotel_image" type="file" name="facilitieshotel_image" >
                            <span class="text-danger form-message"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="facilitieshotel_status">
                            @if($facilitieshotel_old['facilitieshotel_status'] == 0)
                            <option active value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                            @else
                            <option active value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Tiện Ích</label>
                        <textarea rows="8" class="form-control" name="facilitieshotel_desc" id="facilitieshotel_desc" >{{ $facilitieshotel_old['facilitieshotel_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-facilitieshotel-submit">Xác Nhận</button>
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
            form: '#form-facilitieshotel',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#facilitieshotel_name', 'Vui lòng nhập tên Tiện Ích'),
                Validator.isRequired('#facilitieshotel_image', 'Vui lòng tải lên ảnh Tiện Ích'),
                Validator.isRequired('#facilitieshotel_desc', 'Vui lòng nhập mô tả Tiện Ích'),
            ]
        });

        $('.form-facilitieshotel-submit').click(function() {
           
            if ($('.form-message').text() != '') {
                    $("#form-facilitieshotel").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
