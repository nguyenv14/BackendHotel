<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/datphong.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/base.css') }}">
    <title>Đặt Phòng Khách Sạn {{ $hotel->hotel_name }}</title>

    {{-- jquery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
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
@php
    $schedule = Session::get('schedule');
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
                <img src="{{ asset('public/fontend/assets/img/trangchu/icon_notification_empty.svg') }}"alt="">
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

        {{-- @BannerADS
            @php
                setcookie('ADS_BANNER', true, time() + 150);
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
        @endBannerADS --}}

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
        <div class="contentBox">
            <div class="contentBox-Left">
                <div class="contentBox-Left-time">
                    <i class="fa-solid fa-clock"></i>
                    <span style="margin-left: 4px;">Thời gian hoàn tất thủ tục thanh toán 15:00</span>
                </div>
                <div class="contentBox-Left-Bigcontent">
                    <div class="contentBox-Left-Bigcontent-img">
                        <img width="112px" height="112px" style="border-radius: 8px;object-fit: cover;"
                            src="{{ asset('public/fontend/assets/img/hotel/' . $hotel->hotel_image) }}"
                            alt="">
                    </div>
                    <div class="contentBox-Left-Bigcontent-Text">
                        <div class="contentBox-Left-Bigcontent-Text-Title">
                            <span>{{ $hotel->hotel_name }}</span>
                        </div>
                        <div class="contentBox-Left-Bigcontent-Text-Star-Type-Box">
                            <div class="contentBox-Left-Bigcontent-Text-Star">
                                @for ($i = 0; $i < $hotel->hotel_rank; $i++)
                                    <i class="fa-solid fa-star"></i>
                                @endfor
                            </div>
                            <div class="contentBox-Left-Bigcontent-Text-Type">
                                @if ($hotel->hotel_type == 1)
                                    <span> Khách Sạn </span>
                                @elseif($hotel->hotel_type == 2)
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
                            <div class="contentBox-Left-Bigcontent-Text-DateTime-Item">
                                <div class="contentBox-Left-Bigcontent-Text-DateTime-Item-Top">
                                    <span>Số Ngày</span>
                                </div>
                                <div class="contentBox-Left-Bigcontent-Text-DateTime-Item-Bottom">
                                    <span>{{ $schedule['total_date'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (session()->get('customer_id') == null)
                    @php
                        $orderer = session()->get('orderer');
                    @endphp
                    <form class="form" action="">
                        <div class="form-title">
                            <span>Thông Tin Liên Hệ</span>
                        </div>
                        <div class="form-login">
                            <div style="cursor: pointer;;color: #00b6f3;">
                                <i class="fa-solid fa-circle-user"></i>
                                Đăng nhập
                            </div>
                            <span style="margin-left: 3px;">để đặt phòng nhanh hơn, không cần nhập thông tin!</span>
                        </div>
                        <div class="form-info">
                            <div class="form-group">
                                <label for="form-fullname">Họ tên<span style="color: red;">*</span></label>
                                <input id="form-fullname" class=" form-input form-input-format" type="text"
                                    value="<?php if (isset($orderer['orderer_name'])) {
                                        echo $orderer['orderer_name'];
                                    } ?>">
                                <span class="form-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="form-phone">Số điện thoại<span style="color: red;">*</span></label>
                                <input id="form-phone" class="form-input form-input-format" type="text"
                                    value="<?php if (isset($orderer['orderer_phone'])) {
                                        echo $orderer['orderer_phone'];
                                    } ?>">
                                <span class="form-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="form-email">Email<span style="color: red;">*</span></label>
                                <input id="form-email" class=" form-input form-input-format" type="text"
                                    value="<?php if (isset($orderer['orderer_email'])) {
                                        echo $orderer['orderer_email'];
                                    } ?>">
                                <span class="form-message"></span>
                            </div>
                        </div>
                    </form>
                @endif

                <div class="request">
                    <div class="request-title">
                        <span>Yêu cầu đặc biệt</span>
                    </div>
                    <div class="request-type">
                        <span>Chọn loại giường</span>
                    </div>
                    <div class="request-type-box">

                        @if ($type_room->type_room_bed == 1)
                            <div class="request-type-box-item">
                                <div class="request-type-box-item-input">
                                    <input type="radio" name="request-type-bed" checked value="1"
                                        class="request-type-bed">
                                </div>
                                <div class="request-type-box-item-icon">
                                    <img width="17px" height="17px" style="object-fit: cover;"
                                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                                </div>
                                <div class="request-type-box-item-lable">
                                    <span>1 Giường đơn</span>
                                </div>
                            </div>
                        @elseif ($type_room->type_room_bed == 2)
                            <div class="request-type-box-item">
                                <div class="request-type-box-item-input">
                                    <input type="radio" name="request-type-bed" checked value="2"
                                        class="request-type-bed">
                                </div>
                                <div class="request-type-box-item-icon">
                                    <img width="17px" height="17px" style="object-fit: cover;"
                                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                                </div>
                                <div class="request-type-box-item-icon">
                                    <img width="17px" height="17px" style="object-fit: cover;"
                                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                                </div>
                                <div class="request-type-box-item-lable">
                                    <span>2 Giường đơn</span>
                                </div>
                            </div>
                        @else
                            <div class="request-type-box-item">
                                <div class="request-type-box-item-input">
                                    <input type="radio" name="request-type-bed" checked value="1"
                                        class="request-type-bed">
                                </div>
                                <div class="request-type-box-item-icon">
                                    <img width="17px" height="17px" style="object-fit: cover;"
                                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                                </div>
                                <div class="request-type-box-item-lable">
                                    <span>1 Giường đơn</span>
                                </div>
                            </div>

                            <div class="request-type-box-item">
                                <div class="request-type-box-item-input">
                                    <input type="radio" name="request-type-bed" value="2"
                                        class="request-type-bed">
                                </div>
                                <div class="request-type-box-item-icon">
                                    <img width="17px" height="17px" style="object-fit: cover;"
                                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                                </div>
                                <div class="request-type-box-item-icon">
                                    <img width="17px" height="17px" style="object-fit: cover;"
                                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                                </div>
                                <div class="request-type-box-item-lable">
                                    <span>2 Giường đơn</span>
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="request-bd">
                        <div class="request-bd-title">
                            Yêu cầu đặc biệt
                        </div>
                        <div class="request-bd-box">
                            <div class="request-bd-box-item">
                                <div>
                                    <input type="checkbox" name="special_requirements_one"
                                        class="special_requirements" value="1">
                                </div>
                                <div>
                                    <label for="">Phòng không hút thuốc</label>
                                </div>
                            </div>
                            <div class="request-bd-box-item">
                                <div>
                                    <input type="checkbox" name="special_requirements_two"
                                        class="special_requirements" value="2">
                                </div>
                                <div>
                                    <label for="">Phòng ở tầng cao</label>
                                </div>
                            </div>
                        </div>

                        <div class="request-rieng-box">
                            <div class="request-rieng-title">
                                Yêu cầu riêng của bạn
                            </div>
                            <div class="textareabox">
                                <textarea id="insert-notes" cols="80" rows="5" placeholder="Nhập yêu cầu khác..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="receipt-box">
                        <div class="receipt-title">
                            <span>Xuất hóa đơn</span>
                        </div>
                        <div class="receipt-text">
                            <span class="receipt-text">Khi cần xuất hoá đơn GTGT, Quý khách vui lòng gửi yêu cầu đến
                                MyHotel kể
                                <div style="margin-left: 10px;">
                                    từ thời điểm nhận mail đến trước 15h00 ngày hôm sau
                                </div>
                            </span>
                        </div>
                        <div class="receipt-box-btn-text">
                            <div class="receipt-box-btn">
                                <div>
                                    <input type="checkbox" name="bill_require" id="bill_require" checked
                                        value="1">
                                </div>
                                <div>
                                    <span>Yêu cầu xuất hóa đơn</span>
                                </div>
                            </div>
                            <div class="receipt-box-text">
                                <span style="color: #5dc7eb;">Điều khoản xuất hóa đơn</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if (session()->get('coupon') != null)
                    <div class="codesale">
                        <div class="codesalebox">
                            <div class="codesalebox-left">
                                <div class="codesalebox-left-img">
                                    <img width="36px" height="24px" style="object-fit: cover;"
                                        src="{{ asset('public/fontend/assets/img/datphong/sale.png') }}"
                                        alt="">
                                </div>
                                <div class="codesalebox-left-title">
                                    <span>Mã Giảm Giá</span>
                                </div>
                            </div>
                            <div class="codesalebox-right">
                                <div class="codesalebox-right-img" id="coupon-text" style="font-weight: 600">
                                    {{ session()->get('coupon') }}
                                </div>
                                <div class="codesalebox-right-icon">
                                    <i class="fa-solid fa-pen"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="Payment">
                    <div class="Payment-box">
                        <div class="Payment-box-Title">
                            <span>Phương thức thanh toán</span>
                        </div>
                        <div class="Payment-box-Text">
                            <span>Sau khi hoàn tất thanh toán, mã xác nhận phòng sẽ được gửi ngay qua Email của
                                bạn.</span>
                        </div>

                        <div class="Payment-content-box">
                            <div class="Payment-content-box-left">
                                <div class="Payment-content-box-left-iconimg">
                                    <img width="38px" height="32px" style="object-fit: cover;"
                                        src="{{ asset('public/fontend/assets/img/datphong/icon2.png') }}"
                                        alt="">
                                </div>
                                <div class="Payment-content-box-left-text">
                                    <span>Thanh toán Momo</span>
                                </div>
                            </div>
                            <div class="Payment-content-box-right">
                                <input type="radio" name="type-payment" id="" value="1" checked>
                            </div>
                        </div>

                        <div class="Payment-content-box">
                            <div class="Payment-content-box-left">
                                <div class="Payment-content-box-left-iconimg">
                                    <img width="38px" height="32px" style="object-fit: cover;"
                                        src="{{ asset('public/fontend/assets/img/datphong/icon2.png') }}"
                                        alt="">
                                </div>
                                <div class="Payment-content-box-left-text">
                                    <span>Thanh toán QR-Pay</span>
                                </div>
                            </div>
                            <div class="Payment-content-box-right">
                                <input type="radio" name="type-payment" id="" value="">
                            </div>
                        </div>
                        <input hidden class="TheATM-Bank-CheckBox" type="checkbox" name=""
                            id="Input-TheATM-Bank-CheckBox">
                        <div class="Payment-content-box">
                            <div class="Payment-content-box-left">
                                <div class="Payment-content-box-left-iconimg">
                                    <img width="38px" height="32px" style="object-fit: cover;"
                                        src="{{ asset('public/fontend/assets/img/datphong/icon3.png') }}"
                                        alt="">
                                </div>
                                <div class="Payment-content-box-left-text">
                                    <span>Thẻ ATM / Tài khoản Ngân Hàng</span>
                                </div>
                            </div>
                            <div class="Payment-content-box-right">
                                <label for="Input-TheATM-Bank-CheckBox">
                                    <input type="radio" name="type-payment" id="">
                                </label>
                            </div>
                        </div>
                        {{-- <div class="TheATM-Bank">
                            <div class="TheATM-Bank-Box">
                                <div class="TheATM-Bank-Title">
                                    <span>Chọn ngân hàng</span>
                                </div>
                                <div class="TheATM-Bank-Layout">
                                    <div class="TheATM-Bank-Item">
                                        <img width="94px" height="24px" style="object-fit: cover;"
                                            src="assets/img/datphong/bank/vietcombank_logo.png" alt="">
                                    </div>
                                    <div class="TheATM-Bank-Item">
                                        <img width="94px" height="24px" style="object-fit: cover;"
                                            src="assets/img/datphong/bank/namabank_logo.png" alt="">
                                    </div>

                                </div>
                                <div class="TheATM-Bank-Content">
                                    <div style="margin-top: 15px;" class="TheATM-Bank-Content-Box">
                                        <div class="TheATM-Bank-Content-Icon">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="TheATM-Bank-Content-Text">
                                            <span>Tất cả thông tin thanh toán của bạn được bảo mật</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="TheATM-Bank-Content">
                                    <div class="TheATM-Bank-Content-Box">
                                        <div class="TheATM-Bank-Content-Icon">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="TheATM-Bank-Content-Text">
                                            <span>Thanh toán an toàn qua cổng VNPay, miễn phí giao dịch.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="TheATM-Bank-Content">
                                    <div class="TheATM-Bank-Content-Box">
                                        <div class="TheATM-Bank-Content-Icon">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="TheATM-Bank-Content-Text">
                                            <span>Nhận xác nhận ngay qua Email và SMS</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="Payment-content-box">
                            <div class="Payment-content-box-left">
                                <div class="Payment-content-box-left-iconimg">
                                    <img width="38px" height="32px" style="object-fit: cover;"
                                        src="{{ asset('public/fontend/assets/img/datphong/icon4.png') }}"
                                        alt="">
                                </div>
                                <div class="Payment-content-box-left-text">
                                    <span>Thẻ Visa, Master Card</span>
                                </div>
                            </div>
                            <div class="Payment-content-box-right">
                                <input type="radio" name="type-payment" id="">
                            </div>
                        </div>

                        <div class="Payment-content-box">
                            <div class="Payment-content-box-left">
                                <div class="Payment-content-box-left-iconimg">
                                    <img width="38px" height="36px" style="object-fit: cover;"
                                        src="{{ asset('public/fontend/assets/img/datphong/icon5.png') }}"
                                        alt="">
                                </div>
                                <div style="margin-left: 3px;" class="Payment-content-box-left-text">
                                    <div class="Payment-content-box-left-text">
                                        <span>Trả Sau (0% Lãi Suất)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="Payment-content-box-right">
                                <input type="radio" name="type-payment" id="" value="4">
                            </div>
                        </div>
                        <div class="Btn_payment_box">
                            <div class="Btn_payment_box-Btn">
                                <span>Thanh Toán</span>
                            </div>
                            <div class="Btn_payment_box-Dieukhoan">
                                <span>Bằng cách nhấn nút Thanh toán, bạn đồng ý với
                                    <span>Điều kiện và điều khoản</span> của chúng tôi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="contentBox-Right">
                <div class="contentBox-Right-One">
                    <div class="contentBox-Right-One-Box">
                        <div class="contentBox-Right-One-Box_img">
                            <img width="432px" height="114px" style="border-radius: 8px;object-fit: cover;"
                                src="{{ URL('public/fontend/assets/img/hotel/room/gallery_' . $folder . '/' . $gallery_room->gallery_room_image) }}"
                                alt="{{ $gallery_room->gallery_hotel_name }}">
                        </div>
                        <div class="contentBox-Right-One-Box_Text">
                            <div class="contentBox-Right-One-Box_Text-Title">
                                <span>{{ $room->room_name }}</span>
                            </div>
                            <div class="contentBox-Right-One-Box_Text-Content">
                                <div class="contentBox-Right-One-Box_Text-Title-Item">
                                    <i class="fa-solid fa-user-group"></i>
                                    <span style="margin-left: 1px;">{{ $room->room_amount_of_people }} Người</span>
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>
                                <div style="margin-left: 50px;" class="contentBox-Right-One-Box_Text-Title-Item">
                                    <i class="fas fa-eye"></i>
                                    <span style="margin-left: 5px;">{{ $room->room_view }}</span>
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
                            <div style="color: #48bb78;" class="contentBox-Right-One-Box_Text-Content">
                                <i class="fa-solid fa-bolt"></i>
                                <span style="margin-left: 5px;">Xác nhận ngay</span>
                            </div>
                        </div>
                        <div class="contentBox-Right-Two-Box">
                            <span style="margin-left: 10px;">Đừng bỏ lỡ! Chúng tôi chỉ còn 5 phòng có giá này. Hãy đặt
                                ngay!</span>
                        </div>
                    </div>
                </div>
                <div class="contentBox-Text-Bottom" id="load_price_list">

                </div>
            </div>
        </div>
    </div>

    <div class="infohotel">
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

    <div class="coupon-overlay"></div>
    <div class="coupon">
        <div class="" style="display: flex; justify-content: space-between;">
            <h3 style="margin-left: 10px;">Nhập Mã Giảm Giá</h3>
            <span class="close"> <i class="fa-solid fa-xmark"></i></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin: 20px 10px;">
            <div style="width: 80%;">
                <input type="text" class="coupon_text_code" placeholder="Nhập Mã Giảm Giá"
                    name="coupon_name_code">
            </div>
            <div style="width: 20%;">
                <button class="btn-coupon-use">Sử Dụng</button>
            </div>
        </div>
        <div style="padding: 0 10px;">
            <h4>Hoặc chọn một mã giảm giá dưới đây(1 mã)</h4>
        </div>
        <div id="load-coupon">
            {{-- <div class="coupon_body">
                <div class="coupon_item-left">
                    <span class="coupon_item-name">VUILEHOI</span> <br>
                    <span class="coupon_item-number">Khách sạn giảm đến 200K</span> <br>
                    <span class="coupon_item-time">Hạn sử dụng: 31/12/2022</span> <br>
                    <span class="coupon_item-">Điều kiện & thể lệ</span>
                </div>
                <div class="coupon_item-right">
                    <span>Hủy</span>
                </div>
            </div> --}}
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
        load_coupon();
        function load_coupon() {
            $.ajax({
                url: '{{ url('dat-phong/load-coupon') }}',
                method: 'GET',
                data: {

                },
                success: function(data) {
                    $('#load-coupon').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>
    {{-- Coupon - Mã Giảm Giá --}}
    <script>
        $('.close').click(function() {
            coupon_hide();
        })
        $('.coupon-overlay').click(function() {
            coupon_hide();
        })
        $('.codesalebox-right-icon').click(function() {
            coupon_show();
        })

        $('.btn-coupon-use').click(function() {
            var coupon_name_code = $("input[name='coupon_name_code']").val();
            if (coupon_name_code == '') {
                message_toastr("warning", "Vui Lòng Nhập Vào Mã Giảm Giá",
                    "Cảnh Báo !");
            } else {
                apply_coupon(coupon_name_code);
            }
        })



        /*  Áp Dụng Mã Giảm Giá*/
        $(document).on("click", ".coupon_item-right-apply", function() {
            var coupon_name_code = $(this).data('coupon_name_code');
            apply_coupon(coupon_name_code);
        })

        $(document).on("click", ".coupon_item-right-cancel", function() {
            $.ajax({
                url: '{{ url('dat-phong/unset-coupon') }}',
                method: 'GET',
                data: {
                },
                success: function(data) {
                    if (data == 'true') {
                        loading_price_list();
                        $('#coupon-text').html('');
                        message_toastr("success", "Đã Hủy Mã Giảm Giá",
                            "Thành Công !");
                        load_coupon();
                        coupon_hide();
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        })


        function apply_coupon(coupon_name_code) {
            $.ajax({
                url: '{{ url('dat-phong/check-coupon') }}',
                method: 'GET',
                data: {
                    coupon_name_code: coupon_name_code,
                },
                success: function(data) {
                    if (data == 'false') {
                        message_toastr("info", "Mã Giảm Giá Không Tồn Tại Hoặc Hết Hạn",
                            "Thông Báo !");
                    } else {
                        loading_price_list();
                        $('#coupon-text').html(coupon_name_code);
                        message_toastr("success", "Đã Áp Dụng Mã Giảm Giá Thành Công",
                            "Thành Công !");
                        load_coupon();
                        coupon_hide();
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }

        function coupon_hide() {
            $('.coupon').hide();
            $('.coupon-overlay').hide();
        }

        function coupon_show() {
            $('.coupon').show();
            $('.coupon-overlay').show();
        }
    </script>




    {{-- Loading Bảng Giá --}}

    <script>
        loading_price_list();

        function loading_price_list() {
            var type_room_id = {{ $type_room->type_room_id }};

            $.ajax({
                url: '{{ url('dat-phong/load-price-list') }}',
                method: 'GET',
                data: {
                    type_room_id: type_room_id,
                },
                success: function(data) {
                    $('#load_price_list').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>
    {{-- Đăng Nhập --}}
    <script>
        $('.form-login').click(function() {
            $(".fromlogin").css({
                "display": "block"
            });
            $("#overlay").css({
                "display": "block"
            });
        });
    </script>

    {{-- Yêu cầu đặc biệt - Loại Giường --}}
    <script>
        require_bed();

        function require_bed() {
            var type_bed = $('input[type="radio"][name="request-type-bed"]:checked').val();
            $.ajax({
                url: '{{ url('dat-phong/special-requirements') }}',
                method: 'GET',
                data: {
                    type_bed: type_bed,
                },
                success: function(data) {

                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }

        $('.request-type-box-item-input').click(function() {
            require_bed();
        })
    </script>

    {{-- Yêu cầu đặc biệt - Yêu Cầu Riêng --}}
    <script>
        $('#insert-notes').blur(function() {
            var own_require = $(this).val();
            $.ajax({
                url: '{{ url('dat-phong/special-requirements') }}',
                method: 'GET',
                data: {
                    own_require: own_require,
                },
                success: function(data) {

                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })

        });
    </script>

    {{-- Yêu cầu đặc biệt - Yêu Cầu Hóa Đơn --}}
    <script>
        bill_require();

        function bill_require() {
            var bill_require = $('input[type="checkbox"][name="bill_require"]:checked').val();
            if (bill_require == undefined) {
                bill_require = -1;
            }
            $.ajax({
                url: '{{ url('dat-phong/special-requirements') }}',
                method: 'GET',
                data: {
                    bill_require: bill_require,
                },
                success: function(data) {

                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })

        }
        $('#bill_require').click(function() {
            bill_require()
        });
    </script>

    {{-- Yêu Cầu Đặc Biệt --}}
    <script>
        function special_requirements() {
            var special_requirements = -1;
            var special_requirements_one = $('input[type="checkbox"][name="special_requirements_one"]:checked').val();
            var special_requirements_two = $('input[type="checkbox"][name="special_requirements_two"]:checked').val();
            if (special_requirements_one == 1 && special_requirements_two == 2) {
                special_requirements = 3;
            } else if (special_requirements_one == 1) {
                special_requirements = special_requirements_one;
            } else if (special_requirements_two == 2) {
                special_requirements = special_requirements_two;
            }
            $.ajax({
                url: '{{ url('dat-phong/special-requirements') }}',
                method: 'GET',
                data: {
                    special_requirements: special_requirements,
                },
                success: function(data) {

                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
        $('.special_requirements').click(function() {
            special_requirements();
        });
    </script>

    {{-- Nhập Thông Tin Đặt Phòng --}}
    <script>
        $('#form-phone').blur(function() {
            var phone = $(this).val();
            if (phone == '') {
                message_toastr("info", "Số Điện Thoại Không Được Để Trống !",
                    "Thông Báo !");
            } else {
                $.ajax({
                    url: '{{ url('dat-phong/put-orderer') }}',
                    method: 'GET',
                    data: {
                        phone: phone,
                    },
                    success: function(data) {
                        if (data == 'false') {
                            message_toastr("info", "Số Điện Thoại Không Hợp Lệ!",
                                "Thông Báo !");
                            $('#form-phone').val('');
                        }
                    },
                    error: function() {
                        alert("Bug Huhu :<<");
                    }
                })
            }
        });

        $('#form-fullname').blur(function() {
            var name = $(this).val();
            if (name == '') {
                message_toastr("info", "Họ Tên Không Được Để Trống !",
                    "Thông Báo !");
            } else {
                $.ajax({
                    url: '{{ url('dat-phong/put-orderer') }}',
                    method: 'GET',
                    data: {
                        name: name,
                    },
                    success: function(data) {

                    },
                    error: function() {
                        alert("Bug Huhu :<<");
                    }
                })
            }
        });

        $('#form-email').blur(function() {
            var email = $(this).val();
            if (email == '') {
                message_toastr("info", "Email Không Được Để Trống !",
                    "Thông Báo !");
            } else {
                $.ajax({
                    url: '{{ url('dat-phong/put-orderer') }}',
                    method: 'GET',
                    data: {
                        email: email,
                    },
                    success: function(data) {
                        if (data == 'false') {
                            message_toastr("info", "Email Không Hợp Lệ!",
                                "Thông Báo !");
                            $('#form-email').val('');
                        }
                    },
                    error: function() {
                        alert("Bug Huhu :<<");
                    }
                })
            }
        });
    </script>


    {{-- Chức Năng Đặt Phòng - Thanh Toán --}}
    <script>
        function payment() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });

            var type_payment = $('input[type="radio"][name="type-payment"]:checked').val();
            if (type_payment == 1) {
                window.location = '{{ url('/thanh-toan/momo-payment') }}';
            }
            if (type_payment == 4) {
                window.location = '{{ url('/thanh-toan/direct-payment') }}';
            }
        }

        $('.Btn_payment_box-Btn').click(function() {
            var customer_id = '{{ $customer_id }}';
            var form_fullname = $('#form-fullname').val();
            var form_phone = $('#form-phone').val();
            var form_email = $('#form-email').val();

            if (customer_id != '' || form_fullname != '' && form_phone != '' && form_email != '') {
                payment();
            } else {
                message_toastr("warning", "Vui Lòng Nhập Thông Tin Liên Hệ Hoặc Đăng Nhập Để Thanh Toán",
                    "Cảnh Báo !");
            }
        })
    </script>

</body>

</html>
