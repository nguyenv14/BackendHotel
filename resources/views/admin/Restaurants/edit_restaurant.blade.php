@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Nhà Hàng
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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Khách Sạn</h4>
                <form id="form-hotel" action="{{ 'update-restaurant' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $restaurant['restaurant_id'] }}" name="restaurant_id">
                    <div class="form-group">
                        <label for="">Tên Khách Sạn</label>
                        <input id="name_restaurant" type="text" name="restaurant_name"
                               value="{{ $restaurant['restaurant_name'] }}" class="form-control"
                               placeholder="Tên Khách Sạn">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label>Tải Ảnh Lên</label>
                        <div>
                            <img style="object-fit: cover; margin: 30px 0px 30px 0px" width="120px" height="120px"
                                 src="{{ URL::to('public/fontend/assets/img/restaurant/'.$restaurant->restaurant_image) }}"
                                 alt="">
                        </div>
                        <input id="hotel_image" type="file" name="hotel_image" class="file-upload-default">
                        <div class="form-group">
                            <input id="img_restaurant" class="form-control" type="file" name="restaurant_image">
                            <span class="text-danger form-message"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Hạng Khách Sạn</label>
                        <select class="form-control" name="restaurant_rank">
                            <option selected value="5">5 Sao</option>
                            <option value="4">4 Sao</option>
                            <option value="3">4 Sao</option>
                            <option value="2">4 Sao</option>
                            <option value="1">4 Sao</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Khu Vực Khách Sạn</label>
                        <select class="form-control" name="area_id">
                            @foreach ($areas as $v_areas)
                                @if($v_areas->area_id == $restaurant['area_id'])
                                    <option selected
                                            value="{{ $restaurant->area->area_id }}">{{ $restaurant->area->area_name  }}</option>
                                @else
                                    <option value="{{ $v_areas->area_id }}">{{ $v_areas->area_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="restaurant_status">
                            @if($restaurant->restaurant_status == 0)
                                <option active value="0">Ẩn</option>
                                <option value="1">Hiện</option>
                            @else
                                <option active value="1">Hiện</option>
                                <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Khách Sạn</label>
                        <textarea rows="8" class="form-control" name="restaurant_desc"
                                  id="desc_restaurant">{{ $restaurant['restaurant_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Địa Điểm Chi Tiết</label>
                        <input id="restaurant_placedetails" type="text" name="restaurant_placedetails"
                               value="{{ $restaurant['restaurant_placedetails'] }}" class="form-control" id=""
                               placeholder="Địa Điểm Chi Tiết">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Link Địa Điểm GG Map</label>
                        <input id="restaurant_linkplace" type="text" name="restaurant_linkplace"
                               value="{{ $restaurant['restaurant_linkplace'] }}" class="form-control" id=""
                               placeholder="Link GG Map Địa Điểm Chi Tiết">
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-restaurant-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#restaurant_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
    <script>
        Validator({
            form: '#form-restaurant',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#name_restaurant', 'Vui lòng nhập tên khách sạn'),
                Validator.isRequired('#img_restaurant', 'Vui lòng tải lên ảnh khách sạn'),
                Validator.isRequired('#desc_restaurant', 'Vui lòng nhập mô tả khách sạn'),
                Validator.isRequired('#restaurant_placedetails', 'Vui lòng Nhập Trường Này '),
                Validator.isRequired('#restaurant_linkplace', 'Vui lòng Nhập Trường Này ')
            ]
        });

        $('.form-hotel-submit').click(function () {

            if ($('.form-message').text() != '') {
                $("#form-hotel").submit(function (e) {
                    e.preventDefault();
                });
            }
        })
    </script>
@endsection
