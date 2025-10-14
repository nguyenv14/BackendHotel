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
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/thongtinkhachsan.css') }}">
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

    .MeliaDanangBeachResort-Bigevaluate_Right {
        width: 380px;
        height: 145px;
        background-image: url({{ asset('public/fontend/assets/img/thongtinkhachsan/icon_background.png') }});
        background-size: 100%;
        color: #1a202c;
        line-height: 22px;
    }

    .test {}
</style>

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
        <div class="BoxSearch-End">
            <div class="BoxSearch-End-Box">
                <ul class="BoxSearch-End-Box-ul">
                    <a id="MeliaDanangBeachResort-Click" href="">
                        <li style="margin-left: -35px;" class="BoxSearch-End-li">Tổng Quan</li>
                    </a>
                    <a id="MeliaDanangBeachResort-Map-Service-Click" href="">
                        <li class="BoxSearch-End-li">Địa Điểm Nổi Bật</li>
                    </a>
                    <a id="chooserooms-click" href="">
                        <li class="BoxSearch-End-li">Phòng</li>
                    </a>
                    <a id="userswrite-click" href="">
                        <li class="BoxSearch-End-li">Đánh Giá</li>
                    </a>
                    <a id="policy-click" href="">
                        <li class="BoxSearch-End-li">Chính Sách</li>
                    </a>
                    <a id="hotelmessageboard-click" href="">
                        <li class="BoxSearch-End-li">Bản Tin Khách Sạn</li>
                    </a>
                </ul>
            </div>
        </div>
    </div>
    <div class="menumin">
        <div class="menumin-box">
            <ul class="menumin-box-ul">
                <li class="menumin-box-li">
                    @if ($hotel->hotel_type == 1)
                        <span> Khách Sạn </span>
                    @elseif($hotel->hotel_type == 2)
                        <span> Khách Sạn Căn Hộ</span>
                    @else
                        <span> Khu Nghĩ Dưỡng </span>
                    @endif
                </li>
                <li class="menumin-box-li"><i class="fa-solid fa-angle-right"></i></li>
                <li class="menumin-box-li">Đà Nẵng</li>
                <li class="menumin-box-li"><i class="fa-solid fa-angle-right"></i></li>
                <li class="menumin-box-li"> Quận {{ $hotel->area->area_name }}</li>
                <li class="menumin-box-li"> <i class="fa-solid fa-angle-right"></i></li>
                <li class="menumin-box-li">{{ $hotel->hotel_name }}</li>
            </ul>
        </div>
    </div>
    <div class="MeliaDanangBeachResort">
        <div class="MeliaDanangBeachResort_Box">
            <div class="MeliaDanangBeachResort-Title">
                <span>{{ $hotel->hotel_name }}</span>
            </div>
            <div class="MeliaDanangBeachResort-Star-Type-Box">
                <div class="MeliaDanangBeachResort-Star">
                    @for ($i = 0; $i < $hotel->hotel_rank; $i++)
                        <i class="fa-solid fa-star"></i>
                    @endfor
                </div>
                <div class="MeliaDanangBeachResort-Type">
                    @if ($hotel->hotel_type == 1)
                        <span> Khách Sạn </span>
                    @elseif($hotel->hotel_type == 2)
                        <span> Khách Sạn Căn Hộ</span>
                    @else
                        <span> Khu Nghĩ Dưỡng </span>
                    @endif
                </div>
            </div>
            <div class="MeliaDanangBeachResort-evaluate-Box">
                <?php echo($hotel->evaluate_details($hotel->hotel_id))?>
            </div>
        
            <div class="MeliaDanangBeachResort-place-Box">
                <i class="fa-solid fa-location-pin"></i>
                <span style="color:#1a202c;font-size:14px;line-height:17px;margin-left: 5px;">{{ $hotel->hotel_placedetails }}</span>
                <a href="{{ $hotel->hotel_linkplace }}" style=" color:#00b6f3;font-size:14px;font-weight:500;margin-left: 5px;">Xem bản
                    đồ</a>
            </div>
            @php
                $folder = preg_replace('/\s+/', '', $hotel->hotel_name);
                $count_img = count($images_hotel);
            @endphp
            <div class="MeliaDanangBeachResort-img-Box">
                <div class="MeliaDanangBeachResort-img MeliaDanangBeachResort-video ">
                    <video type="video/mp4" autoplay="" muted="" loop="" width="592px"
                        height="366px"
                        src="{{ URL('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $video->gallery_hotel_image) }}"></video>
                </div>
                @foreach ($images_hotel as $key => $image)
                    <div class="MeliaDanangBeachResort-img">
                        <div class="MeliaDanangBeachResort-img-Fixbug">
                            <img width="284px" height="160px" style="border-radius:8px;object-fit: cover;"
                                src="{{ URL('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $image->gallery_hotel_image) }}"
                                alt="{{ $image->gallery_hotel_name }}">
                        </div>
                    </div>
                    @if ($key == 2)
                        @php break @endphp
                    @endif
                @endforeach
                <style>
                    .MeliaDanangBeachResort-representative_image {
                        margin-top: 11.5px;
                        width: 284px;
                        height: 160px;
                        background-image: url({{ asset('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $representative_image->gallery_hotel_image) }});
                        background-size: 100%;
                        opacity: 0.7;
                    }

                    .Fix {}

                    ;
                </style>
                <label for="Input-ShowImgHotel" style="cursor: pointer;border-radius:8px;"
                    class="MeliaDanangBeachResort-img MeliaDanangBeachResort-representative_image">
                    <div class="MeliaDanangBeachResort-img-text" style="color: #fff">
                        <b>+{{ $count_img }}</b><i style="margin-left: 3px;" class="fa-solid fa-image"></i>
                    </div>
                </label>

            </div>
            <div class="MeliaDanangBeachResort-Bigevaluate-Box">
                <?php echo $hotel->evaluate_hotel_details($hotel->hotel_id); ?>
                <div class="MeliaDanangBeachResort-Bigevaluate_Center">
                    <div class="MeliaDanangBeachResort-Bigevaluate_Center-Title">
                        <span>Kỳ nghỉ HÈ hoàn hảo</span>
                    </div>
                    <div class="MeliaDanangBeachResort-Bigevaluate_Center-Text">
                        Khu nghỉ mát tuyệt vời với dịch vụ trên cả tuyệt vời.
                        Nhân viên rất chu đáo và lễ phép.
                        Bữa sáng tuyệt vời bên bãi biển tuyệt vời đã làm cho
                        kỳ nghỉ của chúng tôi trở nên đặc biệt.
                    </div>
                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Right">
                    <div class="MeliaDanangBeachResort-Bigevaluate_Right-box" style="width: 187px;height: 85px;">
                        Nền tảng du lịch số 1 Việt Nam về đặt phòng Khách Sạn và vé Máy Bay
                    </div>
                </div>
            </div>
            <div id="MeliaDanangBeachResort-Map-Service" class="MeliaDanangBeachResort-Map-Service">
                <div class="MeliaDanangBeachResort-Map">
                    <div class="MeliaDanangBeachResort-Map-title">
                        <span>Địa Điểm Nổi Bật</span>
                    </div>
                    <div class="MeliaDanangBeachResort-Map-Border">
                        <div class="MeliaDanangBeachResort-Map-imgMap">
                            <?php echo($hotel->hotel_jfameplace) ?>
                        </div>
                        <div class="MeliaDanangBeachResort-Map-place">
                            <div class="MeliaDanangBeachResort-Map-place-l">
                                <i class="fa-solid fa-location-pin"></i>
                                <span>{{ $hotel->hotel_placedetails }}</span>
                            </div>
                            <div class="MeliaDanangBeachResort-Map-place-r">
                                <a class="MeliaDanangBeachResort-Map-place-r"
                                    href="{{ $hotel->hotel_linkplace }}" target="_blank"><span>Hiễn thị trên
                                        bản đồ</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="MeliaDanangBeachResort-Service">
                    <div class="MeliaDanangBeachResort-Service-title">
                        <span>Tiện Ích Khách Sạn</span>
                    </div>
                    <a href="">
                        <div class="MeliaDanangBeachResort-Service_img">
                            <img width="385px" height="195px" style="object-fit: cover;"
                                src="{{ asset('public/fontend/assets/img/thongtinkhachsan/sv.png') }}"
                                alt="">

                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <input type="checkbox" hidden class="Input-ShowImgHotel-Select" name="" id="Input-ShowImgHotel">
    <label for="Input-ShowImgHotel" class="showimg-overlay ">

    </label>
    <div class="ShowImgHotel">
        <div class="ShowImgHotel-Box">
            <div class="ShowImgHotel-js owl-carousel owl-theme">
                @foreach ($images_hotel as $key => $image)
                    <div class="item">
                        <img width="1100px" height="628px" style="object-fit: cover;border-radius: 8px;"
                            src="{{ URL('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $image->gallery_hotel_image) }}"
                            alt="{{ $image->gallery_hotel_name }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="chooserooms" class="chooserooms">
        <div class="chooseroomsbox">
            <div class="chooseroomsbox-Title">
                <span>Chọn Phòng</span>
            </div>
            <div class="chooseroomsbox-boxcontent">
                <div class="chooseroomsbox-boxcontent-top">
                    <i class="fa-solid fa-star"></i>
                    <span style="margin-left: 5px ; margin-top: 2px;">Được đề xuất</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom">
                    <div class="chooseroomsbox-boxcontent-bottom-box">
                        <div class="chooseroomsbox-boxcontent-bottom-img-top">

                            @php
                                $folder_gallary_room = preg_replace('/\s+/', '', $suggested_room->room_name);
                            @endphp

                            <div class="chooseroomsbox-boxcontent-bottom-img">

                                {{-- Lấy Ra Cái Ảnh To --}}
                                @for ($i = 0; $i < count($gallary_room); $i++)
                                    @if ($gallary_room[$i]->room_id == $suggested_room->room_id)
                                        <div class="chooseroomsbox-boxcontent-bottom-img-item">
                                            <img width="200px" height="151px"
                                                style=" border-radius: 8px 8px 0 0;object-fit: cover;"
                                                src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                alt="">
                                            <div class="hoverimg-chooser-imgbig">
                                                <img width="459px" height="310px"
                                                    style=" border-radius: 8px 8px 0 0;object-fit: cover;"
                                                    src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                    alt="">
                                            </div>
                                        </div>
                                        @php
                                            break;
                                        @endphp
                                    @endif
                                @endfor

                                {{-- Lấy Ra Nhiều Cái Ảnh Bé --}}
                                @for ($i = 0; $i < count($gallary_room); $i++)
                                    @if ($gallary_room[$i]->room_id == $suggested_room->room_id)
                                        <div class="chooseroomsbox-boxcontent-bottom-img-item">
                                            <img width="66px" height="49px"
                                                style="object-fit: cover; border-radius: 4px;padding-left: 2px;"
                                                src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                alt="">
                                            <div class="hoverimg-chooser">
                                                <img width="459px" height="310px"
                                                    style=" border-radius: 8px 8px 0 0;object-fit: cover;"
                                                    src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                    alt="">
                                            </div>
                                        </div>
                                    @endif
                                @endfor


                            </div>
                            <div class="chooseroomsbox-boxcontent-bottom-img-bottom-title">
                                <div href="" class="chooseroomsbox-boxcontent-bottom-img-bottomtext">
                                    <span class="btn-detail-room" data-room_id="{{ $suggested_room->room_id }}"
                                        style="margin-top: 6px;margin-left: 10px;">Xem hình ảnh & tiện nghị</span>
                                </div>
                            </div>
                        </div>
                        <div class="chooseroomsbox-boxcontent-bottom-text">
                            <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne">
                                <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-Title">
                                    <span>{{ $suggested_room->room_name }}</span>
                                </div>
                                <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout">
                                    <div style="margin-left: 0px;"
                                        class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout-Item">
                                        <i class="fa-solid fa-user-group"></i>
                                        <span>{{ $suggested_room->room_amount_of_people }} người</span>
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                    </div>
                                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout-Item">
                                        <i class="fa-solid fa-maximize"></i>
                                        <span>{{ $suggested_room->room_acreage }}m2</span>
                                    </div>
                                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout-Item">
                                        <i class="fa-solid fa-eye"></i>
                                        <span>{{ $suggested_room->room_view }}</span>
                                    </div>
                                </div>

                                {{-- Lấy Ra Lựa Chọn Phòng Đề Xuất --}}
                                <?php
                                echo $suggested_room->loading_type_room($suggested_room->room_id);
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($rooms as $key => $room)
        <div class="chooseroomsmin">
            <div class="chooseroomsbox">
                <div class="chooseroomsbox-boxmincontent">
                    <div class="chooseroomsbox-boxcontent-bottom">
                        <div class="chooseroomsbox-boxcontent-bottom-box">
                            <div class="chooseroomsbox-boxcontent-bottom-img-top">
                                <div class="chooseroomsbox-boxcontent-bottom-img">
                                    @php
                                        $folder_gallary_room = preg_replace('/\s+/', '', $room->room_name);
                                    @endphp
                                    {{-- Lấy Ra Cái Ảnh To --}}
                                    @for ($i = 0; $i < count($gallary_room); $i++)
                                        @if ($gallary_room[$i]->room_id == $room->room_id)
                                            <div class="chooseroomsbox-boxcontent-bottom-img-item">
                                                <img width="200px" height="151px"
                                                    style=" border-radius: 8px 8px 0 0;object-fit: cover;"
                                                    src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                    alt="">
                                                <div class="hoverimg-chooser-imgbig">
                                                    <img width="459px" height="310px"
                                                        style=" border-radius: 8px 8px 0 0;object-fit: cover;"
                                                        src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                        alt="">
                                                </div>
                                            </div>
                                            @php
                                                break;
                                            @endphp
                                        @endif
                                    @endfor
                                    {{-- Lấy Ra Nhiều Cái Ảnh Bé --}}
                                    @for ($i = 0; $i < count($gallary_room); $i++)
                                        @if ($gallary_room[$i]->room_id == $room->room_id)
                                            <div class="chooseroomsbox-boxcontent-bottom-img-item">
                                                <img width="66px" height="49px"
                                                    style="object-fit: cover; border-radius: 4px;padding-left: 2px;"
                                                    src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                    alt="">
                                                <div class="hoverimg-chooser">
                                                    <img width="459px" height="310px"
                                                        style=" border-radius: 8px 8px 0 0;object-fit: cover;"
                                                        src='{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder_gallary_room . '/' . $gallary_room[$i]->gallery_room_image) }}'
                                                        alt="">
                                                </div>
                                            </div>
                                        @endif
                                    @endfor

                                </div>
                                <div class="chooseroomsbox-boxcontent-bottom-img-bottom-title">
                                    <div class="chooseroomsbox-boxcontent-bottom-img-bottomtext">
                                        <span class="btn-detail-room" data-room_id="{{ $room->room_id }}"
                                            style="margin-top: 6px;margin-left: 10px;">Xem hình ảnh & tiện
                                            nghị</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chooseroomsbox-boxcontent-bottom-text">
                                <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne">
                                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-Title">
                                        <span>{{ $room->room_name }}</span>
                                    </div>
                                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout">
                                        <div style="margin-left: 0px;"
                                            class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout-Item">
                                            <i class="fa-solid fa-user-group"></i>
                                            <span>{{ $room->room_amount_of_people }} Người</span>
                                        </div>
                                        <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout-Item"
                                            style="margin-top: 5px">
                                            <i class="fa-solid fa-maximize"></i>
                                            <span>{{ $room->room_acreage }}m<sup>2</sup></span>
                                        </div>
                                        <div class="chooseroomsbox-boxcontent-bottom-text-BoxOne-BoxLayout-Item"
                                            style="margin-top: 9px">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>{{ $room->room_view }}</span>
                                        </div>
                                    </div>
                                    <script>
                                        var page_{{ $key }} = 1;
                                        var room_id = {{ $room->room_id }};
                                        loading_type_room(room_id, 1)

                                        function loading_type_room(room_id, page) {
                                            $.ajax({
                                                url: '{{ url('/khach-san-chi-tiet/lua-chon-phong?page=') }}' + page,
                                                method: 'get',
                                                data: {
                                                    room_id: room_id,
                                                },
                                                success: function(data) {
                                                    $('#loading_type_room' + room_id + '').append(data);
                                                },
                                                error: function() {
                                                    // alert("Bug Huhu :<<");
                                                }
                                            })
                                        }
                                    </script>
                                    <div id="loading_type_room{{ $room->room_id }}">

                                    </div>

                                    <span class="chooseroomsbox-themluachon"
                                        onclick="show_type_room({{ $room->room_id }},{{ $key }});">
                                        <span>Xem thêm lựa chọn</span>
                                        <i style="margin-left: 5px;" class="fa-solid fa-caret-down"></i>
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function show_type_room(room_id, key) {
            if (key == 0) {
                page_0++;
                loading_type_room(room_id, page_0);
            } else if (key == 1) {
                page_1++;
                loading_type_room(room_id, page_1);
            } else if (key == 2) {
                page_2++;
                loading_type_room(room_id, page_2);
            } else if (key == 3) {
                page_3++;
                loading_type_room(room_id, page_3);
            } else if (key == 4) {
                page_4++;
                loading_type_room(room_id, page_4);
            } else if (key == 5) {
                page_5++;
                loading_type_room(room_id, page_5);
            }
        }
    </script>


    <div class="inforooms_overlay">
    </div>
    <div class="inforooms" id="loading-convenient-room">

    </div>

    <div class="Danhgia">
        <div class="Danhgiabox">
            <div class="DanhgiaBox-Title_Text">
                <div class="Danhgia-Title">
                    <span>Đánh giá</span>
                </div>
                <div class="Danhgia-Text">
                    <span>100% đánh giá từ khách hàng đặt phòng trên Myhotel</span>
                </div>
            </div>
            <div class="imgusers">
                <div class="imgusers-box">
                    <div class="imgusers-box-left">
                        <div class="imgusers-box-left-text">

                        </div>
                        <div class="imgusers-box-left-img">
                            <div class="imgusers-box-left-img-item">

                            </div>
                        </div>
                    </div>
                    <div class="imgusers-box-right">
                        <div class="imgusers-box-right-box">
                            <ul class="imgusers-box-right-box-ul">
                                <li class="imgusers-box-right-box-li">Mới nhất</li>
                                <li class="imgusers-box-right-box-li">Cũ nhất</li>
                                <li class="imgusers-box-right-box-li">Điểm cao nhất</li>
                                <li class="imgusers-box-right-box-li">Điểm thấp nhất</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($evaluate_hotel as $v_evaluate_hotel)
                <div class="userswrite">
                    <div class="userswrite-boxone">
                        <div class="userswrite-boxone-imgusers">
                            <div class="userswrite-boxone-imgusers-element">
                                <?php
                                $customer_name = explode(' ', $v_evaluate_hotel->customer_name);
                                $customer_name = array_reverse($customer_name);
                                $customer_name = $customer_name[0];
                                ?>
                                <span>{{ substr($customer_name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="userswrite-boxone-infousers">
                            <div class="userswrite-boxone-infousers-box">
                                <div class="userswrite-boxone-infousers-title">
                                    <span>{{ $v_evaluate_hotel->customer_name }}</span>
                                </div>
                                <div class="userswrite-boxone-infousers-item">
                                    <i class="fa-solid fa-pen"></i>
                                    <span
                                        class="userswrite-boxone-infousers-item-text">{{ $v_evaluate_hotel->created_at }}</span>
                                </div>
                                <div class="userswrite-boxone-infousers-item">
                                    <i class="fa-solid fa-bed"></i>
                                    <span
                                        class="userswrite-boxone-infousers-item-text">{{ $v_evaluate_hotel->room->room_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="userswrite-boxtwo">
                        <div class="userswrite-boxtwo-title">
                            <span>{{ $v_evaluate_hotel->evaluate_title }}</span>
                        </div>
                        <div class="userswrite-boxtwo-content">
                            <span>{{ $v_evaluate_hotel->evaluate_content }}</span>
                        </div>
                        <div class="userswrite-boxtwo-like">
                            <i class="fa-solid fa-thumbs-up"></i>
                            <span>Đánh giá này có hữu ích với bạn không?</span>
                        </div>
                    </div>
                    <div class="userswrite-boxthree">
                        <div class="userswrite-boxthree-content">
                            <div class="userswrite-boxthree-content-number">
                                <?php
                                $avg_evaluate = ($v_evaluate_hotel->evaluate_loaction_point + $v_evaluate_hotel->evaluate_service_point + $v_evaluate_hotel->evaluate_price_point + $v_evaluate_hotel->evaluate_sanitary_point + $v_evaluate_hotel->evaluate_convenient_point) / 5;
                                ?>
                                <span>{{ number_format($avg_evaluate, 1, '.') }}</span>
                            </div>
                            <div class="userswrite-boxthree-content-text">
                                @if ($avg_evaluate == 0)
                                    <span>Chưa Có Đánh Giá</span>
                                @elseif($avg_evaluate <= 2)
                                    <span>Trung Bình</span>
                                @elseif($avg_evaluate <= 3)
                                    <span>Tốt</span>
                                @elseif($avg_evaluate <= 4)
                                    <span>Tuyệt Vời</span>
                                @elseif($avg_evaluate <= 5)
                                    <span>Xuất Sắc</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <a href="" class="seemore">
                <div class="seemore-btn">
                    <span>Xem thêm đánh giá</span>
                </div>
            </a>
        </div>
    </div>

    <div id="policy" class="policy">
        <div class="policy-box">
            <div class="policy-title">
                <span>Chính sách khách sạn</span>
            </div>
            <div class="policy-box-time">
                <div class="policy-box-time-item">
                    <div class="policy-box-time-item-title">
                        <span>Nhận phòng</span>
                    </div>
                    <div class="policy-box-time-item-time">
                        <span>Từ 14:00</span>
                    </div>
                </div>
                <div style="margin-left: 15px;" class="policy-box-time-item">
                    <div class="policy-box-time-item-title">
                        <span>Trả phòng</span>
                    </div>
                    <div class="policy-box-time-item-time">
                        <span>Trước 11:00</span>
                    </div>
                </div>
            </div>
            <div class="policy-content">
                <div class="policy-content-title">
                    <span>Chính sách chung</span>
                </div>
                <div class="policy-content-text">
                    <div>Không cho phép hút thuốc</div>
                    <div style="margin-top: 2px;">Không cho phép thú cưng</div>
                    <div style="margin-top: 2px;">Không cho phép tổ chức tiệc / sự kiện</div>
                </div>
            </div>

            <div class="policy-content">
                <div class="policy-content-title">
                    <span>Chính sách trẻ em</span>
                </div>
                <div class="policy-content-text policy-content-text-borderbottom">
                    <div>Khách lớn hơn 12 tuổi sẽ được xem như người lớn</div>
                    <div style="margin-top: 2px;">Trẻ em dưới 6 tuổi có thể lưu trú miễn phí</div>
                    <div style="margin-top: 2px;">Trẻ em ở các tuổi còn lại lưu trú có thể mất phí</div>
                </div>
            </div>
        </div>
    </div>

    <div id="hotelmessageboard" class="hotelmessageboard">
        <div class="hotelmessageboard-box">
            <div class="hotelmessageboard-title">
                <span>Bản tin khách sạn</span>
            </div>
            <div class="hotelmessageboard-content">
                <div class="hotelmessageboard-content-text">
                    <div class="hotelmessageboard-content-text-title">
                        <span>{{ $hotel->hotel_name }}</span>
                    </div>
                    <div class="hotelmessageboard-content-text-line">
                        {{ $hotel->hotel_desc }}
                    </div>
                </div>
                <div class="hotelmessageboard-content-img">
                    <img width="683px" height="398px" style="object-fit: cover; border-radius: 8px;"
                        src=" {{ asset('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $representative_image->gallery_hotel_image) }}"
                        alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="contenttrending">
        <div class="contentboxTrending">
            <div class="tredingtop-content">
                <div class="tredingtop-left">
                    <div class="tredingtop-left-texttitle">
                        <span class="tredingtop-left-texttitle">Khách sạn xung quanh</span>
                    </div>
                    <div class="tredingtop-left-text">
                        <span class="tredingtop-left-text">Các khách sạn ở xung quanh do Myhotel đề xuất</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="trendinghotel-slider-box">
            <div class="trendinghotel trendinghotel-js owl-carousel owl-theme">

                @foreach ($area_hotel as $key => $hotel)
                    <a class="add_recently_viewed"
                        href="{{ URL::to('khach-san-chi-tiet?hotel_id=' . $hotel->hotel_id) }}"
                        data-id="{{ $hotel->hotel_id }} " data-name="{{ $hotel->hotel_name }}"
                        data-star=" @for ($i = 0; $i < $hotel->hotel_rank; $i++) <i class='fa-solid fa-star'></i> @endfor"
                        data-area=" Quận {{ $hotel->area->area_name }}"
                        data-url_image="{{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }}">
                        <div class="trendinghotel_boxcontent item">
                            <div class="trendinghotel_boxcontent_img_text">
                                <div class="trendinghotel_img trendinghotel_img_{{ $key + 1 }}">

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
                                    <div class="Box-star-type" style="display: flex; align-items: baseline">
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
                                        <div class="trendinghotel_text-evaluate-icon">
                                            <i class="fa-solid fa-umbrella"></i>9.0
                                        </div>
                                        <div class="trendinghotel_text-evaluate-text">
                                            Tuyệt vời <span style=" color:#4a5568;">(573 đánh giá)</span>
                                        </div>
                                    </div>
                                    <div class="trendinghotel_text-time">
                                        Vừa đặt cách đây vài ngày trước
                                    </div>
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
                            .trendinghotel_img_{{ $key + 1 }} {
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
    <script src="{{ asset('public/fontend/assets/js/thongtinkhachsan.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    {{-- Xem Gần Đây Bằng localStorage --}}
    <script>
        $('.btn-detail-room').click(function() {
            var room_id = $(this).data('room_id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('/khach-san-chi-tiet/chi-tiet-tien-nghi') }}',
                method: 'GET',
                data: {
                    room_id: room_id,
                },
                success: function(data) {
                    $('#loading-convenient-room').html(data);
                    $("#owl-demo").owlCarousel({
                        loop: true,
                        margin: 10,
                        nav: false,
                        autoplay: true,
                        autoplayTimeout: 15000,
                        smartSpeed: 1200,
                        dots: true,
                        touchDrag: true,
                        responsive: {
                            0: {
                                items: 1
                            },
                            600: {
                                items: 1
                            },
                            1000: {
                                items: 1
                            }
                        }
                    });
                },
                error: function() {
                    alert('Lỗi quá Nhuận kìa ><');
                }
            });
        })

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

    {{-- Thêm Coupon Vào Session --}}
    <script>
        $(document).on('click', '#add-coupon-room', function() {
            var coupon_name_code = $(this).data('coupon_name_code');
            $.ajax({
                url: '{{ url('dat-phong/put-coupon') }}',
                method: 'GET',
                data: {
                    coupon_name_code: coupon_name_code,
                },

                success: function(data) {
                    if (data == 'false') {
                        alert("Có Bug Mã Giám Giá Kìa !");
                    }
                },
                error: function() {
                    // alert("Bug Huhu :<<");
                }
            })
        });
    </script>


</body>

</html>
