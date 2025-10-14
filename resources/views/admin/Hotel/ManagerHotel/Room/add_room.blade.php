@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Phòng
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
                <h4 style="margin-top: -15px" class="card-title">Thêm Phòng Cho Khách Sạn {{  $hotel->hotel_name }}</h4>
                <form id="form-room" action="{{ 'save-room' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="hotel_id" value="{{ $hotel->hotel_id }}">
                    <div class="form-group">
                        <label for="">Tên Phòng</label>
                        <input id="name_room" type="text" name="room_name" class="form-control"
                            placeholder="Tên Phòng">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Số Người</label>
                        <select class="form-control" name="room_amount_of_people">
                            <option value="1">1 Người</option>
                            <option value="2">2 Người</option>
                            <option value="3">3 Người</option>
                            <option value="4">4 Người</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Diện Tích</label>
                        <input id="acreage_room" type="text" name="room_acreage" class="form-control"
                            placeholder="Diện Tích">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Hướng Phòng</label>
                        <select class="form-control" name="room_view">
                            <option value="Hướng Sông">Hướng Sông</option>
                            <option value="Hướng Sông">Hướng Vườn</option>
                            <option value="Hướng Thành Phố">Hướng Thành Phố</option>
                            <option value="Hướng Thành Phố Và Sông">Hướng Thành Phố Và Sông</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="room_status">
                            <option value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2 form-room-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#room_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
    <script>
        Validator({
            form: '#form-room',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#name_room', 'Vui lòng nhập tên phòng'),
                Validator.isRequired('#acreage_room', 'Vui lòng nhập diện tích phòng'),
            ]
        });

        $('.form-room-submit').click(function() {
            if ($('#name_room').val() == '' || $('#acreage_room').val() == '' || $('.form-message').text() != '') {
                    $("#form-room").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
