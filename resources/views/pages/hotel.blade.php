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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/khachsan.css') }}">
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

        .backgroud_banner {
            background-image: url({{ asset('public/fontend/assets/img/khachsan/banner_hotel.jpg') }});
        }

        .trendinghotel_img {
            width: 284px;
            height: 160px;
            background-size: 100%;
            border-radius: 10px;
        }
    </style>
</head>

@php
    $user = Session::get('user');
@endphp

<body class="preloading">
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
    <div class="banner">
        <div class="backgroud_banner">
            <div>
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
                            <a class="navbar-item-link" href="{{ URL::to('/kiem-tra-don-hang') }}">
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
                        <span class="BoxSearch-title-style">Khách sạn</span>
                    </div>
                    <form action="{{ URL::to('tim-kiem') }}" method="GET">
                        <div class="BoxSearch-Bottom">
                            <div class="BoxSearch-Bottom-One">
                                <div class="BoxSearch-Bottom-One-Title">
                                    Địa Điểm
                                </div>
                                <div class="BoxSearch-Bottom-One-Input">
                                    <input id="search-hotel-place" class="BoxSearch-Bottom-One-input-size"
                                        type="text" placeholder="Khách Sạn, Địa Điểm, Điểm Đến"
                                        style="margin-top: 8px" name="hotel_name">
                                    <div id="result-sreach" class="inputsearchhotel">

                                    </div>
                                </div>
                            </div>


                            <div id="loading-schedule" class="BoxSearch-Bottom-Two">
                                {{-- Loading Hành Trình --}}
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
                            alt=" {{ $slogan_image->config_title }}">
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
    <div class="recentlyviewed">
        <div class="recentlyviewed_box">
            <div class="recentlyviewed_title">
                <span class="recentlyviewed_title">Xem Gần Đây</span>
            </div>

            <div class="recentlyviewed_boxcontent-boxslider">
                <div id="show_recentlyviewed" class="recentlyviewed_boxcontent owl-carousel owl-theme">

                </div>
            </div>

        </div>
    </div>

    <div class="contenttrending">
        <div class="contentboxTrending">
            <div class="tredingtop-content">
                <div class="tredingtop-left">
                    <div class="tredingtop-left-texttitle">
                        <span class="tredingtop-left-texttitle">Khách sạn HOT</span>
                    </div>
                    <div class="tredingtop-left-text">
                        <span class="tredingtop-left-text">Các khách sạn được đặt nhiều nhất do Myhotel đề
                            xuất</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="trendinghotel-slider-box">
            <div class="trendinghotel trendinghotel-js owl-carousel owl-theme">
                @foreach ($hotel_trend as $key => $hotel)
                    <a class="add_recently_viewed"
                        href="{{ URL::to('khach-san-chi-tiet?hotel_id=' . $hotel->hotel_id) }}"
                        data-id="{{ $hotel->hotel_id }} " data-name="{{ $hotel->hotel_name }}"
                        data-star=" @for ($i = 0; $i < $hotel->hotel_rank; $i++) <i class='fa-solid fa-star'></i> @endfor"
                        data-area=" Quận {{ $hotel->area->area_name }}"
                        data-url_image="{{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }}">
                        <div class="trendinghotel_boxcontent item">
                            <div class="trendinghotel_boxcontent_img_text">
                                <div class="trendinghotel_img trendinghotel_img_trend_{{ $key + 1 }}">

                                    <div class="trending_img_box_top">
                                         
                                        @if ($hotel->room->typeroom->type_room_condition == 1)
                                            <div class="trending_sale">
                                                <span
                                                    class="trending_sale_text">-{{ $hotel->room->typeroom->type_room_price_sale }}%</span>
                                            </div>
                                        @else
                                            <div class="trending_sale_test">
                                                <span class="trending_sale_text"></span>
                                            </div>
                                        @endif
                                        <div class="trending_love">
                                            <i class="fa-solid fa-heart"></i>
                                        </div>
                                    </div>

                                    <div class="trending_img_box_bottom">
                                        <div class="trending_img_box_bottom_evaluate">
                                            <span class="trending_img_box_bottom_evaluate_text chunhay">Ưu Đãi
                                                Nhất</span>
                                        </div>
                                        <div class="trending_img_box_bottom_img">
                                            <img height="54px" width="42px" style="object-fit: cover;"
                                                src="{{ asset('public/fontend/assets/img/khachsan/trending/icon_tag_travellers_2021.svg') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="trendinghotel_text">
                                    <div class="trendinghotel_text-title">
                                        {{ $hotel->hotel_name }}
                                    </div>
                                    <div class="Box-star-type">
                                        <div class="recentlyviewed_boxcontent-item-star">
                                            @for ($i = 0; $i < $hotel->hotel_rank; $i++)
                                                <i class="fa-solid fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="hotel-type">
                                            @if ($hotel->hotel_type == 1)
                                                <span> Khách Sạn </span>
                                            @elseif($hotel->hotel_type == 2)
                                                <span> Khách Sạn Căn Hộ</span>
                                            @else
                                                <span> Khu Nghĩ Dưỡng </span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="trendinghotel_place">
                                        <div>
                                            <i class="fa-solid fa-location-dot"></i>
                                            Quận {{ $hotel->area->area_name }}
                                        </div>
                                    </div>
                                    <div class="trendinghotel_text-evaluate">
                                        <?php echo $hotel->evaluate($hotel->hotel_id); ?>
                                    </div>
                                    <?php echo $hotel->order_time($hotel->hotel_id); ?>
                                    <div class="trendinghotel_text-box-price">
                                        @if ($hotel->room->typeroom->type_room_condition == 1)
                                            <div class="trendinghotel_text-box-price-one">
                                                <span>{{ number_format($hotel->room->typeroom->type_room_price, 0, ',', '.') }}đ</span>
                                            </div>
                                            <div class="trendinghotel_text-box-price-two">
                                                @php
                                                    $price_sale = $hotel->room->typeroom->type_room_price - ($hotel->room->typeroom->type_room_price / 100) * $hotel->room->typeroom->type_room_price_sale;
                                                @endphp
                                                <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                            </div>
                                        @else
                                            <div style="margin-top:-5px " class="trendinghotel_text-box-price-two">
                                                <span>&nbsp;</span>
                                            </div>
                                            @php
                                                $price_sale = $hotel->room->typeroom->type_room_price;
                                            @endphp
                                            <div class="trendinghotel_text-box-price-two">
                                                <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                            </div>
                                        @endif
                                        {{-- Lấy Mã Giảm Giá Ngẩu Nhiên --}}
                                        @php
                                            $coupon_rd = array_rand($coupons->toarray());
                                        @endphp
                                        <div class="trendinghotel_text-box-price-three">
                                            <div class="trendinghotel_text-box-price-three-l">
                                                <div class="trendinghotel_text-box-price-three-l-1"><span>Mã : </span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-2">
                                                    <span>{{ $coupons[$coupon_rd]->coupon_name_code }}</span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-3">
                                                    {{ $coupons[$coupon_rd]->coupon_price_sale }}%</div>
                                            </div>
                                            @php
                                                $price_sale_end = $price_sale - ($price_sale / 100) * $coupons[$coupon_rd]->coupon_price_sale;
                                            @endphp
                                            <div class="trendinghotel_text-box-price-three-r">
                                                <span>{{ number_format($price_sale_end, 0, ',', '.') }}đ</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .trendinghotel_img_trend_{{ $key + 1 }} {
                                background-image: url({{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }});
                            }

                            .fix {}

                            ;
                        </style>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="hottelcodesale">
        <div class="hottelcodesale_box">
            <div class="hottelcodesale_box-title">
                <span class="hottelcodesale_box-title">Mã giảm giá cho bạn</span>
            </div>

            <div class="hottelcodesale_box-content">
                @foreach ($coupons as $key => $coupon)
                    <div class="hottelcodesale_box-content-left">
                        <div class="hottelcodesale_box-content-left-content1">
                            <div class="hottelcodesale_box-content-left-content1-text">
                                <span>Nhập mã </span>
                            </div>
                            <div class="hottelcodesale_box-content-left-content1-block">
                                <span>{{ $coupon->coupon_name_code }}</span>
                            </div>
                        </div>
                        <div class="hottelcodesale_box-content-left-content2">
                            <div class="hottelcodesale_box-content-left-content2-l">
                                <span>{{ $coupon->coupon_desc }}</span>
                            </div>
                            <div class="hottelcodesale_box-content-left-content2-r">
                                <span class="hottelcodesale_box-content-left-content2-r">Điều kiện và thể lệ chương
                                    trình</span>
                            </div>
                        </div>
                        <div class="hottelcodesale_box-content-left-content3">
                            <span class="hottelcodesale_box-content-left-content3">Hạn sử dụng 30/5-1/6 | Nhập mã trước
                                khi
                                thanh toán</span>
                        </div>
                    </div>
                    @if ($key == 1)
                        @php break;@endphp
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="contenttrending">
        <div class="contentboxTrending">
            <div class="tredingtop-content">
                <div class="tredingtop-left">
                    <div class="tredingtop-left-texttitle">
                        <span class="tredingtop-left-texttitle">Khách sạn mới</span>
                    </div>
                    <div class="tredingtop-left-text">
                        <span class="tredingtop-left-text">Các khách sạn mới do Myhotel đề
                            xuất</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="trendinghotel-slider-box">
            <div class="trendinghotel trendinghotel-js owl-carousel owl-theme">
                @foreach ($hotel_new as $key => $hotel)
                    <a class="add_recently_viewed"
                        href="{{ URL::to('khach-san-chi-tiet?hotel_id=' . $hotel->hotel_id) }}"
                        data-id="{{ $hotel->hotel_id }} " data-name="{{ $hotel->hotel_name }}"
                        data-star=" @for ($i = 0; $i < $hotel->hotel_rank; $i++) <i class='fa-solid fa-star'></i> @endfor"
                        data-area=" Quận {{ $hotel->area->area_name }}"
                        data-url_image="{{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }}">
                        <div class="trendinghotel_boxcontent item">
                            <div class="trendinghotel_boxcontent_img_text">
                                <div class="trendinghotel_img trendinghotel_img_new_{{ $key + 1 }}">

                                    <div class="trending_img_box_top">
                                         
                                        @if ($hotel->room->typeroom->type_room_condition == 1)
                                            <div class="trending_sale">
                                                <span
                                                    class="trending_sale_text">-{{ $hotel->room->typeroom->type_room_price_sale }}%</span>
                                            </div>
                                        @else
                                            <div class="trending_sale_test">
                                                <span class="trending_sale_text"></span>
                                            </div>
                                        @endif
                                        <div class="trending_love">
                                            <i class="fa-solid fa-heart"></i>
                                        </div>
                                    </div>

                                    <div class="trending_img_box_bottom">
                                        <div class="trending_img_box_bottom_evaluate">
                                            <span class="trending_img_box_bottom_evaluate_text chunhay">Ưu Đãi
                                                Nhất</span>
                                        </div>
                                        <div class="trending_img_box_bottom_img">
                                            <img height="54px" width="42px" style="object-fit: cover;"
                                                src="{{ asset('public/fontend/assets/img/khachsan/trending/icon_tag_travellers_2021.svg') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="trendinghotel_text">
                                    <div class="trendinghotel_text-title">
                                        {{ $hotel->hotel_name }}
                                    </div>
                                    <div class="Box-star-type">
                                        <div class="recentlyviewed_boxcontent-item-star">
                                            @for ($i = 0; $i < $hotel->hotel_rank; $i++)
                                                <i class="fa-solid fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="hotel-type">
                                            @if ($hotel->hotel_type == 1)
                                                <span> Khách Sạn </span>
                                            @elseif($hotel->hotel_type == 2)
                                                <span> Khách Sạn Căn Hộ</span>
                                            @else
                                                <span> Khu Nghĩ Dưỡng </span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="trendinghotel_place">
                                        <div>
                                            <i class="fa-solid fa-location-dot"></i>
                                            Quận {{ $hotel->area->area_name }}
                                        </div>
                                    </div>
                                    <div class="trendinghotel_text-evaluate">
                                        <?php echo $hotel->evaluate($hotel->hotel_id); ?>
                                    </div>
                                    <?php echo $hotel->order_time($hotel->hotel_id); ?>
                                    <div class="trendinghotel_text-box-price">
                                        @if ($hotel->room->typeroom->type_room_condition == 1)
                                            <div class="trendinghotel_text-box-price-one">
                                                <span>{{ number_format($hotel->room->typeroom->type_room_price, 0, ',', '.') }}đ</span>
                                            </div>
                                            <div class="trendinghotel_text-box-price-two">
                                                @php
                                                    $price_sale = $hotel->room->typeroom->type_room_price - ($hotel->room->typeroom->type_room_price / 100) * $hotel->room->typeroom->type_room_price_sale;
                                                @endphp
                                                <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                            </div>
                                        @else
                                            <div style="margin-top:-5px " class="trendinghotel_text-box-price-two">
                                                <span>&nbsp;</span>
                                            </div>
                                            @php
                                                $price_sale = $hotel->room->typeroom->type_room_price;
                                            @endphp
                                            <div class="trendinghotel_text-box-price-two">
                                                <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                            </div>
                                        @endif
                                        {{-- Lấy Mã Giảm Giá Ngẩu Nhiên --}}
                                        @php
                                            $coupon_rd = array_rand($coupons->toarray());
                                        @endphp
                                        <div class="trendinghotel_text-box-price-three">
                                            <div class="trendinghotel_text-box-price-three-l">
                                                <div class="trendinghotel_text-box-price-three-l-1"><span>Mã : </span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-2">
                                                    <span>{{ $coupons[$coupon_rd]->coupon_name_code }}</span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-3">
                                                    {{ $coupons[$coupon_rd]->coupon_price_sale }}%</div>
                                            </div>
                                            @php
                                                $price_sale_end = $price_sale - ($price_sale / 100) * $coupons[$coupon_rd]->coupon_price_sale;
                                            @endphp
                                            <div class="trendinghotel_text-box-price-three-r">
                                                <span>{{ number_format($price_sale_end, 0, ',', '.') }}đ</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .trendinghotel_img_new_{{ $key + 1 }} {
                                background-image: url({{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }});
                            }

                            .fix {}

                            ;
                        </style>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="contenttrending">
        <div class="contentboxTrending">
            <div class="tredingtop-content">
                <div class="tredingtop-left">
                    <div class="tredingtop-left-texttitle">
                        <span class="tredingtop-left-texttitle">Khách sạn thịnh hành</span>
                    </div>
                    <div class="tredingtop-left-text">
                        <span class="tredingtop-left-text">Các khách sạn được người dùng quan tâm và xem nhiều do Myhotel đề xuất</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="trendinghotel-slider-box">
            <div class="trendinghotel trendinghotel-js owl-carousel owl-theme">
                @foreach ($hotel_view as $key => $hotel)
                    <a class="add_recently_viewed"
                        href="{{ URL::to('khach-san-chi-tiet?hotel_id=' . $hotel->hotel_id) }}"
                        data-id="{{ $hotel->hotel_id }} " data-name="{{ $hotel->hotel_name }}"
                        data-star=" @for ($i = 0; $i < $hotel->hotel_rank; $i++) <i class='fa-solid fa-star'></i> @endfor"
                        data-area=" Quận {{ $hotel->area->area_name }}"
                        data-url_image="{{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }}">
                        <div class="trendinghotel_boxcontent item">
                            <div class="trendinghotel_boxcontent_img_text">
                                <div class="trendinghotel_img trendinghotel_img_view_{{ $key + 1 }}">
                                    <div class="trending_img_box_top">
                                        @if ($hotel->room->typeroom->type_room_condition == 1)
                                            <div class="trending_sale">
                                                <span
                                                    class="trending_sale_text">-{{ $hotel->room->typeroom->type_room_price_sale }}%</span>
                                            </div>
                                        @else
                                            <div class="trending_sale_test">
                                                <span class="trending_sale_text"></span>
                                            </div>
                                        @endif
                                        <div class="trending_love">
                                            <i class="fa-solid fa-heart"></i>
                                        </div>
                                    </div>

                                    <div class="trending_img_box_bottom">
                                        <div class="trending_img_box_bottom_evaluate">
                                            <span class="trending_img_box_bottom_evaluate_text chunhay">Ưu Đãi
                                                Nhất</span>
                                        </div>
                                        <div class="trending_img_box_bottom_img">
                                            <img height="54px" width="42px" style="object-fit: cover;"
                                                src="{{ asset('public/fontend/assets/img/khachsan/trending/icon_tag_travellers_2021.svg') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="trendinghotel_text">
                                    <div class="trendinghotel_text-title">
                                        {{ $hotel->hotel_name }}
                                    </div>
                                    <div class="Box-star-type">
                                        <div class="recentlyviewed_boxcontent-item-star">
                                            @for ($i = 0; $i < $hotel->hotel_rank; $i++)
                                                <i class="fa-solid fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="hotel-type">
                                            @if ($hotel->hotel_type == 1)
                                                <span> Khách Sạn </span>
                                            @elseif($hotel->hotel_type == 2)
                                                <span> Khách Sạn Căn Hộ</span>
                                            @else
                                                <span> Khu Nghĩ Dưỡng </span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="trendinghotel_place">
                                        <div>
                                            <i class="fa-solid fa-location-dot"></i>
                                            Quận {{ $hotel->area->area_name }}
                                        </div>
                                    </div>
                                    <div class="trendinghotel_text-evaluate">
                                        <?php echo $hotel->evaluate($hotel->hotel_id); ?>
                                    </div>
                                    <?php echo $hotel->order_time($hotel->hotel_id); ?>
                                    <div class="trendinghotel_text-box-price">
                                        @if ($hotel->room->typeroom->type_room_condition == 1)
                                            <div class="trendinghotel_text-box-price-one">
                                                <span>{{ number_format($hotel->room->typeroom->type_room_price, 0, ',', '.') }}đ</span>
                                            </div>
                                            <div class="trendinghotel_text-box-price-two">
                                                @php
                                                    $price_sale = $hotel->room->typeroom->type_room_price - ($hotel->room->typeroom->type_room_price / 100) * $hotel->room->typeroom->type_room_price_sale;
                                                @endphp
                                                <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                            </div>
                                        @else
                                            <div style="margin-top:-5px " class="trendinghotel_text-box-price-two">
                                                <span>&nbsp;</span>
                                            </div>
                                            @php
                                                $price_sale = $hotel->room->typeroom->type_room_price;
                                            @endphp
                                            <div class="trendinghotel_text-box-price-two">
                                                <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                            </div>
                                        @endif
                                        {{-- Lấy Mã Giảm Giá Ngẩu Nhiên --}}
                                        @php
                                            $coupon_rd = array_rand($coupons->toarray());
                                        @endphp
                                        <div class="trendinghotel_text-box-price-three">
                                            <div class="trendinghotel_text-box-price-three-l">
                                                <div class="trendinghotel_text-box-price-three-l-1"><span>Mã : </span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-2">
                                                    <span>{{ $coupons[$coupon_rd]->coupon_name_code }}</span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-3">
                                                    {{ $coupons[$coupon_rd]->coupon_price_sale }}%</div>
                                            </div>
                                            @php
                                                $price_sale_end = $price_sale - ($price_sale / 100) * $coupons[$coupon_rd]->coupon_price_sale;
                                            @endphp
                                            <div class="trendinghotel_text-box-price-three-r">
                                                <span>{{ number_format($price_sale_end, 0, ',', '.') }}đ</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .trendinghotel_img_view_{{ $key + 1 }} {
                                background-image: url({{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }});
                            }

                            .fix {}

                            ;
                        </style>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="hottelprices">
        <div class="hottelpricesbox">
            <div class="hottelpricesbox-contenttop">
                <div class="hottelpricesbox-contenttop-title">
                    <span class="hottelpricesbox-contenttop-title">Khách sạn chỉ có trên MyHotel</span>
                </div>
                <div class="hottelpricesbox-contenttop-img">
                    <img width="auto" height="86px" style="object-fit: cover;"
                        src=" {{ asset('public/fontend/assets/img/khachsan/giatot/banner-right.png') }}"
                        alt="">
                </div>
            </div>
            <div class="hottelpricesbox-contentbottom">
                <div class="hottelpricesbox-contentbottom-layout">
                    @foreach ($all_hotel as $key => $v_hotels)
                    <a  href="{{ URL::to('khach-san-chi-tiet?hotel_id='.$v_hotels->hotel_id ) }}"
                        data-id="{{ $v_hotels->hotel_id }} " data-name="{{ $v_hotels->hotel_name }}"
                        data-star=" @for ($i = 0; $i < $v_hotels->hotel_rank; $i++) <i class='fa-solid fa-star'></i> @endfor"
                        data-area=" Quận {{ $v_hotels->area->area_name }}"
                        data-url_image="{{ asset('public/fontend/assets/img/hotel/' . $v_hotels->hotel_image) }}">
                        <div class="hottelpricesbox-contentbottom-layout-item">
                            <div class="hottelpricesbox-boxcontent_img_text">
                                <div class="hottelpricesbox-img hottelpricesbox-img_{{ $key+1 }}">
                                    <div class="hottelpricesbox-img_box_top">
                                        @if ($v_hotels->room->typeroom->type_room_condition == 1)
                                            <div class="trending_sale">
                                                <span
                                                    class="trending_sale_text">-{{ $v_hotels->room->typeroom->type_room_price_sale }}%</span>
                                            </div>
                                        @else
                                            <div class="trending_sale_test">
                                                <span class="trending_sale_text"></span>
                                            </div>
                                        @endif
                                        <div class="trending_love">
                                            <i class="fa-solid fa-heart"></i>
                                        </div>
                                    </div>
                                    <div class="hottelpricesbox-img_box_bottom">
                                        <div class="hottelpricesbox-img_box_bottom_evaluate">
                                            <span class="hottelpricesbox-img_box_bottom_evaluate_text">Ưu Đãi
                                                Nhất</span>
                                        </div>
                                        <div class="hottelpricesbox-img_box_bottom_img">
                                            <img height="54px" width="42px" style="object-fit: cover;"
                                                src="{{  asset('public/fontend/assets/img/khachsan/trending/icon_tag_travellers_2021.svg') }}"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="hottelpricesbox-text">
                                    <div class="hottelpricesbox-text-title">
                                        {{ $v_hotels->hotel_name }}
                                    </div>
                                    <div class="Box-star-type">
                                        <div class="recentlyviewed_boxcontent-item-star">
                                            @for ($i = 0; $i < $v_hotels->hotel_rank; $i++)
                                                <i class="fa-solid fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="hotel-type">
                                            @if ($v_hotels->hotel_type == 1)
                                                <span> Khách Sạn </span>
                                            @elseif($v_hotels->hotel_type == 2)
                                                <span> Khách Sạn Căn Hộ</span>
                                            @else
                                                <span> Khu Nghĩ Dưỡng </span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="hottelpricesbox-place">
                                        <div>
                                            <i class="fa-solid fa-location-dot"></i>
                                            Quận {{ $v_hotels->area->area_name }}
                                        </div>
                                    </div>
                                    <div class="trendinghotel_text-evaluate">
                                        <?php echo $v_hotels->evaluate($v_hotels->hotel_id); ?>
                                    </div>
                                    <?php echo $v_hotels->order_time($v_hotels->hotel_id); ?>
                                    <div class="hottelprices-box-price">
                                        @if ($v_hotels->room->typeroom->type_room_condition == 1)
                                        <div class="trendinghotel_text-box-price-one">
                                            <span>{{ number_format($v_hotels->room->typeroom->type_room_price, 0, ',', '.') }}đ</span>
                                        </div>
                                        <div class="trendinghotel_text-box-price-two">
                                            @php
                                                $price_sale = $v_hotels->room->typeroom->type_room_price - ($v_hotels->room->typeroom->type_room_price / 100) * $v_hotels->room->typeroom->type_room_price_sale;
                                            @endphp
                                            <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                        </div>
                                    @else
                                        <div style="margin-top:-5px " class="trendinghotel_text-box-price-two">
                                            <span>&nbsp;</span>
                                        </div>
                                        @php
                                            $price_sale = $v_hotels->room->typeroom->type_room_price;
                                        @endphp
                                        <div class="trendinghotel_text-box-price-two">
                                            <span>{{ number_format($price_sale, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endif
                                    @php
                                        $coupon_rd = array_rand($coupons->toarray());
                                    @endphp
                                        <div class="hottelprices_text-box-price-three">
                                            <div class="trendinghotel_text-box-price-three-l">
                                                <div class="trendinghotel_text-box-price-three-l-1"><span>Mã : </span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-2">
                                                    <span>{{ $coupons[$coupon_rd]->coupon_name_code }}</span>
                                                </div>
                                                <div class="trendinghotel_text-box-price-three-l-3">
                                                    {{ $coupons[$coupon_rd]->coupon_price_sale }}%</div>
                                            </div>
                                            @php
                                                $price_sale_end = $price_sale - ($price_sale / 100) * $coupons[$coupon_rd]->coupon_price_sale;
                                            @endphp
                                            <div class="trendinghotel_text-box-price-three-r">
                                                <span>{{ number_format($price_sale_end, 0, ',', '.') }}đ</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .hottelpricesbox-img_{{ $key+1  }} {
                                background-image: url({{ asset('public/fontend/assets/img/hotel/' . $v_hotels->hotel_image) }});
                            }

                            .fix {}

                            ;
                        </style>
                    </a>
                    @endforeach
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

    <script src=" {{ asset('public/fontend/assets/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src=" {{ asset('public/fontend/assets/js/base.js') }}"></script>
    <script src="{{ asset('public/fontend/assets/js/khachsan.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    {{-- Xem Gần Đây Bằng localStorage --}}
    <script>
        $('.add_recently_viewed').click(function() {
            // var url =$(this).attr("href");
            // var hotel_id = $(this).data("id");
            // var hotel_name =$(this).data("name");
            // var hotel_star =$(this).data("star");
            // var hotel_area =$(this).data("area");
            // var url_image = $(this).data("url_image");
            var newItem = {
                'url': $(this).attr("href"),
                'hotel_id': $(this).data("id"),
                'hotel_name': $(this).data("name"),
                'hotel_star': $(this).data("star"),
                'hotel_area': $(this).data("area"),
                'url_image': $(this).data("url_image"),
            }
            if (localStorage.getItem('data') == null) {
                localStorage.setItem('data', '[]');
            }
            var old_data = JSON.parse(localStorage.getItem('data'));
            /* Hàm kiểm tra item trùng */
            var check = 0;
            for (i = 0; i < old_data.length; i++) {
                if ($(this).data("id") == old_data[i].hotel_id) {
                    check++;
                    break;
                }
            }

            if (check != 0) {
                // alert("trùng");
            } else {
                old_data.push(newItem);
                localStorage.setItem('data', JSON.stringify(old_data));
            }

        })
        show_recently_viewed();

        function show_recently_viewed() {
            if (localStorage.getItem('data') != null) {
                var data = JSON.parse(localStorage.getItem('data'));
                data.reverse(); /* Đảo Ngược Vị Trí Phần Tử */
                for (i = 0; i < data.length; i++) {
                    // var url = data[i].url;
                    // var hotel_id = data[i].hotel_id;
                    // var hotel_name = data[i].hotel_name;
                    // var hotel_star = data[i].hotel_star;
                    // var hotel_area = data[i].hotel_area;
                    // var url_image = data[i].url_image;
                    $('#show_recentlyviewed').append('<a href="' + data[i].url +
                        '"> <div class="recentlyviewed_boxcontent-item item"> <div class="recentlyviewed_boxcontent-item-img-box"> <img class="recentlyviewed_boxcontent-item-img" width="178px" height="133px" style="object-fit: cover;" src="' +
                        data[i].url_image +
                        '" alt=""> </div> <div class="recentlyviewed_boxcontent-item-title"> <span>' + data[i]
                        .hotel_name + '</span> </div> <div class="recentlyviewed_boxcontent-item-star">' + data[i]
                        .hotel_star +
                        '</div> <div class="recentlyviewed_boxcontent-item-place"> <i class="fa-solid fa-location-dot"></i> <span>' +
                        data[i].hotel_area + '</span> </div> </div></a>');
                }
            }
        }
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
