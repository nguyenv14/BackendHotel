<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link REL="SHORTCUT ICON" href="{{ asset('public/fontend/assets/iconlogo/testhuhu.jpg') }}">
    <title>Thông Tin Khách Sạn</title>

    <link rel="stylesheet" href="{{ asset('public/fontend/assets/css/mastersearch.css') }}">
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


    <div class="hottelprices">
        <div class="hottelpricesbox">
            <div class="hottelpricesbox-contenttop">

            </div>
            <div class="hottelpricesbox-contentbottom">
                <div class="seafood_layout">
                    <div class="category_seafood">
                        <div class="filter">
                            <div class="filter_top">
                                <div class="filter_top_box">
                                    <div class="filter_title">
                                        Bộ lọc
                                    </div>
                                    <div class="filter_cancel">
                                        Xóa tất cả lọc
                                    </div>
                                </div>
                            </div>
                            <div class="filter_content">
                                <div class="filter_content_box">
                                    <div class="filter_content_title">
                                        <span>Tìm Kiếm</span>
                                    </div>
                                    <div>
                                        <form>
                                            <div id="search-form"
                                                style="display: flex; width: 100% ;padding-top:8px ;">
                                                <input id="searchbyvoice" style=" margin-left: 20px"
                                                    name="searchbyvoice" class="form-control" type="text"
                                                    placeholder="Tìm Kiếm" autocomplete="off" autofocus>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <div class="filter_content">
                                <div class="filter_content_box">
                                    <div style="display: flex;" class="filter_content_title">
                                        <div>Khoảng Giá </div>
                                        <div><input type="text" id="amount" readonly
                                                style="border:0; color:#f6931f; font-weight:bold; width: 110px; margin-left: 4px;">
                                        </div>
                                    </div>
                                    <div style="margin-left: 20px;margin-top: 8px ; width: 238px;" id="slider-range">

                                    </div>

                                </div>
                            </div>

                            <div class="filter_content">
                                <div class="filter_content_box">
                                    <div class="filter_content_title">
                                        <span>Hạng</span>

                                    </div>

                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="5" name="star" class="action"> <span
                                            class="filter_content_text">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i style="color:#ffbc39;margin-left:4px" class='fa-solid fa-star'></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="4" name="star" class="action"> <span
                                            class="filter_content_text">
                                            @for ($i = 1; $i <= 4; $i++)
                                                <i style="color:#ffbc39;margin-left:4px" class='fa-solid fa-star'></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="3" name="star" class="action"> <span
                                            class="filter_content_text">
                                            @for ($i = 1; $i <= 3; $i++)
                                                <i style="color:#ffbc39;margin-left:4px" class='fa-solid fa-star'></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="2" name="star" class="action"> <span
                                            class="filter_content_text">
                                            @for ($i = 1; $i <= 2; $i++)
                                                <i style="color:#ffbc39;margin-left:4px" class='fa-solid fa-star'></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="1" name="star" class="action"> <span
                                            class="filter_content_text"><i style="color:#ffbc39;margin-left:4px"
                                                class='fa-solid fa-star'></i> </span>
                                    </div>

                                </div>
                            </div>

                            <div class="filter_content">
                                <div class="filter_content_box">
                                    <div class="filter_content_title">
                                        <span>Loại Khách Sạn</span>

                                    </div>

                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="1" name="hotel_type" class="action">
                                        <span class="filter_content_text">Khách Sạn</span>
                                    </div>

                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="2" name="hotel_type" class="action">
                                        <span class="filter_content_text">Khách Sạn Căn Hộ</span>
                                    </div>

                                    <div class="filter_content_Checkbox">
                                        <input type="checkbox" value="3" name="hotel_type" class="action">
                                        <span class="filter_content_text">Khu Nghỉ Dưỡng</span>
                                    </div>

                                </div>
                            </div>

                            <div class="filter_content">
                                <div class="filter_content_box">
                                    <div class="filter_content_title">
                                        <span>Thương Hiệu</span>

                                    </div>
                                    @foreach ($brand as $brand)
                                        <div class="filter_content_Checkbox">
                                            <input type="checkbox" value="{{ $brand->brand_id }}" name="brand"
                                                class="action"> <span
                                                class="filter_content_text">{{ $brand->brand_name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="filter_content">
                                <div class="filter_content_box">
                                    <div class="filter_content_title">
                                        <span>Khu Vực</span>

                                    </div>
                                    @foreach ($area as $area)
                                        <div class="filter_content_Checkbox">
                                            <input type="checkbox" value="{{ $area->area_id }}" name="area"
                                                class="action"> <span
                                                class="filter_content_text">{{ $area->area_name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="layout_seafood">
                        <div class="sort">
                            <ul class="sort_ul">
                                <li class="sort_li">Sắp Xếp : </li>

                                <li class="sort_li sort_li_border button-search" id="newproduct"><label
                                        for="sort_one">Mới
                                        Nhất</label></li>
                                <input hidden type="radio" name="sort_master" id="sort_one" value="new"
                                    class="action">

                                <li class="sort_li sort_li_border button-search" id="maxprice"><label
                                        for="sort_two">Giá Cao
                                        Nhất</label></li>
                                <input hidden type="radio" name="sort_master" id="sort_two" value="max_price"
                                    class="action">

                                <li class="sort_li sort_li_border button-search" id="minprice"><label
                                        for="sort_three">Giá Thấp
                                        Nhất</label></li>
                                <input hidden type="radio" name="sort_master" id="sort_three" value="min_price"
                                    class="action">

                                <li class="sort_li sort_li_border button-search"><label for="sort_four">Thịnh Hành
                                        Nhất</label></li>
                                <input hidden type="radio" name="sort_master" id="sort_four" value="trend"
                                    class="action">

                                <li class="sort_li"><label for="sort_five">Đánh Giá Cao</label></li>
                                <input hidden type="radio" name="sort_master" id="sort_five" value="evaluate"
                                    class="action">
                            </ul>
                        </div>
                        <div class="boxitem_layout">
                            <div class="hottelpricesbox-contentbottom-layout">
                                {{-- <a href="thongtinkhachsan.html">
                                    <div class="hottelpricesbox-contentbottom-layout-item">
                                        <div class="hottelpricesbox-boxcontent_img_text">
                                            <div class="hottelpricesbox-img_1">
                                                <div class="hottelpricesbox-img_box_top">
                                                    <div class="hottelpricesbox-sale">
                                                        <span class="hottelpricesbox-sale_text">-20%</span>
                                                    </div>
                                                    <div class="hottelpricesbox-love">
                                                        <i class="fa-solid fa-heart"></i>
                                                    </div>
                                                </div>
                                                <div class="hottelpricesbox-img_box_bottom">
                                                    <div class="hottelpricesbox-img_box_bottom_evaluate">
                                                        <span class="hottelpricesbox-img_box_bottom_evaluate_text">Ưu
                                                            Đãi Nhất</span>
                                                    </div>
                                                    <div class="hottelpricesbox-img_box_bottom_img">
                                                        <img height="54px" width="42px" style="object-fit: cover;"
                                                            src="assets/img/khachsan/trending/icon_tag_travellers_2021.svg"
                                                            alt="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hottelpricesbox-text">
                                                <div class="hottelpricesbox-text-title">
                                                    Khách Sạn Đà Nẵng
                                                </div>
                                                <div class="hottelpricesbox-text-star">
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                </div>
                                                <div class="hottelpricesbox-place">
                                                    <div>
                                                        <i class="fa-solid fa-location-dot"></i>
                                                        Quận hải châu
                                                    </div>
                                                </div>
                                                <div class="hottelpricesbox-evaluate">
                                                    <div class="hottelpricesbox-text-evaluate-icon">
                                                        <i class="fa-solid fa-umbrella"></i>9.0
                                                    </div>
                                                    <div class="hottelpricesbox-text-evaluate-text">
                                                        Tuyệt vời <span style=" color:#4a5568;">(573 đánh giá)</span>
                                                    </div>
                                                </div>
                                                <div class="hottelpricesbox-text-time">
                                                    Vừa đặt cách đây vài ngày trước
                                                </div>
                                                <div class="hottelprices-box-price">
                                                    <div class="hottelprices_text-box-price-one">
                                                        <span>1.047.000đ</span>
                                                    </div>
                                                    <div class="hottelprices_text-box-price-two">
                                                        <span>1.047.000đ</span>
                                                    </div>
                                                    <div class="hottelprices_text-box-price-three">
                                                        <div class="hottelprices_text-box-price-three-l">
                                                            <div class="hottelprices_text-box-price-three-l-1"><span>Mã
                                                                    : </span></div>
                                                            <div class="hottelprices_text-box-price-three-l-2">
                                                                <span>NHAN21IT3</span>
                                                            </div>
                                                            <div class="hottelprices_text-box-price-three-l-3">-5%
                                                            </div>
                                                        </div>
                                                        <div class="hottelprices_text-box-price-three-r">
                                                            <span>954.177đ</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a> --}}
                            </div>
                            <div></div>
                            {{-- <a href="{!! $all_product->nextPageUrl() !!}">
                                <div class="flashsalehotel-button">
                                    <div class="flashsalehotel_btn">
                                        <span>Xem Thêm</span>
                                    </div>
                                </div>
                            </a> --}}
                            <div></div>
                        </div>
                    </div>
                </div>
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
    <script src="{{ asset('public/fontend/assets/js/mastersearch.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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

    {{-- Master Search --}}
    {{-- Tìm Kiếm Khu Vực Và Tên Khách Sạn --}}
    <script>
        var area_id = '<?php if (isset($_GET['area_id'])) {
            echo $_GET['area_id'];
        } ?>';
        var hotel_name = '<?php if (isset($_GET['hotel_name'])) {
            echo $_GET['hotel_name'];
        } ?>';

        if (area_id != '' || hotel_name != '') {
            $.ajax({
                url: '{{ url('tim-kiem/search-name-or-area') }}',
                method: 'get',
                data: {
                    area_id: area_id,
                    hotel_name: hotel_name,
                },
                success: function(data) {
                    $('.hottelpricesbox-contentbottom-layout').html(data);

                },
                error: function() {
                    // alert("Bug Huhu :<<");
                }
            })
        }
    </script>
    {{-- Về Khoảng Giá --}}
    <script>
        var price_one = '';
        var price_two = '';

        $("#slider-range").slider({
            range: true,
            min: {{ $min_price }},
            max: {{ $max_price }},
            values: [500, 1000],
            slide: function(event, ui) {
                $("#amount").val(ui.values[0] + "k - " + ui.values[1] + "k");
                price_one = ui.values[0];
                price_two = ui.values[1];

                var list_id_brand = [];
                var list_id_area = [];
                var list_id_star = [];
                var list_id_type_hotel = [];
                var list_id_type_sort = '';
                var searchbyvoice = $('#searchbyvoice').val();

                $.each($("input[name='brand']:checked"), function() {
                    list_id_brand.push($(this).val());
                });
                $.each($("input[name='area']:checked"), function() {
                    list_id_area.push($(this).val());
                });
                $.each($("input[name='star']:checked"), function() {
                    list_id_star.push($(this).val());
                });
                $.each($("input[name='hotel_type']:checked"), function() {
                    list_id_type_hotel.push($(this).val());
                });
                $.each($("input[name='sort_master']:checked"), function() {
                    list_id_type_sort = $(this).val();
                });


                $.ajax({
                    url: '{{ url('tim-kiem/handle-mastersearch') }}',
                    method: 'get',
                    data: {
                        searchbyvoice: searchbyvoice,
                        price_one: ui.values[0],
                        price_two: ui.values[1],
                        list_id_brand: list_id_brand,
                        list_id_area: list_id_area,
                        list_id_star: list_id_star,
                        list_id_type_hotel: list_id_type_hotel,
                        list_id_type_sort: list_id_type_sort,
                    },
                    success: function(data) {
                        $('.hottelpricesbox-contentbottom-layout').html(data);

                    },
                    error: function() {
                        // alert("Bug Huhu :<<");
                    }
                })
            }
        });
    </script>

    {{-- Ô tìm kiếm --}}
    <script>
        $('#searchbyvoice').keyup(function() {
            var list_id_brand = [];
            var list_id_area = [];
            var list_id_star = [];
            var list_id_type_hotel = [];
            var list_id_type_sort = '';
            var searchbyvoice = $('#searchbyvoice').val();

            $.each($("input[name='brand']:checked"), function() {
                list_id_brand.push($(this).val());
            });
            $.each($("input[name='area']:checked"), function() {
                list_id_area.push($(this).val());
            });
            $.each($("input[name='star']:checked"), function() {
                list_id_star.push($(this).val());
            });
            $.each($("input[name='hotel_type']:checked"), function() {
                list_id_type_hotel.push($(this).val());
            });
            $.each($("input[name='sort_master']:checked"), function() {
                list_id_type_sort = $(this).val();
            });

            $.ajax({
                url: '{{ url('tim-kiem/handle-mastersearch') }}',
                method: 'GET',
                data: {
                    searchbyvoice: searchbyvoice,
                    price_one: price_one,
                    price_two: price_two,
                    list_id_brand: list_id_brand,
                    list_id_area: list_id_area,
                    list_id_star: list_id_star,
                    list_id_type_hotel: list_id_type_hotel,
                    list_id_type_sort: list_id_type_sort,

                },
                success: function(data) {
                    $('.hottelpricesbox-contentbottom-layout').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });
    </script>



    {{-- js voice --}}
    <script>
        const searchForm = document.querySelector("#search-form");
        const searchFormInput = searchForm.querySelector("input"); // <=> document.querySelector("#search-form input");
        const info = document.querySelector(".info");

        // The speech recognition interface lives on the browser’s window object
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition; // if none exists -> undefined

        if (SpeechRecognition) {
            console.log("Your Browser supports speech Recognition");
            const recognition = new SpeechRecognition();
            recognition.continuous = true;
            recognition.lang = "vi-VN";
            // recognition.lang = "en-US";

            searchForm.insertAdjacentHTML("beforeend",
                '<button style=" margin-right: 20px" type="button"><i class="fas fa-microphone"></i></button>');
            searchFormInput.style.paddingRight = "50px";

            const micBtn = searchForm.querySelector("button");
            const micIcon = micBtn.firstElementChild;

            micBtn.addEventListener("click", micBtnClick);

            function micBtnClick() {
                if (micIcon.classList.contains("fa-microphone")) { // Start Voice Recognition
                    recognition.start(); // First time you have to allow access to mic!
                } else {
                    recognition.stop();
                }
            }

            recognition.addEventListener("start", startSpeechRecognition); // <=> recognition.onstart = function() {...}
            function startSpeechRecognition() {
                micIcon.classList.remove("fa-microphone");
                micIcon.classList.add("fa-microphone-slash");
                searchFormInput.focus();
                message_toastr("info", "Trình Tìm Kiếm Bằng Giọng Nói Đã Được Kích Hoạt !");

                console.log("Voice activated, SPEAK");
            }

            recognition.addEventListener("end", endSpeechRecognition); // <=> recognition.onend = function() {...}
            function endSpeechRecognition() {
                micIcon.classList.remove("fa-microphone-slash");
                micIcon.classList.add("fa-microphone");
                searchFormInput.focus();
                message_toastr("info", "Trình Tìm Kiếm Bằng Giọng Nói Bị Vô Hiệu !");
                console.log("Speech recognition service disconnected");
            }

            recognition.addEventListener("result",
                resultOfSpeechRecognition
            ); // <=> recognition.onresult = function(event) {...} - Fires when you stop talking
            function resultOfSpeechRecognition(event) {
                const current = event.resultIndex;
                const transcript = event.results[current][0].transcript;
                if (transcript.toLowerCase().trim() === "stop recording") {
                    recognition.stop();
                } else if (!searchFormInput.value) {
                    searchFormInput.value = transcript;

                    var list_id_brand = [];
                    var list_id_area = [];
                    var list_id_star = [];
                    var list_id_type_hotel = [];
                    var list_id_type_sort = '';
                    var searchbyvoice = $('#searchbyvoice').val();

                    $.each($("input[name='brand']:checked"), function() {
                        list_id_brand.push($(this).val());
                    });
                    $.each($("input[name='area']:checked"), function() {
                        list_id_area.push($(this).val());
                    });
                    $.each($("input[name='star']:checked"), function() {
                        list_id_star.push($(this).val());
                    });
                    $.each($("input[name='hotel_type']:checked"), function() {
                        list_id_type_hotel.push($(this).val());
                    });
                    $.each($("input[name='sort_master']:checked"), function() {
                        list_id_type_sort = $(this).val();
                    });

                    $.ajax({
                        url: '{{ url('tim-kiem/handle-mastersearch') }}',
                        method: 'GET',
                        data: {
                            searchbyvoice: transcript,
                            price_one: price_one,
                            price_two: price_two,
                            list_id_brand: list_id_brand,
                            list_id_area: list_id_area,
                            list_id_star: list_id_star,
                            list_id_type_hotel: list_id_type_hotel,
                            list_id_type_sort: list_id_type_sort,

                        },
                        success: function(data) {
                            $('.hottelpricesbox-contentbottom-layout').html(data);
                        },
                        error: function() {
                            alert("Bug Huhu :<<");
                        }
                    })

                } else {
                    // if (transcript.toLowerCase().trim() === "go") {
                    //     searchForm.submit();
                    // } else 
                    if (transcript.toLowerCase().trim() === "reset input") {
                        searchFormInput.value = "";
                    } else {
                        searchFormInput.value = transcript;
                    }
                }

                //searchFormInput.value = transcript;
                // searchFormInput.focus();
                // setTimeout(() => {
                //   searchForm.submit();
                // }, 500);

                /* Sau 5s Thì Reset Input */
                setTimeout(() => {
                    searchFormInput.value = "";
                    // recognition.stop();
                }, 5000);
            }

            // info.textContent = 'Voice Commands: "stop recording", "reset input", "go"';

        } else {
            // console.log("Your Browser does not support speech Recognition");
            // info.textContent = "Your Browser does not support Speech Recognition";
        }
    </script>

    {{-- Search Xử Lý --}}
    <script>
        $(".action").on("click", function() {
            var list_id_brand = [];
            var list_id_area = [];
            var list_id_star = [];
            var list_id_type_hotel = [];
            var list_id_type_sort = '';
            var searchbyvoice = $('#searchbyvoice').val();

            $.each($("input[name='brand']:checked"), function() {
                list_id_brand.push($(this).val());
            });
            $.each($("input[name='area']:checked"), function() {
                list_id_area.push($(this).val());
            });
            $.each($("input[name='star']:checked"), function() {
                list_id_star.push($(this).val());
            });
            $.each($("input[name='hotel_type']:checked"), function() {
                list_id_type_hotel.push($(this).val());
            });
            $.each($("input[name='sort_master']:checked"), function() {
                list_id_type_sort = $(this).val();
            });

            $.ajax({
                url: '{{ url('tim-kiem/handle-mastersearch') }}',
                method: 'GET',
                data: {
                    searchbyvoice: searchbyvoice,
                    price_one: price_one,
                    price_two: price_two,
                    list_id_brand: list_id_brand,
                    list_id_area: list_id_area,
                    list_id_star: list_id_star,
                    list_id_type_hotel: list_id_type_hotel,
                    list_id_type_sort: list_id_type_sort,

                },
                success: function(data) {
                    $('.hottelpricesbox-contentbottom-layout').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })

        });
    </script>

    {{-- Xóa Tất Cả Bộ Lọc --}}
    <script>
        $('.filter_cancel').click(function() {
            $('.action').prop('checked', false);
            $('#searchbyvoice').val('');
        });
    </script>

    {{-- Xem Gần Đây Bằng localStorage --}}
    <script>
        $(document).on('click', '.add_recently_viewed', function() {
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
    <script>
        $('.button-search').on('click', function() {
            $('.button-search').removeClass('selected');
            $(this).addClass('selected');
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
