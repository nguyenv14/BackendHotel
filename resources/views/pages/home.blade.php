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

    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/trangchu.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/resTrangchu.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/owlcarousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href=" {{ asset('public/fontend/assets/owlcarousel/assets/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    {{-- jquery --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
        integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- jquery UI --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- jquery CSS UI --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                // alert("Bug Huhu :<<");
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

</head>

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

@php
    $user = Session::get('user');
@endphp

<body class="preloading">
    <div class="overlay-loading"></div>
    <div class="load">
        <img src="{{ asset('public/fontend/assets/img/loader.gif') }}" alt="">
    </div>
    <div class="loading">
        <img src="{{ asset('public/fontend/assets/img/loader.gif') }}" alt="">
    </div>
    <div class="overlay-loading"></div>
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

    <script>
        $('.banner-close').click(function() {
            $('.banner-overlay').hide();
            $('.banner').hide();
        });
        $('.banner-overlay').click(function() {
            $('.banner-overlay').hide();
            $('.banner').hide();
        });
    </script>

    <div class="header">
        <div class="videoContainer">
            <video type="video/mp4" autoplay="" muted="" loop="">
                <source src="https://storage.googleapis.com/tripi-assets/mytour/videos/video_bg_mytour.mov" />
                Your browser does not support the video tag.
            </video>

            <div class="ContentInVideo">

                <nav class="navbar">

                    <div class="navbar-logo">
                        <a class="navbar-item-link" href="{{ URL::to('/') }}"> <img
                                style="width: 130px; height: 45px;object-fit: cover;"
                                src="{{ asset('public/fontend/assets/img/config/' . $config_logo_web->config_image) }}"
                                alt="{{ $config_logo_web->config_content }}">
                        </a>
                    </div>

                    <ul class="navbar-list .navbar-list--left">
                        <li class="navbar-item res-navbar-item-589">
                            <a class="navbar-item-link" href="">
                                <i class="fa-solid fa-house"></i>
                            </a>
                            <a class="navbar-item-link res-navbar-item-589" href="{{ URL::to('/') }}"><span>Trang
                                    Chủ</span></a>
                        </li>
                        <li class="navbar-item">
                            <a class="navbar-item-link res-navbar-item-589"
                                href="{{ URL::to('/khach-san') }}"><span>Khách
                                    Sạn</span></a>
                        </li>
                        <li class="navbar-item ">
                            <a class="navbar-item-link res-navbar-item-768" href=""><span>Homestay</span></a>
                        </li>
                        <li class="navbar-item res-navbar-item-1120">
                            <a class="navbar-item-link" href=""><span>Nhà Hàng</span></a>
                        </li>
                        <li class="navbar-item res-navbar-item-1230">
                            <a class="navbar-item-link" href=""><span>Đặc Sản - Myfresh</span></a>
                        </li>
                        <li class="navbar-item res-navbar-item-1297">
                            <a class="navbar-item-link" href=""><span>Tour & Sự Kiện</span></a>
                        </li>
                    </ul>

                    <ul class="navbar-list navbar-list--right">
                        {{-- <li class="navbar-item res-navbar-item-1120">
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
                            <a class="navbar-item-link" href="{{ URL::to('/kiem-tra-don-hang') }}">
                                Kiểm Tra Đơn Hàng
                            </a>
                        </li>

                        <li class="navbar-item res-navbar-item-589">
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

                    <input type="checkbox" hidden class="Notification-input-select" name=""
                        id="Notification-input">
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

                    <input type="checkbox" hidden class="nav-login-logout-select" name=""
                        id="nav-login-logout">
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

                    @BannerADS
                        @if ($BannerADS != null)
                            @php
                                setcookie('ADS_BANNER', true, time() + 30);
                            @endphp
                            <div class="banner-overlay"></div>
                            <div class="bannerADS">
                                <div class="banner-image">
                                    <div class="banner-close">
                                        <span> <i class="fa-solid fa-xmark"></i></span>
                                    </div>
                                    <a href="{{ $BannerADS->bannerads_link }}">

                                        <img src=" {{ asset('public/fontend/assets/img/bannerads/' . $BannerADS->bannerads_image) }}"
                                            alt="">
                                    </a>
                                </div>
                                <div class="banner-text">
                                    <div class="banner-title">
                                        <span>{{ $BannerADS->bannerads_title }}</span>
                                    </div>
                                    <div class="banner-text">
                                        <span>{{ $BannerADS->bannerads_desc }}</span>
                                    </div>
                                </div>
                                <a href="{{ $BannerADS->bannerads_link }}">
                                    <div class="banner-button">
                                        <div class="banner-btn">
                                            <span>Xem Thêm</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <script>
                                $('.banner-close').click(function() {
                                    $('.banner-overlay').hide();
                                    $('.bannerADS').hide();
                                });
                                $('.banner-overlay').click(function() {
                                    $('.banner-overlay').hide();
                                    $('.bannerADS').hide();
                                });
                            </script>
                        @endif
                    @endBannerADS

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
                                <li class="nav-menu-item"><i style="color: #00b6f3;"
                                        class="fa-solid fa-house"></i><span class="nav-menu-item-text">Trang
                                        Chủ</span></li>
                                <li class="nav-menu-item nav-menu-item-boder"><i style="color: #00b6f3;"
                                        class="fa-solid fa-heart"></i><span class="nav-menu-item-text">Yêu
                                        Thích</span>
                                </li>
                                <li class="nav-menu-item"><i style="color: #ffc043;"
                                        class="fa-solid fa-hotel"></i><span class="nav-menu-item-text">Khách
                                        Sạn</span></li>
                                <li class="nav-menu-item"><i style="color: #ff2890;"
                                        class="fa-solid fa-plane"></i><span class="nav-menu-item-text">Vé Máy
                                        Bay</span></li>
                                <li class="nav-menu-item"><i style="color: #ff2890;"
                                        class="fa-solid fa-hand-holding-heart"></i><span
                                        class="nav-menu-item-text">The
                                        Memories</span></li>
                                <li class="nav-menu-item"><i style="color: #00b6f3;"
                                        class="fa-solid fa-briefcase"></i><span class="nav-menu-item-text">Tour & Sự
                                        Kiện</span></li>
                                <li class="nav-menu-item nav-menu-item-boder"><i style="color: #48bb78;"
                                        class="fa-solid fa-book"></i><span class="nav-menu-item-text">Cẩm Năng Du
                                        Lịch</span></li>
                                <li class="nav-menu-item"><i style="color: #00b6f3;"
                                        class="fa-solid fa-briefcase"></i><span class="nav-menu-item-text">Tuyển
                                        Dụng</span></li>
                                <li class="nav-menu-item"><i style="color: #00b6f3;"
                                        class="fa-solid fa-headphones-simple"></i><span class="nav-menu-item-text">Hỗ
                                        Trợ</span></li>
                                <li class="nav-menu-item nav-menu-item-boder"><i style="color: #00b6f3;"
                                        class="fa-solid fa-sack-dollar"></i><span class="nav-menu-item-text">Trở Thành
                                        Đối Tác Liên Kết</span></li>
                                <li class="nav-menu-item"><i style="color: #00b6f3;"
                                        class="fa-solid fa-handshake"></i><span class="nav-menu-item-text">Hợp Tác Với
                                        Chúng Tôi</span></li>
                                <li class="nav-menu-item"><i style="color: #00b6f3;"
                                        class="fa-solid fa-mobile"></i><span class="nav-menu-item-text">Tải Ứng Dụng
                                        MyHotel</span></li>
                                <li class="nav-menu-item"><i style="color: #00b6f3;"
                                        class="fa-solid fa-share-nodes"></i><span class="nav-menu-item-text">Giới
                                        Thiệu
                                        Bạn Bè</span></li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="BoxSearch">
                    <div class="BoxSearch-title">
                        <ul class="BoxSearch-list">
                            <li class="BoxSearch-item">
                                <a class="BoxSearch-link" href=""><i class="fa-solid fa-hotel"></i></a>
                                <a class="BoxSearch-link" href="">Khách Sạn</a>
                            </li>
                            <li class="BoxSearch-item">
                                <a class="BoxSearch-link" href=""><i class="fa-solid fa-plane"></i></a>
                                <a class="BoxSearch-link" href="">Chuyến Bay</a>
                            </li>
                            <li class="BoxSearch-item">
                                <a class="BoxSearch-link" href=""><i class="fa-solid fa-utensils"></i></a>
                                <a class="BoxSearch-link" href="">Nhà Hàng</a>
                            </li>
                            <li class="BoxSearch-item">
                                <a class="BoxSearch-link" href=""><i
                                        class="fa-solid fa-house-chimney"></i></a>
                                <a class="BoxSearch-link" href="">Biệt Thự</a>
                            </li>
                        </ul>
                    </div>
                    <form action="{{ URL::to('tim-kiem') }}" method="GET">
                        <div class="BoxSearch-Bottom">
                            <div class="BoxSearch-Bottom-One">
                                <div class="BoxSearch-Bottom-One-Title">
                                    Địa Điểm
                                </div>
                                <div class="BoxSearch-Bottom-One-Input">
                                    <input id="search-hotel-place" class="BoxSearch-Bottom-One-input-size"
                                        type="text" placeholder="Khách Sạn, Địa Điểm, Điểm Đến" name="hotel_name">
                                    <div id="result-sreach" class="inputsearchhotel">

                                    </div>
                                </div>
                            </div>
                            <div id="loading-schedule" class="BoxSearch-Bottom-Two">

                            </div>
                            <div class="BoxSearch-Bottom-Three">
                                <div class="BoxSearch-Bottom-Three-Title">
                                    Số phòng, số khách
                                </div>
                                <div class="BoxSearch-Bottom-Three-Lable">
                                    <label class="BoxSearch-Bottom-Three-Lable-size" for="">Một phòng, 2 người
                                        lớn, 0 trẻ
                                        em</label>
                                </div>
                            </div>

                            <button type="submit" style="border: 0;background-color: #fff">
                                <div class="BoxSearch-Bottom-BtnSrearch">
                                    <div class="BoxSearch-Bottom-BtnSrearch-Box">
                                        <i
                                            class="fa-solid fa-magnifying-glass BoxSearch-Bottom-BtnSrearch-Box-Icon"></i>
                                    </div>
                                </div>
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="slider">
        <div class="slider-box">
            <div class="slider-js owl-carousel owl-theme">
                @foreach ($sliders as $slider)
                    <div class="item">
                        <img width="465px" height="195px" style="object-fit: cover;border-radius: 8px;"
                            src="{{ URL::to('public/fontend/assets/img/slider/' . $slider->slider_image) }}"
                            alt="{{ $slider->slider_desc }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container">
        <div class="boxcontent">
            @foreach ($config_slogan_web as $slogan_image)
                <div class="boxcontent-layout">
                    <div class="boxcontent-img">
                        <img src="{{ asset('public/fontend/assets/img/config/' . $slogan_image->config_image) }}"
                            alt="">
                    </div>
                    <div class="boxcontent-text">
                        <div class="boxcontent-text-one">
                            {{ $slogan_image->config_title }}
                        </div>
                        <div class="boxcontent-text-two">
                            {{ $slogan_image->config_content }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="contentflashsale">

        <div class="contentboxflashsale">
            <div class="flashsaletop-content">
                <div class="flashsaletop-left">
                    <div class="flashsaletop-left-img">
                        <img style="object-fit:cover ;" width="198px" height="44px"
                            src="{{ asset('public/fontend/assets/img/trangchu/sale/icon_flashSale_home_white_new.png') }}"
                            alt="">
                    </div>
                    <div class="flashsaletop-left-text chunhay">
                        Chương trình sẽ diễn ra trong : 2 ngày
                    </div>
                </div>
            </div>

            <div class="flashsaletop-right-content">
                <div class="flashsaletop-right res-flashsaletop-right-top-1213">
                    <div class="flashsaletop-right-top ">
                        12:00-13:00
                    </div>
                    <div class="flashsaletop-right-bottom">
                        Đã kết thúc
                    </div>
                </div>
                <div class="flashsaletop-right res-flashsaletop-right-top-902">
                    <div class="flashsaletop-right-top">
                        15:00-16:00
                    </div>
                    <div class="flashsaletop-right-bottom">
                        Đã kết thúc
                    </div>
                </div>
                <div class="flashsaletop-right">
                    <div class="flashsaletop-right-top">
                        21:00-22:00
                    </div>
                    <div class="flashsaletop-right-bottom">
                        Sắp diễn ra
                    </div>
                </div>
                <div class="flashsaletop-right">
                    <div class="flashsaletop-right-top">
                        09:00-10:00
                    </div>
                    <div class="flashsaletop-right-bottom">
                        11/5
                    </div>
                </div>
                <div class="flashsaletop-right res-flashsaletop-right">
                    <div class="flashsaletop-right-top">
                        12:00-13:00
                    </div>
                    <div class="flashsaletop-right-bottom">
                        11/5
                    </div>
                </div>
            </div>
        </div>

        <div class="flashsalehotel-card-box">
            <div class="flashsalehotel flashsalehotel-js owl-carousel owl-theme">
                @foreach ($hotel_flashsale as $hotel)
                    <a href="{{ URL::to('khach-san-chi-tiet?hotel_id=' . $hotel->hotel_id) }}" class="flashsalehotel_boxcontent_hover">
                        <div class="flashsalehotel_boxcontent item">
                            <div class="flashsalehotel_boxcontent_img_text">
                                <div class="flashsalehotel_img-box">
                                    <img class="flashsalehotel_img" width="284px" height="160px"
                                        style="object-fit: cover;" src="{{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }}"
                                        alt="">
                                </div>
                                <div class="flashsalehotel_text">
                                    <div class="flashsalehotel_text-title">
                                        {{ $hotel->hotel_name }}
                                    </div>
                                    <div class="flashsalehotel_text-star">
                                        @for ($i = 0; $i < $hotel->hotel_rank; $i++)
                                        <i class="fa-solid fa-star"></i>
                                        @endfor
                                    </div>
                                    <div class="flashsalehotel_place">
                                        <div>
                                            <i class="fa-solid fa-location-dot"></i>
                                            Quận {{ $hotel->area->area_name }}
                                        </div>
                                    </div>
                                    <div class="flashsalehotel_text-evaluate">
                                        <?php echo $hotel->evaluate($hotel->hotel_id); ?>
                                    </div>
                                    <?php echo $hotel->order_time($hotel->hotel_id); ?>
                                    <div class="flashsalehotel_text-box-price">
                                        <div class="flashsalehotel_text-box-price-one">
                                            <span>{{ number_format($hotel->type_room_price,0,',','.') }}đ</span>
                                        </div>
                                        @php
                                        $price_sale = $hotel->type_room_price - ($hotel->type_room_price / 100) * $hotel->type_room_price_sale;
                                        @endphp
                                        <div class="flashsalehotel_text-box-price-two">
                                            <span>{{ number_format($price_sale,0,',','.') }}</span>
                                        </div>
                                        @php
                                            $coupon_rd = array_rand($coupons->toarray());
                                        @endphp
                                       <div class="flashsalehotel_text-box-price-three bordernhay">
                                            <div class="flashsalehotel_text-box-price-three-l">
                                                <div class="flashsalehotel_text-box-price-three-l-1"><span>Mã : </span>
                                                </div>
                                                <div class="flashsalehotel_text-box-price-three-l-2">
                                                    <span>{{ $coupons[$coupon_rd]->coupon_name_code }}</span>
                                                </div>
                                                <div class="flashsalehotel_text-box-price-three-l-3">-{{ $coupons[$coupon_rd]->coupon_price_sale }}%</div>
                                            </div>
                                            @php
                                            $price_sale_end = $price_sale - ($price_sale / 100) * $coupons[$coupon_rd]->coupon_price_sale;
                                            @endphp
                                            <div class="flashsalehotel_text-box-price-three-r chunhay">
                                                <span>{{ number_format($price_sale_end,0,',','.') }}đ</span>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach


            </div>

            <a href="#">
                <div class="flashsalehotel-button">
                    <div class="flashsalehotel_btn">
                        <span>Xem Thêm</span>
                    </div>
                </div>
            </a>

        </div>
    </div>

    <div class="place">
        <div class="box-place">
            <div class="places-title">
                Các Khu Vực Trong Đà Nẵng
            </div>
            <div class="places-layout">
                @foreach ($areas as $area)
                    <a class="places-layout-item" href="{{ URL::to('tim-kiem?area_id=' . $area->area_id) }}">
                        <div class="places-layout-img">
                            <img src="{{ asset('public/fontend/assets/img/area/' . $area->area_image) }}"
                                alt="{{ $area->area_desc }}">
                        </div>
                        <span class="places-layout-item-text">{{ $area->area_name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="inspiration">
        <div class="inspiration-box">
            <div class="inspiration-box-top">
                <div class="inspiration-box-top-title">
                    <span>Cảm hứng cho những chuyến đi</span>
                </div>
                <div class="inspiration-box-top-text">
                    <span>Bí quyết du lịch, những câu chuyện thú vị đang chờ đón bạn</span>
                </div>
            </div>
            <div class="inspiration-box-bottom">
                <div class="inspiration-box-bottom-left">
                    <a href="">
                        <div class="inspiration-box-bottom-left-zoom">
                            <div class="inspiration-box-bottom-left-img">
                                <img width="586px" height="380px" style="object-fit: cover;border-radius: 8px;"
                                    src="{{ asset('public/fontend/assets/img/trangchu/camhung/1.jpg') }}"
                                    alt="">
                            </div>
                        </div>
                        <div class="inspiration-box-bottom-left-text">
                            <span>Top 5 Điểm Cắm Trại Quanh Hà Nội Siêu Hấp Dẫn Phải Rủ “Cạ Cứng” Đi Ngay</span>
                        </div>
                    </a>
                </div>
                <div class="inspiration-box-bottom-right">
                    <a href="">
                        <div class="inspiration-box-bottom-right-item">
                            <div class="inspiration-box-bottom-right-item-zoom">
                                <div class="inspiration-box-bottom-right-item-img">
                                    <img width="285px" height="152px" style="object-fit: cover;border-radius: 8px;"
                                        src="{{ asset('public/fontend/assets/img/trangchu/camhung/2.jpg') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="inspiration-box-bottom-right-item-text">
                                <span>Du Lịch Tại Chỗ - Xu Hướng Du Lịch Mùa Covid</span>
                            </div>
                        </div>
                    </a>
                    <a href="">
                        <div class="inspiration-box-bottom-right-item">
                            <div class="inspiration-box-bottom-right-item-zoom">
                                <div class="inspiration-box-bottom-right-item-img">
                                    <img width="285px" height="152px" style="object-fit: cover;border-radius: 8px;"
                                        src="{{ asset('public/fontend/assets/img/trangchu/camhung/3.jpg') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="inspiration-box-bottom-right-item-text">
                                <span>Điểm du lịch Pleiku khiến bạn muốn xách balo lên và đi ngay</span>
                            </div>
                        </div>
                    </a>
                    <a href="">
                        <div class="inspiration-box-bottom-right-item">
                            <div class="inspiration-box-bottom-right-item-zoom">
                                <div class="inspiration-box-bottom-right-item-img">
                                    <img width="285px" height="152px" style="object-fit: cover;border-radius: 8px;"
                                        src="{{ asset('public/fontend/assets/img/trangchu/camhung/4.jpg') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="inspiration-box-bottom-right-item-text">
                                <span>Kết hợp du lịch và chụp ảnh cưới, The Memories liệu có gỡ rối cho...</span>
                            </div>
                        </div>
                    </a>
                    <a href="">
                        <div class="inspiration-box-bottom-right-item">
                            <div class="inspiration-box-bottom-right-item-zoom">
                                <div class="inspiration-box-bottom-right-item-img">
                                    <img width="285px" height="152px" style="object-fit: cover;border-radius: 8px;"
                                        src="{{ asset('public/fontend/assets/img/trangchu/camhung/5.jpg') }}"
                                        alt="">

                                </div>
                            </div>
                            <div class="inspiration-box-bottom-right-item-text">
                                <span>Sáng tạo hay là chết, Myhotel liều lĩnh mở tour trải nghiệm khám phá</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
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
        <div class="infohotel-box">
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

    <script src=" {{ asset('public/fontend/assets/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.1/TweenMax.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
        integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src=" {{ asset('public/fontend/assets/js/base.js') }}"></script>
    <script src=" {{ asset('public/fontend/assets/js/trangchu.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        new WOW().init();
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-248734486-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-248734486-1');
    </script>

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
                    // alert("Bug Huhu :<<");
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
                    // alert("Bug Huhu :<<");
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
                    // alert("Bug Huhu :<<");
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
                    // alert("Bug Huhu :<<");
                }
            })
        })
    </script>

</body>

</html>
