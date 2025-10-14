<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link REL="SHORTCUT ICON" href="{{ asset('public/fontend/assets/iconlogo/testhuhu.jpg') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/bill.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/base.css') }}">
    <title>Đặt Phòng Thành Công</title>
    {{-- jquery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
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
@php
    $payment = session()->get('payment');
    $orderdetails = session()->get('order_details');
    $schedule = session()->get('schedule');
    $customer_id = session()->get('customer_id');
    $folder = preg_replace('/\s+/', '', $gallery_room->room->room_name);
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


    <div class="content">
        <div class="content-box">
            <div class="content-box-top">
            </div>
            <div class="content-box-bottom">
                <div class="content-box-bottom-img">
                    <img width="70px" height="67px" style="object-fit: cover;"
                        src="{{ asset('public/fontend/assets/img/bill/check.png') }}" alt="">
                </div>
                <div class="content-box-bottom-title">
                    <span>Đặt Phòng {{ $orderdetails['room_name'] }} Thành Công!</span>
                </div>
                <div class="content-box-bottom-code">
                    <span>Mã đặt phòng : {{ $orderdetails['order_code'] }}</span>
                </div>
                <div class="content-box-bottom-text">
                    @if ($payment['payment_method'] == 1)
                        <div class="content-box-bottom-text-three">
                            <span>Quý khách đã thanh toán bằng Ví Momo thành công!</span>
                        </div>
                    @endif
                    <div class="content-box-bottom-text-one">
                        <span>Đơn đặt phòng của bạn đã được ghi nhận</span>
                    </div>
                    <div class="content-box-bottom-text-one">
                        <span>Thông tin đặt phòng đã được gửi tới email <b>{{ $customer_email }}</b></span>
                    </div>
                </div>
            </div>

            <div class="contentBox-Left-Bigcontent">
                <div class="contentBox-Left-Bigcontent-Text">
                    <div class="contentBox-Left-Bigcontent-Text-Title">
                        <span>{{ $orderdetails['hotel_name'] }}</span>
                    </div>
                    <div class="contentBox-Left-Bigcontent-Text-Star-Type-Box">
                        <div class="contentBox-Left-Bigcontent-Text-Star">
                            @for ($i = 0; $i < $type_room->room->hotel->hotel_rank; $i++)
                                <i class="fa-solid fa-star"></i>
                            @endfor
                        </div>

                        <div class="contentBox-Left-Bigcontent-Text-Type">
                            @if ($type_room->room->hotel->hotel_type == 1)
                                <span> Khách Sạn </span>
                            @elseif($type_room->room->hotel->hotel_type == 2)
                                <span> Khách Sạn Căn Hộ</span>
                            @else
                                <span> Khu Nghĩ Dưỡng </span>
                            @endif
                        </div>
                    </div>
                    <div class="contentBox-Left-Bigcontent-Text-place">
                        <i class="fa-solid fa-location-pin"></i>
                        <span>19, Trường Sa, Quận Ngũ Hành Sơn, Đà Nẵng, Việt Nam</span>
                    </div>
                    <div class="contentBox-Left-Bigcontent-Text-DateTime">
                        <div class="contentBox-Left-Bigcontent-Text-DateTime-Item">
                            <div class="contentBox-Left-Bigcontent-Text-DateTime-Item-Top">
                                <span>Nhận phòng</span>
                            </div>
                            <div class="contentBox-Left-Bigcontent-Text-DateTime-Item-Bottom">
                                <span>{{ $schedule['date_start'] }}</span>
                            </div>
                        </div>
                        <div class="contentBox-Left-Bigcontent-Text-DateTime-Item">
                            <div class="contentBox-Left-Bigcontent-Text-DateTime-Item-Top">
                                <span>Trả phòng</span>
                            </div>
                            <div class="contentBox-Left-Bigcontent-Text-DateTime-Item-Bottom">
                                <span>{{ $schedule['date_end'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inforom">
                    <div class="inforom-box">
                        <div class="inforom-layout">
                            <div class="contentBox-Right-One-Box_Text">
                                <div class="contentBox-Right-One-Box_Text-Title">
                                    <span>{{ $orderdetails['room_name'] }}</span>
                                </div>
                                <div class="contentBox-Right-One-Box_Text-Content">
                                    <div class="contentBox-Right-One-Box_Text-Title-Item">
                                        <i class="fa-solid fa-user-group"></i>
                                        <span style="margin-left: 1px;">{{ $type_room->room->room_amount_of_people }}
                                            Người</span>
                                    </div>
                                    <div style="margin-left: 50px;" class="contentBox-Right-One-Box_Text-Title-Item">
                                        <i class="fas fa-eye"></i>
                                        <span style="margin-left: 5px;">{{ $type_room->room->room_view }}</span>
                                    </div>
                                </div>
                                <div class="contentBox-Right-One-Box_Text-Content">
                                    <img width="17px" height="17px" style="object-fit: cover;margin-top: -3px;"
                                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                                    <span style="margin-left: 5px;">
                                        @if ($type_room->type_room_bed == 1)
                                            1 Giường Đơn
                                        @elseif ($type_room->type_room_bed == 2)
                                            2 Giường Đơn
                                        @else
                                            1 Giường Đơn Hoặc 2 Giường Đơn
                                        @endif
                                    </span>
                                </div>
                                <div class="contentBox-Right-One-Box_Text-Content">
                                    <i class="fa-solid fa-address-card"></i>
                                    <span style="margin-left: 5px;">Hoàn huỷ một phần</span>
                                    <i style="margin-left: 5px;" class="fa-solid fa-circle-exclamation"></i>

                                </div>
                                <div class="contentBox-Right-One-Box_Text-Content">
                                    <i class="fas fa-kitchen-set"></i>
                                    <span style="margin-left: 5px;">Không bao gồm bữa ăn sáng</span>
                                </div>
                                <div class="contentBox-Right-One-Box_Text-Content">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span style="margin-left: 5px;">Chính sách hành khách và trẻ em</span>
                                </div>
                            </div>
                            <div class="inforom-img">
                                <img width="175px" height="128px" style="object-fit: cover;border-radius: 8px;"
                                    src="{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder . '/' . $gallery_room->gallery_room_image) }}"
                                    alt="{{ $gallery_room->gallery_hotel_name }}" alt="">
                            </div>
                        </div>

                        <div class="contentBox-Text-Bottom-Box">
                            <div class="contentBox-Text-Bottom-Box-One">
                                <div class="contentBox-Text-Bottom-Box-One-Box">
                                    <div class="contentBox-Text-Bottom-Box-One-Item">
                                        <span>{{ $schedule['total_date'] }} Ngày</span>
                                    </div>
                                    <div class="contentBox-Text-Bottom-Box-One-Item">
                                        <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                                            <span
                                                style="margin-left: 17px;">{{ number_format($orderdetails['price_room'], 0, ',', '.') }}đ</span>
                                        </div>
                                        @if ($type_room->type_room_condition == 1)
                                            <div class="contentBox-Text-Bottom-Box-One-Item-Bottom">
                                                <div class="contentBox-Text-Bottom-Box-One-Item-Bottom-l">
                                                    <span>-{{ $type_room->type_room_price_sale }}%</span>
                                                </div>
                                                <div class="contentBox-Text-Bottom-Box-One-Item-Bottom-r">
                                                    <span>{{ number_format($schedule['total_date'] * $type_room->type_room_price, 0, ',', '.') }}đ</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="contentBox-Text-Bottom-Box-One-Box">
                                    <div class="contentBox-Text-Bottom-Box-One-Item">
                                        <span>Thuế và phí dịch vụ khách sạn</span>
                                    </div>
                                    <div class="contentBox-Text-Bottom-Box-One-Item">
                                        <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                                            <span
                                                style="margin-left: 17px;">{{ number_format($orderdetails['hotel_fee'], 0, ',', '.') }}đ</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="contentBox-Text-Bottom-Box-One-Box">
                                    <div class="contentBox-Text-Bottom-Box-One-Item">
                                        <span>Tổng giá phòng</span>
                                    </div>
                                    <div class="contentBox-Text-Bottom-Box-One-Item">
                                        <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                                            <span
                                                style="margin-left: 17px;">{{ number_format($orderdetails['price_room'] + $orderdetails['hotel_fee'], 0, ',', '.') }}đ</span>
                                        </div>
                                    </div>
                                </div>
                                @if ($orderdetails['coupon_price_sale'] != 0)
                                    <div class="contentBox-Text-Bottom-Box-One-Box">
                                        <div
                                            class="contentBox-Text-Bottom-Box-One-Item contentBox-Text-Bottom-Box-One-Item-Layout">
                                            <div class="contentBox-Text-Bottom-Box-One-Item-Layout-item">
                                                <span>Mã giảm giá</span>
                                            </div>
                                            <div class="contentBox-Text-Bottom-Box-One-Item-Layout-item-Sale">
                                                <span>{{ $orderdetails['coupon_name_code'] }}</span>
                                            </div>
                                        </div>
                                        <div class="contentBox-Text-Bottom-Box-One-Item">
                                            <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                                                <span
                                                    style="color: #68c78f;margin-left: 17px;">-{{ number_format($orderdetails['coupon_price_sale'], 0, ',', '.') }}đ</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="Totalpayment">
                                    <div class="Totalpayment-left">
                                        <div class="Totalpayment-left-top">
                                            <span>Tổng tiền thanh toán</span>
                                        </div>
                                        <div class="Totalpayment-bottom">
                                            <span>Đã bao gồm thuế, phí, VAT</span>
                                        </div>
                                    </div>


                                    <div class="Totalpayment-right">
                                        <div class="Totalpayment-right-Top">
                                            <span>{{ number_format($orderdetails['total_payment'], 0, ',', '.') }}đ</span>
                                        </div>
                                        <div class="Totalpayment-right-Bottom">
                                            <span>(Giá cho {{ $type_room->room->room_amount_of_people }} người
                                                lớn)</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="Content-end">
                                    <i class="fa-solid fa-money-bill"></i>
                                    <span style="margin-left: 5px;">
                                        Chúc mừng! Bạn đã tiết kiệm được
                                        112.485đ</span>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="icon-bottom">
                    <div class="icon-bottom-box">
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i class="fa-solid fa-circle"></i>
                        <i style="padding: 0;" class="fa-solid fa-circle"></i>
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

    <script src="{{ asset('public/fontend/assets/js/datphong.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
    <script>
        un_set_order()

        function un_set_order() {
            $.ajax({
                url: '{{ url('/thanh-toan/un-set-order') }}',
                method: 'GET',
                data: {},
                success: function(data) {

                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>

</body>

</html>
