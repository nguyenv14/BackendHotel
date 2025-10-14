@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Khách Sạn
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
                <h4 style="margin-top: -15px" class="card-title">Thêm Nhà Hàng</h4>
                <form id="form-hotel" action="{{ 'save-restaurant' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">Tên Nhà Hàng</label>
                        <input id="name_hotel" type="text" name="restaurant_name" class="form-control" id=""
                               placeholder="Tên Khách Sạn" value="{{ old('name_hotel') }}">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Hạng Nhà Hàng</label>
                        <select class="form-control m-bot15" name="restaurant_rank">
                            <option value="1">1 Sao</option>
                            <option value="2">2 Sao</option>
                            <option value="3">3 Sao</option>
                            <option value="4">4 Sao</option>
                            <option value="5">5 Sao</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Khu Vực Nhà Hàng</label>
                        <select class="form-control m-bot15" name="area_id">
                            @foreach ($areas as $key => $area)
                                <option value="{{ $area->area_id }}">{{ $area->area_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Vị Trí Chi Tiết</label>
                        <input id="restaurant_placedetails" type="text" name="restaurant_placedetails" class="form-control"
                               placeholder="Vị Trí Chi Tiết" value="{{ old('restaurant_placedetails') }}">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Link Vị Trí Google Map</label>
                        <input id="restaurant_linkplace" type="text" name="restaurant_linkplace" class="form-control"
                               placeholder="Link Vị Trí Google Map" value="{{ old('restaurant_linkplace') }}">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="restaurant_image" class="form-label">Tải Ảnh Lên</label>
                        <input class="form-control"  id="image_restaurant" type="file"
                               name="restaurant_image">
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Nhà Hàng</label>
                        <textarea id="desc_hotel" rows="8" class="form-control" name="restaurant_desc"></textarea>
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Hiển thị</label>
                        <select class="form-control" name="restaurant_status">
                            <option value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2 form-hotel-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#hotel_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
    <script>
        Validator({
            form: '#form-hotel',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#name_hotel', 'Vui lòng nhập tên nhà hàng'),
                Validator.isRequired('#restaurant_image', 'Vui lòng tải lên ảnh nhà hàng'),
                Validator.isRequired('#desc_hotel', 'Vui lòng nhập mô tả nhà hàng'),
                Validator.isRequired('#tag_keyword_hotel', 'Vui lòng nhập mô tả nhà hàng'),
                Validator.isRequired('#restaurant_placedetails', 'Vui lòng nhập vào đây'),
                Validator.isRequired('#restaurant_linkplace', 'Vui lòng nhập vào đây'),
            ]
        });

        $('.form-hotel-submit').click(function() {
            if ($('#name_hotel').val() == '' || $('#img_hotel').val() == ''  || $('#desc_hotel').val() == ''  || $('#tag_keyword_hotel').val() == '' || $('.form-message').text() != '') {
                $("#form-hotel").submit(function(e) {
                    e.preventDefault();
                });
            }
        })
    </script>
@endsection
