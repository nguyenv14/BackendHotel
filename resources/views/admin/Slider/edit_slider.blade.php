@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Slider
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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Slider</h4>
                <form id="form-slider" action="{{ 'update-slider' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $slider_old['slider_id'] }}" name="slider_id">
                    <div class="form-group">
                        <label for="">Tên Slider</label>
                        <input id="name_slider" type="text" name="slider_name" value="{{ $slider_old['slider_name'] }}" class="form-control" id="" placeholder="Tên Slider" >
                        <span class="text-danger form-message"></span>
                    </div>
            
                    <div class="form-group">
                        <label>Tải Ảnh Lên</label>
                        <div>
                            <img style="object-fit: cover; margin: 30px 0px 30px 0px" width="120px" height="120px"
                                src="{{ URL::to('public/fontend/assets/img/slider/'.$slider_old->slider_image) }}"
                                alt="">
                        </div>
                        <input id="slider_image" type="file" name="slider_image" class="file-upload-default">
                        <div class="form-group">
                            <input id="img_slider" class="form-control" type="file"  id="slider_image" type="file" name="slider_image" >
                            <span class="text-danger form-message"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="slider_status">
                            @if($slider_old->slider_status == 0)
                            <option active value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                            @else
                            <option active value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Slider</label>
                        <textarea rows="8" class="form-control" name="slider_desc" id="desc_slider" >{{ $slider_old['slider_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-slider-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#slider_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
        <script>
            Validator({
            form: '#form-slider',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#name_slider', 'Vui lòng nhập tên slider'),
                Validator.isRequired('#img_slider', 'Vui lòng tải lên ảnh slider'),
                Validator.isRequired('#desc_slider', 'Vui lòng nhập mô tả slider'),
            ]
        });

        $('.form-slider-submit').click(function() {
           
            if ($('.form-message').text() != '') {
                    $("#form-slider").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
