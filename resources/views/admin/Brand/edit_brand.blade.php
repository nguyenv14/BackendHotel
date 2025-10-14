@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Thương Hiệu
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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Thương Hiệu</h4>
                <form id="form-brand" action="{{ 'update-brand' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $brand_old['brand_id'] }}" name="brand_id">
                    <div class="form-group">
                        <label for="">Tên Thương Hiệu</label>
                        <input id="name_brand" type="text" name="brand_name" value="{{ $brand_old['brand_name'] }}" class="form-control" id="" placeholder="Tên Thương Hiệu" >
                        <span class="text-danger form-message"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="brand_status">
                            @if($brand_old->brand_status == 0)
                            <option active value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                            @else
                            <option active value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Thương Hiệu</label>
                        <textarea rows="8" class="form-control" name="brand_desc" id="brand_desc" >{{ $brand_old['brand_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-brand-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#brand_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
        <script>
            Validator({
            form: '#form-brand',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#name_brand', 'Vui lòng nhập tên thương hiệu'),
                Validator.isRequired('#brand_desc', 'Vui lòng nhập mô tả thương hiệu'),
            ]
        });

        $('.form-brand-submit').click(function() {
           
            if ($('.form-message').text() != '') {
                    $("#form-brand").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
