<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $meta['title'] }}</title>

    <!-- SEO -->
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="robots" content="INDEX,FOLLOW">
    <link rel="canonical" href="{{ $meta['canonical'] }}">
    <meta name="author" content="Nhân Sợ Code Và Nhuận Báo Thủ">
    <link REL="SHORTCUT ICON" href="{{ asset('public/fontend/assets/iconlogo/testhuhu.jpg') }}">


    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="{{ $meta['canonical'] }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $meta['sitename'] }}">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:image" content="{{ $meta['image'] }}">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:url" content="{{ $meta['canonical'] }}">
    <meta name="twitter:type" content="website">
    <meta name="twitter:site_name" content="{{ $meta['sitename'] }}">
    <meta name="twitter:title" content="{{ $meta['title'] }}">
    <meta name="twitter:description" content="{{ $meta['description'] }}">
    <meta name="twitter:image" content="{{ $meta['image'] }}">

    <!-- END SEO -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/kiemtradon.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/owlcarousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href=" {{ asset('public/fontend/assets/owlcarousel/assets/owl.theme.default.min.css') }}">


    {{-- jquery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    {{-- jquery CSS UI --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- jquery UI --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- Toastr Css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    {{-- Js Toast  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- Khởi Tạo Giá Trị Cần Thiết --}}
    <script>
        $.ajax({
            url: '{{ url('/inint-value') }}',
            method: 'GET',
            data: {

            },
            success: function(data) {
                // alert("Giá Trị Đã Được Khởi Tạo");
            },
            error: function() {
                alert("Bug Huhu :<<");
            }
        })
    </script>
    {{-- Thông Báo Toastr --}}
    <script>
        function message_toastr(type, title, content) {
            Command: toastr[type](title, content)
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
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

    <style>
        @font-face {
            font-family: nhanf;
            src: url({{ asset('public/fontend/assets/fonts/Mt-Regular.otf') }});
            font-display: swap;
        }

        html,
        body {
            cursor: url("https://cdn.custom-cursor.com/db/5396/32/doraemon-necklace-cursor.png"), auto;
        }

        a:hover {
            cursor: url("https://cdn.custom-cursor.com/db/5395/32/doraemon-pointer.png"), auto;
        }
    </style>

</head>

<body class="preloading">
    <div class="load">
        <img src="{{ asset('public/fontend/assets/img/loader.gif') }}" alt="">
    </div>
    <div class="loading">
        <img src="{{ asset('public/fontend/assets/img/loader.gif') }}" alt="">
    </div>
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

    <nav class="navbar">
        <div class="navbar-logo">
            <a class="navbar-item-link" href="{{ URL::to('/') }}"> <img
                    style="width: 130px; height: 45px;object-fit: cover;"
                    src="{{ asset('public/fontend/assets/img/config/' . $config_logo_web->config_image) }}"
                    alt="{{ $config_logo_web->config_content }}">
            </a>
        </div>
        <ul class="navbar-list .navbar-list--left">
            <li class="navbar-item">
                <a class="navbar-item-link" href="">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a class="navbar-item-link" href="{{ URL::to('/') }}"><span>Trang Chủ</span></a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-link" href="{{ URL::to('/khach-san') }}"><span>Khách Sạn</span></a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-link" href=""><span>Homestay</span></a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-link" href=""><span>Nhà Hàng</span></a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-link" href=""><span>Đặc Sản - Myfresh</span></a>
            </li>
            <li class="navbar-item">
                <a class="navbar-item-link" href=""><span>Tour & Sự Kiện</span></a>
            </li>
        </ul>

        <ul class="navbar-list navbar-list--right">
            {{-- <li class="navbar-item">
                <a class="navbar-item-link" href="">
                    <i class="fa-solid fa-gift"></i>
                </a>
                <a class="navbar-item-link" href="gioithieunhanqua.html">
                    Giới Thiệu Nhận Quà
                </a>
            </li> --}}
            <li class="navbar-item">
                <a class="navbar-item-link" href="">
                    <i class="fa-regular fa-file-lines"></i>
                </a>
                <a class="navbar-item-link" href="">
                    Kiểm Tra Đơn Hàng
                </a>
            </li>
            <li class="navbar-item ">
                <label style="cursor: pointer;" for="Notification-input" class="navbar-item-link">
                    <i class="fa-solid fa-bell"></i>
                </label>
            </li>

            @if (session()->get('customer_id') != null)
                <label class="navbar-item">
                    <label class="navbar-item-link">
                        <i class="fa-solid fa-user"></i>
                    </label>
                    <label class="navbar-item-link">
                        {{ session()->get('customer_name') }}
                    </label>
                </label>
                <a href="{{ URL::to('user/logout') }}">
                    <label class="navbar-item">
                        <label class="navbar-item-link">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </label>
                        <label class="navbar-item-link">
                            Đăng Xuất
                        </label>
                    </label>
                </a>
            @else
                <label for="nav-login-logout" class="navbar-item">
                    <label for="nav-login-logout" class="navbar-item-link">
                        <i class="fa-solid fa-user"></i>
                    </label>
                    <label for="nav-login-logout" class="navbar-item-link">
                        <i class="fa-solid fa-caret-down"></i>
                    </label>
                </label>
            @endif

            <li class="navbar-item ">
                <label style="cursor: pointer;" for="nav-input" class="navbar-item-link">
                    <i class="fa-solid fa-bars"></i>
                </label>
            </li>
        </ul>

        <input type="checkbox" hidden class="Notification-input-select" name="" id="Notification-input">
        <label class="nooverlay-Notification" for="Notification-input">
        </label>
        <label for="Notification-input" class="Notification">
            <div class="Notification-img">
                <img
                    src="{{ asset('public/fontend/assets/img/trangchu/icon_notification_empty.svg') }}"alt="">
            </div>
            <div class="Notification-text">
                <span>Không có thông báo nào!</span>
            </div>
        </label>

        <input type="checkbox" hidden class="nav-login-logout-select" name="" id="nav-login-logout">
        <label class="nooverlay-login-logout" for="nav-login-logout">
        </label>
        <div class="nav-login-logout">
            <div class="nav-login-logout-box">
                <label id="dangnhap" for="input-fromlogin" class="nav-login-logout-box-item">
                    <span class="nav-login-logout-box-text">Đăng Nhập</span>
                </label>
                <label id="dangky" for="input-fromsignup" class="nav-login-logout-box-item">
                    <span class="nav-login-logout-box-text">Đăng Ký</span>
                </label>
            </div>
        </div>

        @include('pages.Login_Register.register')
        @include('pages.Login_Register.verycode')
        @include('pages.Login_Register.recoverypw')
        @include('pages.Login_Register.code_confirmation')
        @include('pages.Login_Register.confirmpassword')
        @include('pages.Login_Register.login')

        <input type="checkbox" hidden class="nav-input-select" name="" id="nav-input">
        <label for="nav-input" class="nav-overlay">
        </label>
        <div class="nav-menu">
            <div class="nav-menu-box">
                <label for="nav-input" class="nav-menu-close">
                    <i class="nav-menu-close fa-solid fa-xmark"></i>
                </label>
                <ul>
                    <li class="nav-menu-item"><i style="color: #00b6f3;" class="fa-solid fa-house"></i><span
                            class="nav-menu-item-text">Trang
                            Chủ</span></li>
                    <li class="nav-menu-item nav-menu-item-boder"><i style="color: #00b6f3;"
                            class="fa-solid fa-heart"></i><span class="nav-menu-item-text">Yêu
                            Thích</span>
                    </li>
                    <li class="nav-menu-item"><i style="color: #ffc043;" class="fa-solid fa-hotel"></i><span
                            class="nav-menu-item-text">Khách
                            Sạn</span></li>
                    <li class="nav-menu-item"><i style="color: #ff2890;" class="fa-solid fa-plane"></i><span
                            class="nav-menu-item-text">Vé Máy
                            Bay</span></li>
                    <li class="nav-menu-item"><i style="color: #ff2890;"
                            class="fa-solid fa-hand-holding-heart"></i><span class="nav-menu-item-text">The
                            Memories</span></li>
                    <li class="nav-menu-item"><i style="color: #00b6f3;" class="fa-solid fa-briefcase"></i><span
                            class="nav-menu-item-text">Tour & Sự
                            Kiện</span></li>
                    <li class="nav-menu-item nav-menu-item-boder"><i style="color: #48bb78;"
                            class="fa-solid fa-book"></i><span class="nav-menu-item-text">Cẩm Năng Du
                            Lịch</span></li>
                    <li class="nav-menu-item"><i style="color: #00b6f3;" class="fa-solid fa-briefcase"></i><span
                            class="nav-menu-item-text">Tuyển
                            Dụng</span></li>
                    <li class="nav-menu-item"><i style="color: #00b6f3;"
                            class="fa-solid fa-headphones-simple"></i><span class="nav-menu-item-text">Hỗ
                            Trợ</span></li>
                    <li class="nav-menu-item nav-menu-item-boder"><i style="color: #00b6f3;"
                            class="fa-solid fa-sack-dollar"></i><span class="nav-menu-item-text">Trở Thành
                            Đối Tác Liên Kết</span></li>
                    <li class="nav-menu-item"><i style="color: #00b6f3;" class="fa-solid fa-handshake"></i><span
                            class="nav-menu-item-text">Hợp Tác Với
                            Chúng Tôi</span></li>
                    <li class="nav-menu-item"><i style="color: #00b6f3;" class="fa-solid fa-mobile"></i><span
                            class="nav-menu-item-text">Tải Ứng Dụng
                            MyHotel</span></li>
                    <li class="nav-menu-item"><i style="color: #00b6f3;" class="fa-solid fa-share-nodes"></i><span
                            class="nav-menu-item-text">Giới
                            Thiệu
                            Bạn Bè</span></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="BoxSearch BoxSearch-infohotel">
        <div class="BoxSearch-box">
            <form action="{{ URL::to('tim-kiem') }}" method="GET">
                <div class="BoxSearch-Bottom">
                    <div class="BoxSearch-Bottom-One">
                        <div class="BoxSearch-Bottom-One-Title">
                            Khách sạn
                        </div>
                        <div class="BoxSearch-Bottom-One-Input">
                            <input id="search-hotel-place" class="BoxSearch-Bottom-One-input-size" type="text"
                                placeholder="Tìm Khách Sạn, Khu Vực, Điểm Đến..." name="hotel_name">
                            <div id="result-sreach" class="inputsearchhotel">

                            </div>
                        </div>
                    </div>
                    <div id="loading-schedule" class="BoxSearch-Bottom-Two">
                        {{-- <div class="BoxSearch-Bottom-Two-Go">
                        <div class="BoxSearch-Bottom-Two-Go-Top">
                            Ngày đến
                        </div>
                        <div class="BoxSearch-Bottom-Two-Go-Bottom">
                            <input id="date-start"
                                style="border: none ; font-weight: 550;
                                    line-height: 19px;height: 15px;font-size: 14px"
                                type="text" value="{{ date('d-m-Y') }}">
                        </div>
                    </div>
                    <div class="BoxSearch-Bottom-Two-Time">
                        1 <i class="fa-solid fa-moon"></i>
                    </div>
                    <div class="BoxSearch-Bottom-Two-Back">
                        <div class="BoxSearch-Bottom-Two-Back-Top">
                            Ngày về
                        </div>
                        <div class="BoxSearch-Bottom-Two-Back-Bottom">
                            <input id="date-end"
                                style="border: none ; font-weight: 550;
                                line-height: 19px;height: 15px;font-size: 14px"
                                type="text" value="{{ date('d-m-Y') }}">
                        </div>
                    </div> --}}
                    </div>
                    <div class="BoxSearch-Bottom-Three">
                        <div class="BoxSearch-Bottom-Three-Title">
                            Số phòng, số khách
                        </div>
                        <div class="BoxSearch-Bottom-Three-Lable">
                            <label class="BoxSearch-Bottom-Three-Lable-size" for="">Một phòng, 2 người lớn, 0
                                trẻ
                                em</label>
                        </div>
                    </div>
                    <button type="submit" style="border: 0;background-color: #fff">
                        <div class="BoxSearch-Bottom-BtnSrearch">
                            <div class="BoxSearch-Bottom-BtnSrearch-Box">
                                <i class="fa-solid fa-magnifying-glass BoxSearch-Bottom-BtnSrearch-Box-Icon"></i>
                            </div>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="menumin">
        <div class="menumin-box">
            <ul class="menumin-box-ul">
                <li class="menumin-box-li">Khách Sạn</li>
                <li class="menumin-box-li"><i class="fa-solid fa-angle-right"></i></li>
                <li class="menumin-box-li">Chi Tiết Đơn Hàng</li>
            </ul>
        </div>
    </div>

    <!-- Đánh Giá -->
    <div class="fromorder-box">
        <label class="fromlogin-close btn-info-order">
            <span id="close_box"> <i class="fa-solid fa-xmark"></i></span>
        </label>
        <div class="fromlogin-title">
            <label for=""><span>Đánh Giá</span></label>
        </div>
        <div style="margin-top: 10px;">
            <input class="fromlogin-item-input" type="text" style="width: 96%" name="title_evaluate"
                placeholder="Nhập tiêu đề" required>
        </div>
        <div style="margin-top: 20px;">
            <input class="fromlogin-item-input" type="text" style="width: 96%" name="content_evaluate"
                placeholder="Nhập nội dung" required>
        </div>
        <div style="margin-top: 24px;">
            <label class="fromlogin-title" style="font-size: 16px" for="">Vị Trí</label>
            <div style="display: flex; justify-content: space-between; margin-top: 2px">
                <div>
                    <input type="radio" name="vi-tri" value="5" checked> 5
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="vi-tri" value="4"> 4
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="vi-tri" value="3"> 3
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="vi-tri" value="2"> 2
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="vi-tri" value="1"> 1
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>
        <div style="margin-top: 24px;">
            <label class="fromlogin-title" style="font-size: 16px" for="">Phục Vụ</label>
            <div style="display: flex; justify-content: space-between; margin-top: 2px">
                <div>
                    <input type="radio" name="phuc-vu" value="5" checked> 5
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="phuc-vu" value="4"> 4
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="phuc-vu" value="3"> 3
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="phuc-vu" value="2"> 2
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="phuc-vu" value="1"> 1
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>
        <div style="margin-top: 24px;">
            <label class="fromlogin-title" style="font-size: 16px" for="">Giá Cả</label>
            <div style="display: flex; justify-content: space-between; margin-top: 2px">
                <div>
                    <input type="radio" name="gia-ca" value="5" checked> 5
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="gia-ca" value="4"> 4
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="gia-ca" value="3"> 3
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="gia-ca" value="2"> 2
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="gia-ca" value="1"> 1
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>
        <div style="margin-top: 24px;">
            <label class="fromlogin-title" style="font-size: 16px" for="">Vệ Sinh</label>
            <div style="display: flex; justify-content: space-between; margin-top: 2px">
                <div>
                    <input type="radio" name="ve-sinh" value="5" checked> 5
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="ve-sinh" value="4"> 4
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="ve-sinh" value="3"> 3
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="ve-sinh" value="2"> 2
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="ve-sinh" value="1"> 1
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>
        <div style="margin-top: 24px;">
            <label class="fromlogin-title" style="font-size: 16px" for="">Tiện Nghi</label>
            <div style="display: flex; justify-content: space-between; margin-top: 2px">
                <div>
                    <input type="radio" name="tien-nghi" value="5" checked> 5
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="tien-nghi" value="4"> 4
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="tien-nghi" value="3"> 3
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="tien-nghi" value="2"> 2
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
                <div>
                    <input type="radio" name="tien-nghi" value="1"> 1
                    <i style="color: #ffc043" class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>
        <input class="fromlogin-btn " type="submit" style="display: block; margin: auto; margin-top: 16px;"
            value="Xác Nhận" id="btn-submit-evaluate">
    </div>
    <div class="showimg-overlay"></div>



    <!-- Chi Tiết Đơn -->
    <div class="conatiner_info-order">
        @if ($order != '')
            @foreach ($order as $v_order)
                <h3>Chi Tiết Đơn Đặt Phòng : {{ $v_order->order_code }}</h3>
                <div class="info-order">
                    <div class="info-order-left">
                        <div class="info-order-left-box">
                            <img src="{{ asset('public/fontend/assets/img/hotel/' . $v_order->orderdetails->hotel->hotel_image) }}"
                                height="214px" alt="" srcset="">
                            <div class="info-order-left-item">
                                <h4 class="info-order-left-item-name"> {{ $v_order->orderdetails->hotel_name }}</h4>
                                <div class="info-order-left-item-rating">
                                    @for ($i = 1; $i <= $v_order->orderdetails->hotel->hotel_rank; $i++)
                                        <i style="margin-left: 3px" class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                                <div class="info-order-left-item-address">
                                    <svg width="16" height="16" fill="none">
                                        <path d="M8.467 3.8V2a1 1 0 00-1-1h-.8a1 1 0 00-1 1v1.8" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path
                                            d="M1 7.467a1 1 0 001 1h9.838a1 1 0 00.64-.232l1.6-1.333a1 1 0 000-1.537l-1.6-1.333a1 1 0 00-.64-.232H2a1 1 0 00-1 1v2.667zM5.667 10.333V14a1 1 0 001 1h.8a1 1 0 001-1v-3.667"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        </path>
                                    </svg>
                                    <span>{{ $v_order->orderdetails->hotel->area->area_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $folder_gallary_room = preg_replace('/\s+/', '', $v_order->orderdetails->room_name);
                    @endphp
                    <div class="info-order-right">
                        <div class="info-order-right-box">
                            <div class="info-order-left-item">
                                <img height="214px"
                                    src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $v_order->orderdetails->room->galleryroom->gallery_room_image) }}'
                                    alt="">
                            </div>
                            <div class="info-order-right-item">
                                <h4 class="info-order-right-item-room-name">{{ $v_order->orderdetails->room_name }}
                                </h4>
                                <div class="info-order-right-item-detail">
                                    <div>Ngày Đặt Phòng: {{ $v_order->created_at }}</div>
                                    <div>Ngày Đến: {{ $v_order->start_day }}</div>
                                    <div>Ngày Đi: {{ $v_order->end_day }}</div>
                                    <div class="">Giá Phòng:
                                        {{ number_format($v_order->orderdetails->price_room, 0, ',', '.') }} đ</div>
                                    <div class="">Phí Khách Sạn:
                                        {{ number_format($v_order->orderdetails->hotel_fee, 0, ',', '.') }} đ</div>
                                    @if ($v_order->coupon_name_code == 'Không Có')
                                        <div class="info-order-right-item-price-total">Tổng Thanh Toán:
                                            {{ number_format($v_order->orderdetails->price_room + $v_order->orderdetails->hotel_fee, 0, ',', '.') }}
                                            đ</div>
                                    @else
                                        <div class="info-order-right-item-coupon">Mã Giảm Giá: <span
                                                style="color: #00B6F3">{{ $v_order->coupon_name_code }}</span></div>
                                        <div class="">Số Tiền Giảm:
                                            {{ number_format($v_order->coupon_sale_price, 0, ',', '.') }} đ</div>
                                        <div class="info-order-right-item-price-total">Tổng Thanh Toán:
                                            {{ number_format($v_order->orderdetails->price_room + $v_order->orderdetails->hotel_fee - $v_order->coupon_sale_price, 0, ',', '.') }}
                                            đ</div>
                                    @endif


                                    <div class="">Tình Trạng Thanh Toán: @if ($v_order->payment->payment_method == 4)
                                            Khi Đến Nhận Phòng
                                        @elseif ($v_order->payment->payment_method == 1)
                                            MomoPay
                                            @endif - @if ($v_order->payment->payment_status == 0)
                                                Chưa Thanh Toán
                                            @else
                                                Đã Thanh Toán
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="info-customer">
                            <div class="info-customer-left">
                                <div class="info-customer-left-name">Thông Tin Khách Hàng</div>
                                @if ($v_order->orderer->customer != null)
                                    <div>Tên Khách Hàng: {{ $v_order->orderer->customer->customer_name }}</div>
                                    <div>Số Điện Thoại: {{ $v_order->orderer->customer->customer_phone }}</div>
                                    <div>Email: {{ $v_order->orderer->customer->customer_email }}</div>
                                @else
                                    <div>Tên Khách Hàng: {{ $v_order->orderer->orderer_name }}</div>
                                    <div>Số Điện Thoại: {{ $v_order->orderer->orderer_phone }}</div>
                                    <div>Email: {{ $v_order->orderer->orderer_email }}</div>
                                @endif

                            </div>
                            <div class="info-customer-right loading-status{{ $v_order->order_code }}">

                            </div>

                            <script>
                                var order_code = '{{ $v_order->order_code }}';
                                loading_order_status(order_code);

                                function loading_order_status(order_code) {
                                    $.ajax({
                                        url: '{{ url('/kiem-tra-don-hang/loading-order-status') }}',
                                        method: 'GET',
                                        data: {
                                            order_code: order_code,
                                        },
                                        success: function(data) {
                                            $('.loading-status' + order_code).html(data);
                                        },
                                        error: function() {
                                            alert("Bug Huhu :<<");
                                        }
                                    })
                                }
                            </script>

                        </div>
                    </div>
                </div>
            @endforeach
        @endif



    </div>


    <div class="infohotel">
        <div class="BoxNhapSDT">
            <div class="BoxNhapSDT-left">
                <div class="BoxNhapSDT-left-img">
                    <img src=" {{ asset('public/fontend/assets/img/trangchu/icon_mail_red.svg') }}" alt="">
                </div>
                <div class="BoxNhapSDT-left-text">
                    <div class="BoxNhapSDT-left-text-title">
                        <span>Bạn muốn tiết kiện 50% khi đặt phòng khách sạn ?</span>
                    </div>
                    <div class="BoxNhapSDT-left-text-content">
                        <span>Nhập số điện thoại để MyHotel có thể gửi đến bạn những chương trình khuyến mại mới
                            nhất!</span>
                    </div>
                </div>
            </div>
            <div class="BoxNhapSDT-right">
                <div class="BoxNhapSDT-right-Groupinput">
                    <div class="BoxNhapSDT-right-input">
                        <input type="text" placeholder="Nhập số điện thoại">
                    </div>
                    <label class="BoxNhapSDT-right-lable" for="">
                        <span>Đăng ký</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="infohotel_logo">
            <img width="185px" height="55px" style="object-fit: cover;"
                src="{{ asset('public/fontend/assets/img/config/' . $config_logo_web->config_image) }}"
                alt="{{ $config_logo_web->config_content }}">
        </div>
        <div class="infohotel_content">
            <div class="infohotel_content_box">
                <div class="infohotel_content_title">
                    {{ $company_config->company_name }}
                </div>
                <div class="infohotel_content_text">
                    Tổng đài chăm sóc: {{ $company_config->company_hostline }} <br>
                    Email: {{ $company_config->company_mail }} <br>
                    Văn phòng Đà Nẵng: {{ $company_config->company_address }}<br>
                </div>
            </div>
            <div class="infohotel_content_box">
                <div class="infohotel_content_title">
                    Chính sách & Quy định
                </div>
                <div class="infohotel_content_text">
                    Điều khoản và điều kiện <br>
                    Quy định về thanh toán <br>
                    Quy định về xác nhận thông tin đặt phòng <br>
                    Chính sách về hủy đặt phòng và hoàn trả tiền <br>
                    Chính sách bảo mật thông tin <br>
                    Quy chế hoạt động <br>
                    Quy trình giải quyết tranh chấp, khiếu nại <br>
                    Điều lệ bay quốc nội <br>
                </div>
            </div>
            <div class="infohotel_content_box">
                <div class="infohotel_content_title">
                    Khách hàng và đối tác
                </div>
                <div class="infohotel_content_text">
                    Đăng nhập HMS <br>
                    Tuyển dụng <br>
                    Liên hệ <br>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="footer-content">
            <div class="footer-content">
                {{ $company_config->company_slogan }}
            </div>
            <div class="footer-content">
                @foreach ($config_brand_web as $brand_web)
                    <img src="{{ asset('public/fontend/assets/img/config/' . $brand_web->config_image) }}"
                        alt="{{ $brand_web->config_content }}" alt="">
                @endforeach
            </div>
            <div class="footer-content">
                {{ $company_config->company_copyright }}
            </div>
        </div>
    </div>

    <script src=" {{ asset('public/fontend/assets/js/base.js') }}"></script>


    <script>
        $(document).ready(function() {
            $(window).scroll(function() {
                if ($(this).scrollTop()) {
                    $('.BoxSearch-infohotel').addClass('BoxSearch-fixed');
                } else {
                    $('.BoxSearch-infohotel').removeClass('BoxSearch-fixed');
                }
            });
        });
    </script>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src=" {{ asset('public/fontend/assets/owlcarousel/owl.carousel.min.js') }}"></script>
    <!-- Messenger Plugin chat Code -->
    <div id="fb-root"></div>

    <!-- Your Plugin chat code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
        var chatbox = document.getElementById('fb-customer-chat');
        chatbox.setAttribute("page_id", "106394162332096");
        chatbox.setAttribute("attribution", "biz_inbox");
    </script>

    <!-- Your SDK code -->
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                xfbml: true,
                version: 'v15.0'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <script>
        $(document).on('click', '#boxdanhgia', function() {
            $('.fromorder-box').show();
            $('.showimg-overlay').show();
            var order_code = $(this).data('order_code');

            $('#btn-submit-evaluate').click(function() {
                var title_evaluate = $("input[name='title_evaluate']").val();
                var content_evaluate = $("input[name='content_evaluate']").val();
                var loaction_point = $('input[type="radio"][name="vi-tri"]:checked').val();
                var service_point = $('input[type="radio"][name="phuc-vu"]:checked').val();
                var price_point = $('input[type="radio"][name="gia-ca"]:checked').val();
                var sanitary_point = $('input[type="radio"][name="ve-sinh"]:checked').val();
                var convenient_point = $('input[type="radio"][name="tien-nghi"]:checked').val();
                if (title_evaluate == '' || content_evaluate == '' || order_code == '') {
                    message_toastr("warning", "Vui Lòng Điền Đầy Đủ Thông Tin",
                        "Cảnh Báo !");
                } else {
                    $.ajax({
                        url: '{{ url('kiem-tra-don-hang/insert-evaluate') }}',
                        method: 'GET',
                        data: {
                            order_code: order_code,
                            title_evaluate: title_evaluate,
                            content_evaluate: content_evaluate,
                            loaction_point: loaction_point,
                            service_point: service_point,
                            price_point: price_point,
                            sanitary_point: sanitary_point,
                            convenient_point: convenient_point,
                        },
                        success: function(data) {
                            if (data == 'true') {
                                message_toastr("success", "Đánh Giá Thành Công !",
                                    "Thành Công !");
                                $('.fromorder-box').hide();
                                $('.showimg-overlay').hide();
                                $("input[name='title_evaluate']").val('');
                                $("input[name='content_evaluate']").val('');
                                loading_order_status(order_code);
                            }
                        },
                        error: function() {
                            alert("Bug Huhu :<<");
                        }
                    })
                }
            });
        });

        $('#close_box').click(function() {
            $('.fromorder-box').hide();
            $('.showimg-overlay').hide();
        });
        $('.showimg-overlay').click(function() {
            $('.fromorder-box').hide();
            $('.showimg-overlay').hide();
        });
    </script>

    

    <script>
        $(document).on('click', '#btn-cancel-order', function() {
            var order_code = $(this).data('order_code');
            $.ajax({
                url: '{{ url('kiem-tra-don-hang/cancel-order') }}',
                method: 'GET',
                data: {
                    order_code: order_code,
                },
                success: function(data) {
                    if (data == 'true') {
                        message_toastr("success", "Hủy Đơn Thành Công !", "Thành Công !");
                        loading_order_status(order_code);
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });
    </script>

    {{-- Loading Hành Trình --}}
    <script>
        loading_schedule();

        function loading_schedule() {
            $.ajax({
                url: '{{ url('/trang-chu/loading-schedule') }}',
                method: 'GET',
                data: {

                },
                success: function(data) {
                    $('#loading-schedule').html(data);
                    // Table Ngày Tháng Năm
                    $("#date-start").datepicker({
                        prevText: "Tháng Trước",
                        nextText: "Tháng Sau",
                        dateFormat: "dd-mm-yy",
                        dayNamesMin: ["Thứ 2 ", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7",
                            "Chủ Nhật"
                        ],
                        duration: "slow",
                    });
                    $("#date-end").datepicker({
                        prevText: "Tháng Trước",
                        nextText: "Tháng Sau",
                        dateFormat: "dd-mm-yy",
                        dayNamesMin: ["Thứ 2 ", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7",
                            "Chủ Nhật"
                        ],
                        duration: "slow",
                    });
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>
    {{-- Cập Nhật Hành Trình --}}
    <script>
        $(document).on('change', '#date-start', function() {
            var date_start = $(this).val();
            var date_end = $('#date-end').val();
            $.ajax({
                url: '{{ url('trang-chu/update-schedule') }}',
                method: 'GET',
                data: {
                    date_start: date_start,
                    date_end: date_end,
                },
                success: function(data) {
                    loading_schedule();
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });

        $(document).on('change', '#date-end', function() {
            var date_start = $('#date-start').val();
            var date_end = $(this).val();
            $.ajax({
                url: '{{ url('trang-chu/update-schedule') }}',
                method: 'GET',
                data: {
                    date_start: date_start,
                    date_end: date_end,
                },
                success: function(data) {
                    loading_schedule();
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });
    </script>
    {{-- Tìm Kiếm Khách Sạn , Địa Điểm  --}}
    <script>
        $('#search-hotel-place').keyup(function() {
            var key = $(this).val();

            $.ajax({
                url: '{{ url('khach-san/sreach-hotel-place') }}',
                method: 'GET',
                data: {
                    key: key,
                },

                success: function(data) {
                    $('#result-sreach').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        })
    </script>


</body>

</html>
