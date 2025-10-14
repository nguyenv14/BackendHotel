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
                <h4 style="margin-top: -15px" class="card-title">Thêm Lựu Chọn Phòng Cho Khách Sạn {{ $hotel->hotel_name }}
                </h4>
                <form id="form-type-room" action="{{ 'save-typeroom' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="hotel_id" value="{{  $hotel->hotel_id }}">
                    <div class="form-group">
                        <label for="">Chọn Phòng</label>
                        <select class="form-control" name="room_id">
                            @foreach ($rooms as $room)
                                <option value="{{ $room->room_id }}">{{ $room->room_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Loại Giường</label>
                        <select class="form-control" name="type_room_bed">
                            <option value="1">1 Giường Đơn</option>
                            <option value="2">2 Giường Đơn</option>
                            <option value="3">1 Giường Đơn Hoặc 2 Giường Đơn</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Giá Phòng</label>
                        <input id="type_room_price" type="text" name="type_room_price" class="form-control"
                            placeholder="Giá Phòng">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Giảm Giá Phòng</label>
                        <select id="type_room_condition" class="form-control" name="type_room_condition">
                            <option value="0">Không Giảm Giá</option>
                            <option value="1">Giảm Theo %</option>
                        </select>
                    </div>

                    <div class="form-group" id="price_sale">
                        <label for="">Nhập % Giảm Giá</label>
                        <input id="type_room_price_sale" type="text" name="type_room_price_sale" class="form-control"
                            placeholder="Nhập % Giảm Giá">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Nhập Số Lượng Loại Phòng</label>
                        <input id="type_room_quantity" type="text" name="type_room_quantity" class="form-control"
                            placeholder="Nhập Số Lượng Loại Phòng">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="type_room_status">
                            <option value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2 form-typeroom-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        Validator({
            form: '#form-type-room',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#type_room_price', 'Vui lòng nhập vào giá'),
                Validator.isRequired('#type_room_price_sale', 'Vui lòng nhập vào % giá'),
                Validator.isRequired('#type_room_quantity', 'Vui lòng nhập số lượng loại phòng'),
            ]
        });

        $('.form-typeroom-submit').click(function() {
            if ($('#type_room_price').val() == '' || $('.form-message').text() != '') {
                $("#form-type-room").submit(function(e) {
                    e.preventDefault();
                });
            }
            // alert($('#type_room_price').val());
            // alert($('#type_room_price_sale').val());
            // alert($('.form-message').text());
        })

        $("#price_sale").hide();
        $('#type_room_condition').change(function() {
            if ($(this).val() == 0) {
                $("#price_sale").hide();
                document.getElementById('type_room_price_sale').value = '';
            } else {
                $("#price_sale").show();
            }
        })
    </script>
@endsection
