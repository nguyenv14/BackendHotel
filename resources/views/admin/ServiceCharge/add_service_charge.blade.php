@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Phí Dịch Vụ
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
                <h4 style="margin-top: -15px" class="card-title">Thêm Phí Dịch Vụ</h4>
                <form id="form-service-charge" action="{{ 'save-service-charge' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    
                    <div class="form-group">
                        <label for="">Chọn Khách Sạn</label>
                        <select class="form-control" name="hotel_id">
                            @foreach ($hotels as $hotel)
                            <option value="{{ $hotel->hotel_id }}">{{ $hotel->hotel_name  }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Tính Năng Phí Dịch Vụ</label>
                        <select class="form-control" name="servicecharge_condition">
                            <option value="1">Tính Phí Theo %</option>
                            <option value="2">Tính Phí Theo Số Tiền</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Phí Dịch Vụ</label>
                        <input id="servicecharge_fee" type="text" name="servicecharge_fee" class="form-control"
                            placeholder="Phí Dịch Vụ">
                        <span class="text-danger form-message"></span>
                    </div>


                    <button type="submit" class="btn btn-gradient-primary me-2 form-service-charge-submit">Xác Nhận</button>
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
            form: '#form-service-charge',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#facilitiesroom_name', 'Vui lòng nhập tên tiện ích'),
                Validator.isRequired('#facilitiesroom_image', 'Vui lòng tải lên ảnh tiện ích'),
                Validator.isRequired('#facilitiesroom_desc', 'Vui lòng nhập mô tả tiện ích'),
            ]
        });

        $('.form-service-charge-submit').click(function() {
            if ($('#facilitiesroom_name').val() == '' || $('#facilitiesroom_image').val() == ''  || $('#facilitiesroom_desc').val() == '' || $('.form-message').text() != '') {
                    $("#form-service-charge").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
