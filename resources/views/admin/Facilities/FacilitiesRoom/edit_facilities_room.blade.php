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
                <form id="form-facilitiesroom" action="{{ 'update-room-facilities' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $facilitiesroom_old['facilitiesroom_id'] }}" name="facilitiesroom_id">
                    <div class="form-group">
                        <label for="">Tên Tiện Ích</label>
                        <input id="facilitiesroom_name" type="text" name="facilitiesroom_name" value="{{ $facilitiesroom_old['facilitiesroom_name'] }}" class="form-control" id="" placeholder="Tên Tiện Ích" >
                        <span class="text-danger form-message"></span>
                    </div>
            
                    <div class="form-group">
                        <label>Tải Ảnh Lên</label>
                        <div>
                            <img style="object-fit: cover; margin: 30px 0px 30px 0px" width="120px" height="120px"
                                src="{{ URL::to('public/fontend/assets/img/facilitiesroom/'.$facilitiesroom_old->facilitiesroom_image) }}"
                                alt="">
                        </div>
                        <input id="facilitiesroom_image" type="file" name="facilitiesroom_image" class="file-upload-default">
                        <div class="form-group">
                            <input id="facilitiesroom_image" class="form-control" type="file"  id="facilitiesroom_image" type="file" name="facilitiesroom_image" >
                            <span class="text-danger form-message"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="facilitiesroom_status">
                            @if($facilitiesroom_old['facilitiesroom_status'] == 0)
                            <option active value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                            @else
                            <option active value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Tiện Ích</label>
                        <textarea rows="8" class="form-control" name="facilitiesroom_desc" id="facilitiesroom_desc" >{{ $facilitiesroom_old['facilitiesroom_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-facilitiesroom-submit">Xác Nhận</button>
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
            form: '#form-facilitiesroom',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#facilitiesroom_name', 'Vui lòng nhập tên Tiện Ích'),
                Validator.isRequired('#facilitiesroom_image', 'Vui lòng tải lên ảnh Tiện Ích'),
                Validator.isRequired('#facilitiesroom_desc', 'Vui lòng nhập mô tả Tiện Ích'),
            ]
        });

        $('.form-facilitiesroom-submit').click(function() {
           
            if ($('.form-message').text() != '') {
                    $("#form-facilitiesroom").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
