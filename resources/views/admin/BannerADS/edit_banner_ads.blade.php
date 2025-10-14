@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Banner ADS
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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Banner ADS</h4>
                <form id="form-bannerads" action="{{ 'update-banner-ads' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $bannerads_old['bannerads_id'] }}" name="bannerads_id">
                    <div class="form-group">
                        <label for="">Tên Banner ADS</label>
                        <input id="bannerads_title" type="text" name="bannerads_title" value="{{ $bannerads_old['bannerads_title'] }}" class="form-control" id="" placeholder="Tên Banner ADS" >
                        <span class="text-danger form-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="">Hiễn Thị Ở Trang</label>
                        <select class="form-control" name="bannerads_page">
                            @if($bannerads_old['bannerads_page'] == 1)
                            <option value="1">Trang Chủ</option>
                            <option value="2">Khách Sạn</option>
                            <option value="3">Chi Tiết Khách Sạn</option>
                            @elseif($bannerads_old['bannerads_page'] == 2)
                            <option value="2">Khách Sạn</option>
                            <option value="1">Trang Chủ</option>
                            <option value="3">Chi Tiết Khách Sạn</option>
                            @elseif($bannerads_old['bannerads_page'] == 3)
                            <option value="3">Chi Tiết Khách Sạn</option>
                            <option value="2">Khách Sạn</option>
                            <option value="1">Trang Chủ</option>
                            @endif
                           
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Link Liên Kết</label>
                        <input id="bannerads_link" type="text" name="bannerads_link" class="form-control" id=""
                            placeholder="Tên Banner ADS" value="{{ $bannerads_old['bannerads_link'] }}">
                        <span class="text-danger form-message"></span>
                    </div>

            
                    <div class="form-group">
                        <label>Tải Ảnh Lên</label>
                        <div>
                            <img style="object-fit: cover; margin: 30px 0px 30px 0px" width="120px" height="120px"
                                src="{{ URL::to('public/fontend/assets/img/bannerads/'.$bannerads_old->bannerads_image) }}"
                                alt="">
                        </div>
                        <input id="bannerads_image" type="file" name="bannerads_image" class="file-upload-default">
                        <div class="form-group">
                            <input id="bannerads_image" class="form-control" type="file"  id="bannerads_image" type="file" name="bannerads_image" >
                            <span class="text-danger form-message"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Hiễn thị</label>
                        <select class="form-control" name="bannerads_status">
                            @if($bannerads_old['bannerads_status'] == 0)
                            <option active value="0">Ẩn</option>
                            <option value="1">Hiện</option>
                            @else
                            <option active value="1">Hiện</option>
                            <option value="0">Ẩn</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô Tả Banner ADS</label>
                        <textarea rows="8" class="form-control" name="bannerads_desc" id="bannerads_desc" >{{ $bannerads_old['bannerads_desc']  }}</textarea>
                        <span class="text-danger form-message"></span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2 form-bannerads-submit">Xác Nhận</button>
                </form>
            </div>
        </div>
    </div>
    {{-- <script>
        ClassicEditor
            .create(document.querySelector('#bannerads_desc'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script> --}}
        <script>
            Validator({
            form: '#form-bannerads',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#bannerads_title', 'Vui lòng nhập tên ads banner'),
                Validator.isRequired('#bannerads_link', 'Vui lòng nhập link liên kết'),
                Validator.isRequired('#bannerads_image', 'Vui lòng tải lên ảnh ads banner'),
                Validator.isRequired('#bannerads_desc', 'Vui lòng nhập mô tả ads banner'),
            ]
        });

        $('.form-bannerads-submit').click(function() {
            if ($('.form-message').text() != '') {
                    $("#form-bannerads").submit(function(e) {
                        e.preventDefault();
                    });
            }
        })
    </script>
@endsection
