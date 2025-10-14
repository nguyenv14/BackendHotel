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
                <form id="form-servicecharge" action="{{ 'update-service-charge' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $servicecharge_old['servicecharge_id'] }}" name="servicecharge_id">
                    {{-- <input type="hidden" value="{{ $servicecharge_old['hotel_id'] }}" name="hotel_id"> --}}
                    <div class="form-group">
                        <label for="">Tên Khách Sạn</label>
                        <input disabled id="" type="text" name="hotel_id" class="form-control"
                            placeholder="Tên Khách Sạn" value="{{ $servicecharge_old->hotel->hotel_name }}">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Tính Năng Phí Dịch Vụ</label>
                        <select class="form-control" name="servicecharge_condition">
                            @if($servicecharge_old['servicecharge_condition'] == 1)
                            <option value="1">Tính Phí Theo %</option>
                            <option value="2">Tính Phí Theo Số Tiền</option>
                            @else
                            <option value="2">Tính Phí Theo Số Tiền</option>
                            <option value="1">Tính Phí Theo %</option>
                            @endif
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Phí Dịch Vụ</label>
                        <input id="servicecharge_fee" type="text" name="servicecharge_fee" class="form-control"
                            placeholder="Phí Dịch Vụ" value="{{  $servicecharge_old['servicecharge_fee'] }}">
                        <span class="text-danger form-message"></span>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2 form-servicecharge-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#servicecharge_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
        <script>
            Validator({
            form: '#form-servicecharge',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#servicecharge_name', 'Vui lòng nhập tên Tiện Ích'),
                Validator.isRequired('#servicecharge_image', 'Vui lòng tải lên ảnh Tiện Ích'),
                Validator.isRequired('#servicecharge_desc', 'Vui lòng nhập mô tả Tiện Ích'),
            ]
        });

        $('.form-servicecharge-submit').click(function() {
           
            if ($('.form-message').text() != '') {
                    $("#form-servicecharge").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
