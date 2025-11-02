<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title> Quản Lý {{ $hotel->hotel_name }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
        integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- Js Toast  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
                        </div>
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
                            <input type="hidden" value="{{ $hotel->hotel_id }}" name="hotel_id" id="hotel_id">
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
                                        {{ 'Quản lý Khách Sạn' }}
                                    @endif
                                </span>
                            </div>
                            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::to('admin/hotel/manager?hotel_id=' . $hotel->hotel_id) }}">
                            <span class="menu-title">{{ $hotel->hotel_name }}</span>
                            <i class="fa-solid fa-hotel menu-icon"></i>
                        </a>
                    </li>

                    @hasanyroles(['admin','manager'])
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-admin" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Staff</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-account-circle menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-admin">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/staff?hotel_id=' . $hotel->hotel_id) }}">Danh
                                        Sách Admin</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/staff/add-staff?hotel_id=' . $hotel->hotel_id) }}">Thêm
                                        Tài Khoản Admin</a></li>
                            </ul>
                        </div>
                    </li>
                    @endhasanyroles
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#hotel-gallery-menu"
                            aria-expanded="false" aria-controls="hotel-gallery">
                            <span class="menu-title">Quản Lý Ảnh Và Video KS</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-folder-image menu-icon"></i>
                        </a>
                        <div class="collapse" id="hotel-gallery-menu">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/list-media-gallery?hotel_id=' . $hotel->hotel_id) }}">Thư
                                        Viện Ảnh Và Video</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/list-video-gallery?hotel_id=' . $hotel->hotel_id) }}">Thêm
                                        Video</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/list-image-gallery?hotel_id=' . $hotel->hotel_id) }}">Thêm
                                        Thư Viện Ảnh</a></li>
                            </ul>
                        </div>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#hotel-rooms-menu" aria-expanded="false"
                            aria-controls="hotel-rooms">
                            <span class="menu-title">Quản Lý Phòng</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-book-variant menu-icon"></i>
                        </a>
                        <div class="collapse" id="hotel-rooms-menu">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/room/all-room?hotel_id=' . $hotel->hotel_id) }}">Danh
                                        Sách Phòng</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/room/add-room?hotel_id=' . $hotel->hotel_id) }}">Thêm
                                        Phòng</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/room/list-image-gallery?hotel_id=' . $hotel->hotel_id) }}">Thư
                                        Viện Ảnh</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#hotel-type-rooms-menu"
                            aria-expanded="false" aria-controls="hotel-type-rooms-menu">
                            <span class="menu-title">Quản Lý Loại Phòng</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-book-variant menu-icon"></i>
                        </a>
                        <div class="collapse" id="hotel-type-rooms-menu">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/room/all-room?hotel_id=' . $hotel->hotel_id) }}">Danh
                                        Sách Phòng</a></li>
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/room/typeroom/add-typeroom?hotel_id=' . $hotel->hotel_id) }}">Thêm
                                        Lựa Chọn Phòng</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#hotel-edit-info-menu"
                            aria-expanded="false" aria-controls="hotel-edit-info-menu">
                            <span class="menu-title">Chỉnh Sửa Thông Tin</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-book-variant menu-icon"></i>
                        </a>
                        <div class="collapse" id="hotel-edit-info-menu">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/edit-hotel?hotel_id=' . $hotel->hotel_id) }}">Chỉnh
                                        Sửa Thông Tin KS</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#hotel-overview-info-menu"
                            aria-expanded="false" aria-controls="hotel-overview-info-menu">
                            <span class="menu-title">Thông Tin Tổng Quan</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-book-variant menu-icon"></i>
                        </a>
                        <div class="collapse" id="hotel-overview-info-menu">
                            <ul class="nav flex-column sub-menu">
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic-order" aria-expanded="false"
                            aria-controls="ui-basic">
                            <span class="menu-title">Quản Lý Đặt Phòng</span>
                            <i class="menu-arrow"></i>
                            <i class="mdi mdi-map-marker-radius menu-icon"></i>
                        </a>
                        <div class="collapse" id="ui-basic-order">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"> <a class="nav-link"
                                        href="{{ URL::to('admin/hotel/manager/order/all-order?hotel_id=' . $hotel->hotel_id) }}">Danh
                                        Sách Đơn Đặt Phòng</a></li>
                            </ul>
                        </div>
                    </li>

                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('manager_hotel')
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

    <script>
        $(document).ready(function() {
            // Đóng tất cả collapse menu
            $('.sidebar .collapse').removeClass('show');
            $('.sidebar .nav-link[data-bs-toggle="collapse"]').attr('aria-expanded', 'false');

            // Ngăn chặn misc.js tự động thêm active class cho hotel name - tăng thời gian delay
            setTimeout(function() {
                $('.sidebar .nav-item:first-child').removeClass('active');
                $('.sidebar .nav-item:first-child .nav-link').removeClass('active');
            }, 500);

            // Thêm interval để liên tục loại bỏ active state
            setInterval(function() {
                $('.sidebar .nav-item:first-child').removeClass('active');
                $('.sidebar .nav-item:first-child .nav-link').removeClass('active');
            }, 1000);

            // Smooth collapse animation - chỉ áp dụng cho menu items có collapse
            $('.sidebar .nav-item:not(:first-child) .nav-link[data-bs-toggle="collapse"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                var $target = $(target);

                // Toggle collapse
                $target.collapse('toggle');

                // Update aria-expanded
                var isExpanded = $target.hasClass('show');
                $(this).attr('aria-expanded', isExpanded);
            });

            // Add smooth transitions - chỉ áp dụng cho menu items thông thường
            $('.sidebar .nav-item:not(:first-child) .nav-link').on('mouseenter', function() {
                $(this).addClass('hover-effect');
            }).on('mouseleave', function() {
                $(this).removeClass('hover-effect');
            });

            // Hotel name click - chỉ redirect, không ảnh hưởng đến menu khác
            $('.sidebar .nav-item:first-child .nav-link').on('click', function(e) {
                // Chỉ redirect, không có hiệu ứng collapse
                e.stopPropagation();
                return true;
            });

            // Ngăn chặn event bubbling từ hotel name
            $('.sidebar .nav-item:first-child').on('click', function(e) {
                e.stopPropagation();
            });

            // Đảm bảo các menu items khác không bị ảnh hưởng bởi hotel name
            $('.sidebar .nav-item:not(:first-child)').on('click', function(e) {
                // Chỉ xử lý menu items thông thường
                if ($(this).find('.nav-link[data-bs-toggle="collapse"]').length > 0) {
                    return; // Đã xử lý ở trên
                }
            });

            // Initialize tooltips if needed
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

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
