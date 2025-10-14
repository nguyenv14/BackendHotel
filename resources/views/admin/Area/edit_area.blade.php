@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Khu Vực
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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Khu Vực</h4>
                <form id="form-area" action="{{ 'update-area' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $area_old['area_id'] }}" name="area_id">
                    <div class="form-group">
                        <label for="">Tên Khu Vực</label>
                        <input id="name_area" type="text" name="area_name" value="{{ $area_old['area_name'] }}" class="form-control" id="" placeholder="Tên Khu Vực" >
                        <span class="text-danger form-message"></span>
                    </div>
            
                    <div class="form-group">
                        <label>Tải Ảnh Lên</label>
                        <div>
                            <img style="object-fit: cover; margin: 30px 0px 30px 0px" width="120px" height="120px"
                                src="{{ URL::to('public/fontend/assets/img/area/'.$area_old->area_image) }}"
                                alt="">
                        </div>
                        <input id="area_image" type="file" name="area_image" class="file-upload-default">
                        <div class="form-group">
                            <input id="img_area" class="form-control" type="file"  id="area_image" type="file" name="area_image" >
                            <span class="text-danger form-message"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="area_status">
                            @if($area_old->area_status == 0)
                            <option active value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                            @else
                            <option active value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Khu Vực</label>
                        <textarea rows="8" class="form-control" name="area_desc" id="desc_area" >{{ $area_old['area_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-area-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#area_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
        <script>
            Validator({
            form: '#form-area',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#name_area', 'Vui lòng nhập tên area'),
                Validator.isRequired('#img_area', 'Vui lòng tải lên ảnh area'),
                Validator.isRequired('#desc_area', 'Vui lòng nhập mô tả area'),
            ]
        });

        $('.form-area-submit').click(function() {
           
            if ($('.form-message').text() != '') {
                    $("#form-area").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
