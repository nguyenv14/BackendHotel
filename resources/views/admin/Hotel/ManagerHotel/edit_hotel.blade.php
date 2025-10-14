@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Khách Sạn</h4>
                <form id="form-hotel" action="{{ 'update-hotel' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $hotel['hotel_id'] }}" name="hotel_id">
                    <div class="form-group">
                        <label for="">Tên Khách Sạn</label>
                        <input id="name_hotel" type="text" name="hotel_name" value="{{ $hotel['hotel_name'] }}" class="form-control" id="" placeholder="Tên Khách Sạn" >
                        <span class="text-danger form-message"></span>
                    </div>
            
                    <div class="form-group">
                        <label>Tải Ảnh Lên</label>
                        <div>
                            <img style="object-fit: cover; margin: 30px 0px 30px 0px" width="120px" height="120px"
                                src="{{ URL::to('public/fontend/assets/img/hotel/'.$hotel->hotel_image) }}"
                                alt="">
                        </div>
                        <input id="hotel_image" type="file" name="hotel_image" class="file-upload-default">
                        <div class="form-group">
                            <input id="img_hotel" class="form-control" type="file"  id="hotel_image" type="file" name="hotel_image" >
                            <span class="text-danger form-message"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Hạng Khách Sạn</label>
                        <select class="form-control" name="hotel_rank">
                            <option selected value="5">5 Sao</option>
                            <option value="4">4 Sao</option>
                            <option value="3">4 Sao</option>
                            <option value="2">4 Sao</option>
                            <option value="1">4 Sao</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Loại Khách Sạn</label>
                        <select class="form-control" name="hotel_type">
                            @if ($hotel->hotel_type == 1)
                                <option  value="1">Khách Sạn</option>
                                <option  value="2">Khách Sạn Căn Hộ</option>
                                <option value="3">Khu Nghỉ Dưỡng</option>
                            @elseif($hotel->hotel_type == 2)
                                <option value="2">Khách Sạn Căn Hộ</option>
                                <option value="1">Khách Sạn</option>
                                <option value="3">Khu Nghỉ Dưỡng</option>
                            @elseif($hotel->hotel_type == 3)
                                <option value="3">Khu Nghỉ Dưỡng</option>
                                <option value="2">Khách Sạn Căn Hộ</option>
                                <option value="1">Khách Sạn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Thương Hiệu Khách Sạn</label>
                        <select class="form-control" name="brand_id">
                            @foreach ($brands as $v_brands)
                                @if($v_brands->brand_id == $hotel['brand_id'])
                                    <option selected value="{{ $hotel->brand->brand_id }}">{{ $hotel->brand->brand_name  }}</option>
                                @else
                                    <option value="{{ $v_brands->brand_id }}">{{ $v_brands->brand_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Khu Vực Khách Sạn</label>
                        <select class="form-control" name="area_id">
                            @foreach ($areas as $v_areas)
                                @if($v_areas->area_id == $hotel['area_id'])
                                    <option selected value="{{ $hotel->area->area_id }}">{{ $hotel->area->area_name  }}</option>
                                @else
                                    <option value="{{ $v_areas->area_id }}">{{ $v_areas->area_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="hotel_status">
                            @if($hotel->hotel_status == 0)
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
                        <textarea rows="8" class="form-control" name="hotel_desc" id="desc_hotel" >{{ $hotel['hotel_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Địa Điểm Chi Tiết</label>
                        <input id="hotel_placedetails" type="text" name="hotel_placedetails" value="{{ $hotel['hotel_placedetails'] }}" class="form-control" id="" placeholder="Địa Điểm Chi Tiết" >
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Link Địa Điểm GG Map</label>
                        <input id="hotel_linkplace" type="text" name="hotel_linkplace" value="{{ $hotel['hotel_linkplace'] }}" class="form-control" id="" placeholder="Link GG Map Địa Điểm Chi Tiết" >
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Iframe Địa Điểm GG Map</label>
                        <input id="hotel_jfameplace" type="text" name="hotel_jfameplace" value="{{ $hotel['hotel_jfameplace'] }}" class="form-control" id="" placeholder="Link GG Map Địa Điểm Chi Tiết" >
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Từ Khóa Tìm Kiếm (SEO)</label>
                        <input id="hotel_tag_keyword" type="text" name="hotel_tag_keyword" value="{{ $hotel['hotel_tag_keyword'] }}" class="form-control" id="" placeholder="Link GG Map Địa Điểm Chi Tiết" >
                        <span class="text-danger form-message"></span>
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
                Validator.isRequired('#name_hotel', 'Vui lòng nhập tên khách sạn'),
                Validator.isRequired('#img_hotel', 'Vui lòng tải lên ảnh khách sạn'),
                Validator.isRequired('#desc_hotel', 'Vui lòng nhập mô tả khách sạn'),
                Validator.isRequired('#hotel_placedetails', 'Vui lòng Nhập Trường Này '),
                Validator.isRequired('#hotel_linkplace', 'Vui lòng Nhập Trường Này '),
                Validator.isRequired('#hotel_jfameplace', 'Vui lòng Nhập Trường Này'),
            ]
        }); 

        $('.form-hotel-submit').click(function() {
           
            if ($('.form-message').text() != '') {
                    $("#form-hotel").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
