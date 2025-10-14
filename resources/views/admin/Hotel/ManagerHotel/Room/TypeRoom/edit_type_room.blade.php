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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Phòng</h4>
                <form id="form-typeroom" action="{{ 'update-typeroom' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $hotel['hotel_id'] }}" name="hotel_id">
                    <input type="hidden" value="{{ $typeroom_old['room_id'] }}" name="room_id">
                    <input type="hidden" value="{{ $typeroom_old['type_room_id'] }}" name="type_room_id">

                    <div class="form-group">
                        <label for="">Loại Giường</label>
                        <select class="form-control" name="type_room_bed">
                            @if($typeroom_old['type_room_bed'] == 1)
                            <option active value="1">1 Giường Đơn</option>
                            <option value="2">2 Giường Đơn</option>
                            <option value="3">1 Giường Đơn Hoặc 2 Giường Đơn</option>
                            @elseif($typeroom_old['type_room_bed'] == 2)
                            <option active value="2">2 Giường Đơn</option>
                            <option value="1">1 Giường Đơn</option>
                            <option value="3">1 Giường Đơn Hoặc 2 Giường Đơn</option>
                            @else
                            <option active value="3">1 Giường Đơn Hoặc 2 Giường Đơn</option>
                            <option value="2">2 Giường Đơn</option>
                            <option value="1">1 Giường Đơn</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Giá Phòng</label>
                        <input id="type_room_price" type="text" name="type_room_price" class="form-control"
                            placeholder="Giá Phòng" value="{{ $typeroom_old['type_room_price']  }}">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Giảm Giá Phòng</label>
                        <select id="type_room_condition" class="form-control" name="type_room_condition">
                            @if($typeroom_old['type_room_condition'] == 0)
                            <option active value="0">Không Giảm Giá</option>
                            <option value="1">Giảm Theo %</option>
                            @else
                            <option active value="1">Giảm Theo %</option>
                            <option value="0">Không Giảm Giá</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group" id="price_sale">
                        <label for="">Nhập % Giảm Giá</label>
                        <input id="type_room_price_sale" type="text" name="type_room_price_sale" class="form-control"
                            placeholder="Nhập % Giảm Giá" value="{{ $typeroom_old['type_room_price_sale'] }}">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Nhập Số Lượng Loại Phòng</label>
                        <input id="type_room_quantity" type="text" name="type_room_quantity" class="form-control"
                            placeholder="Nhập Số Lượng Loại Phòng" value="{{ $typeroom_old['type_room_quantity'] }}">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="room_status">
                            @if($typeroom_old['type_room_status'] == 0)
                            <option active value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                            @else
                            <option active value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                  
                    <button type="submit" class="btn btn-gradient-primary me-2 form-typeroom-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
        <script>
            Validator({
            form: '#form-typeroom',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#type_room_price', 'Vui lòng nhập vào giá'),
                Validator.isRequired('#type_room_price_sale', 'Vui lòng nhập vào % giá'),
                Validator.isRequired('#type_room_quantity', 'Vui lòng nhập số lượng loại phòng'),
            ]
        });
        $('.form-typeroom-submit').click(function() {
            if ($('.form-message').text() != '') {
                    $("#form-typeroom").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
        if( $('#type_room_condition').val() == 0){
            $("#price_sale").hide();
        }else{
            $("#price_sale").show();
        }
        $('#type_room_condition').change(function() {
            if ($(this).val() == 0) {
                document.getElementById('type_room_price_sale').value = '';
                $("#price_sale").hide();

            } else {
                $("#price_sale").show();
            }
        })
    </script>
@endsection
