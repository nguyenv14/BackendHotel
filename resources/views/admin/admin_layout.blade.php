<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title> Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href=" {{ asset('public/backend/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/backend/assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('public/backend/assets/css/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('public/backend/assets/images/favicon.ico') }}" />
    {{-- Toastr Css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    {{-- jquery --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
        integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- jquery CSS UI --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- jquery UI --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- Js Toast  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- CKeditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <script>
        function message_toastr(type, content) {
            Command: toastr[type](content)
            toastr.options = {
                "closeButton": true,
                "debug": true,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        }
    </script>
    {{-- Validator --}}
    <script src=" {{ asset('public/backend/assets/js/validator.js') }}"></script>
    <style>
        @font-face {
            font-family: nhanf;
            src: url({{ asset('public/backend/assets/fonts/Mt-Regular.otf') }});
            font-display: swap;
        }

        a {
            text-decoration: none;
        }
        .chongloihuhu {}
    </style>
</head>

<body>
    <?php
    if(session()->get('message')!=null){
       $message = session()->get('message');
       $type = $message['type'];
       $content = $message['content'];
    ?>
    <script>
        message_toastr("{{ $type }}", "{{ $content }}");
    </script>
    <?php
    }
    ?>

    <div class="overlay-loading"></div>
    <div class="loading">
        <img src="{{ asset('public/fontend/assets/img/loader.gif') }}" alt="">
    </div>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="{{ URL::to('admin/dashboard') }}"><img
                        src="{{ asset('public/backend/assets/images/logo.svg') }}" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href=""><img
                        src="{{ asset('public/backend/assets/images/logo-mini.svg') }}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-stretch">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="mdi mdi-menu"></span>
                </button>
                <div class="search-field d-none d-md-block">
                    <form class="d-flex align-items-center h-100" action="#">
                        <div class="input-group">
                            <div class="input-group-prepend bg-transparent">
                                <i class="input-group-text border-0 mdi mdi-magnify"></i>
                            </div>
                            <input type="text" class="form-control bg-transparent border-0"
                                placeholder="Search projects">
                        </div>
                    </form>
                </div>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="nav-profile-img">
                                <img src="{{ asset('public/backend/assets/images/faces/face1.jpg') }}" alt="image">
                                <span class="availability-status online"></span>
                            </div>
                            <div class="nav-profile-text">
                                <p class="mb-1 text-black">
                                    <?php
                                    if (Auth::check()) {
                                        echo Auth::user()->admin_name;
                                    }
                                    ?>
                                </p>
                            </div>
                        </a>
                        <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="mdi mdi-cached me-2 text-success"></i> Activity Log </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ URL::to('admin/auth/logout') }}">
                                <i class="mdi mdi-logout me-2 text-primary"></i> Đăng Xuất</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-email-outline"></i>
                            <span class="count-symbol bg-warning"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            aria-labelledby="messageDropdown">
                            <h6 class="p-3 mb-0">Tin Nhắn</h6>

                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-bs-toggle="dropdown">
                            <i class="mdi mdi-bell-outline"></i>
                            <span class="count-symbol bg-danger"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            aria-labelledby="notificationDropdown">
                            <h6 class="p-3 mb-0">Thông Báo</h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-success">
                                        <i class="mdi mdi-note-outline"></i>
                                    </div>
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject font-weight-normal mb-1">Đơn Hàng - 2 giây trước</h6>
                                    <p class="text-gray ellipsis mb-0">Sếp Nguyên Vừa Mới Đặt Hàng</p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-warning">
                                        <i class="mdi mdi-comment-text-outline"></i>
                                    </div>
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject font-weight-normal mb-1">Bình Luận - 1 giây trước</h6>
                                    <p class="text-gray ellipsis mb-0">Lê Khả Nhân Vừa Bình Luận Vào Sản Phẩm</p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-info">
                                        <i class="mdi mdi-login"></i>
                                    </div>
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject font-weight-normal mb-1">Đăng Nhập - 1 phút trước</h6>
                                    <p class="text-gray ellipsis mb-0">Sếp Nhuận Vừa Đăng Nhập Vào Hệ Thống </p>
                                </div>
                            </a>
                            <h6 class="p-3 mb-0 text-center">Thông Báo Từ Hệ Thống</h6>
                            <div id="loading_notification">

                            </div>
                            <div class="dropdown-divider"></div>
                            <h6 class="p-3 mb-0 text-center">Xem Tất Cả Thông Báo</h6>
                        </div>
                    </li>

                    <li class="nav-item d-none d-lg-block full-screen-link">
                        <a class="nav-link">
                            <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
                        </a>
                    </li>

                    <li class="nav-item nav-logout d-none d-lg-block">
                        <a class="nav-link" href="#">
                            <i class="mdi mdi-power"></i>
                        </a>
                    </li>
                    <li class="nav-item nav-settings d-none d-lg-block">
                        <a class="nav-link" href="#">
                            <i class="mdi mdi-format-line-spacing"></i>
                        </a>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">

                    <li class="nav-item nav-profile">
                        <a href="#" class="nav-link">
                            <div class="nav-profile-image">
                                <img src="{{ asset('public/backend/assets/images/faces/face1.jpg') }}"
                                    alt="profile">
                                <span class="login-status online"></span>
                                <!--change to offline or busy as needed-->
                            </div>
                            <div class="nav-profile-text d-flex flex-column">
                                <span class="font-weight-bold mb-2">
                                    <?php
                                    if (Auth::check()) {
                                        echo Auth::user()->admin_name;
                                    }
                                    ?>
                                </span>
                                <span class="text-secondary text-small">
                                    @if (Auth::user()->hasRoles('admin'))
                                        {{ 'Quản Trị Hệ Thống' }}
                                    @elseif(Auth::user()->hasRoles('manager'))
                                        {{ 'Quản Lý Hệ Thống' }}
                                    @elseif(Auth::user()->hasRoles('employee'))
                                        {{ 'Nhân Viên Hệ Thống' }}
                                    @else
                                        {{ 'Chưa Đặt Quyền Hạn' }}
                                    @endif
                                </span>
                            </div>
                            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::to('admin/dashboard') }}">
                            <span class="menu-title">Dashboard</span>
                            <i class="mdi mdi-home menu-icon"></i>
                        </a>
                    </li>
                    @hasrole('admin')
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-admin" aria-expanded="false"
                                aria-controls="ui-basic">
                                <span class="menu-title">Quản Lý Admin</span>
                                <i class="menu-arrow"></i>
                                <i class="mdi mdi-account-circle menu-icon"></i>
                            </a>
                            <div class="collapse" id="ui-basic-admin">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ URL::to('admin/auth/all-admin') }}">Danh Sách Admin</a></li>
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ URL::to('admin/auth/register') }}">Thêm Tài Khoản Admin</a></li>
                                </ul>
                            </div>
                        </li>
                    @endhasrole


                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-customer"
                            aria-expanded="false" aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Khách Hàng</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-customer">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/customer/all-customer') }}">Danh Sách Khách Hàng</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/customer/view-email') }}">Gửi Mail Khách Hàng</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-restaurants" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Nhà Hàng</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-food menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-restaurants">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/restaurants/all-restaurants') }}">Danh Sách Nhà Hàng</a>
                                    </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/restaurants/add-restaurant') }}">Thêm Nhà Hàng</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-ads" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Banner Ads</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-animation menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-ads">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/bannerads/all-banner-ads') }}">Danh Sách Banner
                                        ADS</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/bannerads/add-banner-ads') }}">Thêm Banner ADS</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-slider" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Slider</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-book-open menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-slider">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/slider/all-slider') }}">Danh Sách Slider</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/slider/add-slider') }}">Thêm Slider</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-brand" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Thương Hiệu</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-book-variant menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-brand">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/brand/all-brand') }}">Danh Sách Thương Hiệu</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/brand/add-brand') }}">Thêm Thương Hiệu</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-area" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Khu Vực</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-map-marker-radius menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-area">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/area/all-area') }}">Danh Sách Khu Vực</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/area/add-area') }}">Thêm Khu Vực</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-hotel" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Khách Sạn</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-map-marker-radius menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-hotel">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/all-hotel') }}">Danh Sách Khách Sạn</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/add-hotel') }}">Thêm Khách Sạn</a></li>
                            </ul>
                        </div>
                    </li>

                    
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-evaluate" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Đánh Giá</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-map-marker-radius menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-evaluate">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/evaluate/all-evaluate') }}">Danh Sách Đánh Giá</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-facilities"
                            aria-expanded="false" aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Tiện Ích</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi mdi-sale menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-facilities">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    Quản Lí Tiện Ích Khách Sạn
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/facilities/hotel/all-hotel-facilities') }}">Danh
                                        Sách Tiện Ích Khách Sạn</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/facilities/hotel/add-hotel-facilities') }}">Thêm
                                        Tiện Ích Khách Sạn</a>
                                </li>
                            </ul>
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    Quản Lí Tiện Ích Phòng
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/facilities/room/all-room-facilities') }}">Danh
                                        Sách Tiện Ích Phòng</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/facilities/room/add-room-facilities') }}">Thêm
                                        Tiện Ích Phòng</a>
                                </li>
                            </ul>
                        </div>
                    </li>


                    @hasanyroles(['admin','manager'])
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-coupon" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Sự Kiện</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi mdi-sale menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-coupon">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    Quản Lí Mã Giảm Giá
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/coupon/list-coupon') }}">Danh
                                        Sách Mã Giảm Giá</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/coupon/add-coupon') }}">Thêm
                                        Mã Giảm
                                        Giá</a>
                                </li>
                            </ul>
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    Quản Lí Sự Kiện FlashSale
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ URL::to('#') }}">Danh
                                        Sách Khách Sạn</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="{{ URL::to('#') }}">Thêm
                                        Khách Sạn Vào SK</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-service" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Thuế Dịch Vụ</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-map-marker-radius menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-service">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/servicecharge/all-service-charge') }}">Danh Sách Thuế Phí Dịch Vụ</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/servicecharge/add-service-charge') }}">Thêm Thuế Phí Dịch Vụ</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-activity"
                            aria-expanded="false" aria-controls="ui-basic">
                            <span class="menu-title">Nhật Ký Hoạt Động</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-gauge menu-icon"></i>
                        </a>

                        <div class="collapse" id="ui-basic-activity">

                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    Hoạt Động Đăng Nhập
                                </li>
                                @hasrole('admin')
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ URL::to('admin/activity/all-activity-admin') }}">Hoạt Động Đăng Nhập
                                            Admin</a>
                                    </li>
                                @endhasrole
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/activity/all-activity-customer') }}">Hoạt Động Đăng
                                        Nhập Khách Hàng</a>
                                </li>

                            </ul>

                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    Hoạt Động Hệ Thống
                                </li>
                                @hasrole('admin')
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ URL::to('admin/activity/all-manipulation-admin') }}">Nhật Ký Thao Tác
                                            Admin</a>
                                    </li>
                                @endhasrole

                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/activity/all-manipulation-customer') }}">Nhật Ký Thao
                                        Tác Người Dùng</a>
                                </li>

                            </ul>

                        </div>


                    </li>
                    @endhasanyroles

                    @hasrole('admin')
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-config-web"
                                aria-expanded="false" aria-controls="ui-basic">
                                <span class="menu-title">Cấu Hình WebSite</span>
                                <i class="menu-arrow"></i>
                                <i class="mdi mdi-account-circle menu-icon"></i>
                            </a>
                            <div class="collapse" id="ui-basic-config-web">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a class="nav-link" href="{{ URL::to('/admin/web') }}">Cấu
                                            Hình Web</a></li>

                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ URL::to('/admin/config-footer') }}">Cấu Hình Footer</a></li>
                                </ul>
                            </div>
                        </li>
                    @endhasrole

                    @impersonate()
                        <li class="nav-item" style="margin-top:-10px ">
                            <a class="nav-link" href="{{ url('admin/auth/destroy-impersonate') }}">
                                <span class="menu-title text-danger">Hủy Chuyển Quyền</span>
                                <i style="margin-left: 10px" class="mdi mdi-account-remove menu-icon text-danger"></i>
                            </a>
                        </li>
                    @endimpersonate

                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">

                    @yield('admin_content')

                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="container-fluid d-flex justify-content-between">
                        <span class="text-muted d-block text-center text-sm-start d-sm-inline-block">Đồ Án Cơ Sở
                            2</span>
                        <span class="float-none float-sm-end mt-1 mt-sm-0 text-end"> Nhuận Báo Thủ - Nhân - Học
                            Laravel</span>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>


    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('public/backend/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('public/backend/assets/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('public/backend/assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('public/backend/assets/js/off-canvas.js') }}"></script>
    <script src=" {{ asset('public/backend/assets/js/hoverable-collapse.js') }}"></script>
    <script src=" {{ asset('public/backend/assets/js/misc.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="  {{ asset('public/backend/assets/js/dashboard.js') }}"></script>
    <script src=" {{ asset('public/backend/assets/js/todolist.js') }}"></script>
    <!-- End custom js for this page -->

    {{--     
    <script>
        setInterval(() => {
            $.ajax({
                url: '{{ url('admin/dashboard/notification') }}',
                method: 'GET',
                data: {

                },
                success: function(data) {
                    $('#loading_notification').html(data);
                },
                error: function(data) {
                    // alert("Nhân Ơi Fix Bug Huhu :<");
                },
            });
        }, 3000);
    </script> --}}

</body>

</html>
