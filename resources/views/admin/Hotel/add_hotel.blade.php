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
                <h4 style="margin-top: -15px" class="card-title">Thêm Khách Sạn</h4>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="hotelTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="hotel-info-tab" data-bs-toggle="tab"
                            data-bs-target="#hotel-info" type="button" role="tab">
                            <i class="mdi mdi-home"></i> Thông Tin Khách Sạn
                            <span class="badge bg-success ms-2" id="step1-badge" style="display: none;">
                                <i class="mdi mdi-check"></i>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link disabled" id="hotel-manager-tab" data-bs-toggle="tab" data-bs-target="#hotel-manager"
                            type="button" role="tab" disabled>
                            <i class="mdi mdi-account"></i> Quản Lý Khách Sạn
                            <span class="badge bg-secondary ms-2" id="step2-badge">
                                <i class="mdi mdi-lock"></i>
                            </span>
                        </button>
                    </li>
                </ul>

                <form id="form-hotel" action="{{ 'save-hotel' }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="hotelTabsContent">
                        <!-- Tab 1: Hotel Information -->
                        <div class="tab-pane fade show active" id="hotel-info" role="tabpanel">
                            <div class="form-group">
                                <label for="">Tên Khách Sạn</label>
                                <input id="name_hotel" type="text" name="hotel_name" class="form-control" id=""
                                    placeholder="Tên Khách Sạn" value="{{ old('name_hotel') }}">
                                <span class="text-danger form-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="">Hạng Khách Sạn</label>
                                <select class="form-control m-bot15" name="hotel_rank">
                                    <option value="1">1 Sao</option>
                                    <option value="2">2 Sao</option>
                                    <option value="3">3 Sao</option>
                                    <option value="4">4 Sao</option>
                                    <option value="5">5 Sao</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Loại Khách Sạn</label>
                                <select class="form-control m-bot15" name="hotel_type">
                                    <option value="1">Khách Sạn </option>
                                    <option value="2">Khách Sạn Căn Hộ</option>
                                    <option value="3">Khu Nghỉ Dưỡng</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Thương Hiệu Khách Sạn</label>
                                <select class="form-control m-bot15" name="brand_id">
                                    @foreach ($brands as $key => $brand)
                                        <option value="{{ $brand->brand_id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Khu Vực Khách Sạn</label>
                                <select class="form-control m-bot15" name="area_id">
                                    @foreach ($areas as $key => $area)
                                        <option value="{{ $area->area_id }}">{{ $area->area_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Vị Trí Chi Tiết</label>
                                <input id="hotel_placedetails" type="text" name="hotel_placedetails" class="form-control"
                                    placeholder="Vị Trí Chi Tiết" value="{{ old('hotel_placedetails') }}">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Link Vị Trí Google Map</label>
                                <input id="hotel_linkplace" type="text" name="hotel_linkplace" class="form-control"
                                    placeholder="Link Vị Trí Google Map" value="{{ old('hotel_linkplace') }}">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Jframe Vị Trí Google Map</label>
                                <input id="hotel_jfameplace" type="text" name="hotel_jfameplace" class="form-control"
                                    placeholder="Jframe Vị Trí Google Map" value="{{ old('hotel_jfameplace') }}">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="hotel_image" class="form-label">Tải Ảnh Lên</label>
                                <input id="img_hotel" class="form-control" type="file" id="hotel_image"
                                    type="file" name="hotel_image">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="exampleTextarea1">Mô Tả Khách Sạn</label>
                                <textarea id="desc_hotel" rows="8" class="form-control" name="hotel_desc"></textarea>
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="exampleTextarea1">Từ Khóa - Tag Khách Sạn (SEO)</label>
                                <textarea id="tag_keyword_hotel" rows="8" class="form-control" name="hotel_tag_keyword"></textarea>
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Hiễn thị</label>
                                <select class="form-control" name="hotel_status">
                                    <option value="1">Hiện</option>
                                    <option value="0">Ẩn</option>
                                </select>
                            </div>

                            <!-- Tab 1 Navigation -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-gradient-primary" id="next-to-manager">
                                    Tiếp Theo <i class="mdi mdi-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tab 2: Hotel Manager Information -->
                        <div class="tab-pane fade" id="hotel-manager" role="tabpanel">
                            <h5 class="mb-4">Thông Tin Quản Lý Khách Sạn</h5>

                            <div class="form-group">
                                <label for="">Tên Quản Lý</label>
                                <input id="manager_name" type="text" name="manager_name" class="form-control"
                                    placeholder="Tên Quản Lý" value="{{ old('manager_name') }}">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Email Quản Lý</label>
                                <input id="manager_email" type="email" name="manager_email" class="form-control"
                                    placeholder="Email Quản Lý" value="{{ old('manager_email') }}">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Số Điện Thoại</label>
                                <input id="manager_phone" type="text" name="manager_phone" class="form-control"
                                    placeholder="Số Điện Thoại" value="{{ old('manager_phone') }}">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Mật Khẩu</label>
                                <input id="manager_password" type="password" name="manager_password"
                                    class="form-control" placeholder="Mật Khẩu">
                                <span class="text-danger form-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Xác Nhận Mật Khẩu</label>
                                <input id="manager_password_confirmation" type="password"
                                    name="manager_password_confirmation" class="form-control"
                                    placeholder="Xác Nhận Mật Khẩu">
                                <span class="text-danger form-message"></span>
                            </div>

                            <!-- Tab 2 Navigation -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="back-to-hotel">
                                    <i class="mdi mdi-arrow-left"></i> Quay Lại
                                </button>
                                <button type="submit" class="btn btn-gradient-primary form-hotel-submit">
                                    <i class="mdi mdi-check"></i> Hoàn Thành
                                </button>
                            </div>
                        </div>
                    </div>
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
        // Tab Navigation
        $('#next-to-manager').click(function() {
            // Validate Tab 1 fields
            let isValid = true;
            const requiredFields = ['#name_hotel', '#img_hotel', '#desc_hotel', '#tag_keyword_hotel',
                '#hotel_placedetails', '#hotel_linkplace', '#hotel_jfameplace'
            ];

            requiredFields.forEach(function(field) {
                if ($(field).val() === '') {
                    $(field).next('.form-message').text('Trường này không được để trống');
                    isValid = false;
                } else {
                    $(field).next('.form-message').text('');
                }
            });

            if (isValid) {
                // Enable Tab 2 and update styling
                $('#hotel-manager-tab').removeClass('disabled').prop('disabled', false);
                $('#step1-badge').show();
                $('#step2-badge').removeClass('bg-secondary').addClass('bg-primary').html('<i class="mdi mdi-unlock"></i>');
                $('#hotel-manager-tab').click();
                
                // Show success message using toastr
                message_toastr('success', 'Bước 1 hoàn thành! Bạn có thể tiếp tục với bước 2.');
            } else {
                message_toastr('error', 'Vui lòng điền đầy đủ thông tin khách sạn trước khi tiếp tục!');
            }
        });

        $('#back-to-hotel').click(function() {
            $('#hotel-info-tab').click();
        });

        // Form Validation
        Validator({
            form: '#form-hotel',
            errorSelector: '.form-message',
            rules: [
                Validator.isRequired('#name_hotel', 'Vui lòng nhập tên khách sạn'),
                Validator.isRequired('#img_hotel', 'Vui lòng tải lên ảnh khách sạn'),
                Validator.isRequired('#desc_hotel', 'Vui lòng nhập mô tả khách sạn'),
                Validator.isRequired('#tag_keyword_hotel', 'Vui lòng nhập từ khóa SEO'),
                Validator.isRequired('#hotel_placedetails', 'Vui lòng nhập vị trí chi tiết'),
                Validator.isRequired('#hotel_linkplace', 'Vui lòng nhập link Google Map'),
                Validator.isRequired('#hotel_jfameplace', 'Vui lòng nhập Jframe Google Map'),
                Validator.isRequired('#manager_name', 'Vui lòng nhập tên quản lý'),
                Validator.isRequired('#manager_email', 'Vui lòng nhập email quản lý'),
                Validator.isEmail('#manager_email', 'Email không hợp lệ'),
                Validator.isRequired('#manager_phone', 'Vui lòng nhập số điện thoại'),
                Validator.isRequired('#manager_password', 'Vui lòng nhập mật khẩu'),
                Validator.minLength('#manager_password', 6, 'Mật khẩu phải có ít nhất 6 ký tự'),
                Validator.isRequired('#manager_password_confirmation', 'Vui lòng xác nhận mật khẩu'),
                Validator.isConfirmed('#manager_password_confirmation', function() {
                    return $('#manager_password').val();
                }, 'Mật khẩu xác nhận không khớp')
            ]
        });

        // Submit Handler
        $('.form-hotel-submit').click(function() {
            // Validate all fields before submit
            let isValid = true;
            const allRequiredFields = [
                '#name_hotel', '#img_hotel', '#desc_hotel', '#tag_keyword_hotel',
                '#hotel_placedetails', '#hotel_linkplace', '#hotel_jfameplace',
                '#manager_name', '#manager_email', '#manager_phone',
                '#manager_password', '#manager_password_confirmation'
            ];

            allRequiredFields.forEach(function(field) {
                if ($(field).val() === '') {
                    $(field).next('.form-message').text('Trường này không được để trống');
                    isValid = false;
                }
            });

            // Check password confirmation
            if ($('#manager_password').val() !== $('#manager_password_confirmation').val()) {
                $('#manager_password_confirmation').next('.form-message').text('Mật khẩu xác nhận không khớp');
                isValid = false;
            }

            if (!isValid) {
                $("#form-hotel").submit(function(e) {
                    e.preventDefault();
                });
            }
        });

        // Notification function using toastr
        function showNotification(type, message) {
            message_toastr(type, message);
        }

        // Prevent clicking on disabled tab
        $('#hotel-manager-tab').click(function(e) {
            if ($(this).hasClass('disabled')) {
                e.preventDefault();
                message_toastr('error', 'Vui lòng hoàn thành bước 1 trước khi chuyển sang bước 2!');
                return false;
            }
        });
    </script>
@endsection
