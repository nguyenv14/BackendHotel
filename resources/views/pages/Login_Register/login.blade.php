@if ($errors->has('g-recaptcha-response'))
    <script>
        message_toastr("warning", "Bạn Chưa Vượt Mã Xác Minh Google !", "Cảnh Báo !");
    </script>
@endif

<form class="fromlogin" action="{{ URL::to('user/login-customer') }}" method="POST">
    {{ csrf_field() }}
    <div for="input-fromlogin" class="fromlogin-close">
        <span> <i class="close-box fa-solid fa-xmark"></i></span>
    </div>
    <div class="fromlogin-box">
        <div class="fromlogin-title">
            <label for=""><span>Đăng nhập</span></label>
        </div>

        <a href="{{ URL::to('/user/login-facebook') }}">
            <div class="fromlogin-item">
                <div class="fromlogin-item-logo">
                    <!-- <i class="fa-brands fa-facebook"></i> -->
                    <img width="30px" height="30px" style="object-fit: cover;margin-left: -4px;"
                        src="{{ asset('public/fontend/assets/img/trangchu/fb-login-icon.png') }}" alt="">
                </div>
                <div class="fromlogin-item-text">
                    <span style="margin-left: -3px;">Đăng nhập bằng facebook</span>
                </div>
            </div>
        </a>

        <a href="{{ URL::to('/user/login-google') }}">
            <div class="fromlogin-item">
                <div class="fromlogin-item-logo">
                    <!-- <i class="fa-brands fa-google"></i> -->
                    <img width="24px" height="24px" style="object-fit: cover;"
                        src="{{ asset('public/fontend/assets/img/trangchu/gg-login-icon.png') }}" alt="">
                </div>
                <div class="fromlogin-item-text">
                    <span>Đăng nhập bằng Google</span>
                </div>
            </div>
        </a>

        <div style="margin-top:16px" class="fromlogin-item-input-box">
            <input id="email_login" class="fromlogin-item-input" type="text" name="customer_email" required>
            <label class="fromlogin-item-lable" for="">Email</label>
            <span class="form-message"></span>
        </div>
        <div style="margin-top: 25px;" class="fromlogin-item-input-box">
            <input id="pass_login" class="fromlogin-item-input" type="text" name="customer_password" required>
            <label class="fromlogin-item-lable" for="">Mật khẩu</label>
            <span class="form-message"></span>
        </div>

        <div style="margin-top: 25px" class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>

        <input type="submit" class="fromlogin-btn" value="Đăng nhập" id="btn-login-submit">
        <div style="cursor: pointer" id="recoverypassaccount" class="fromlogin-restore">
            <span>Khôi phục mật khẩu</span>
        </div>
        <div class="fromlogin-end">
            <span>Chưa có tài khoản?</span>
            <span id="registeraccount" style="cursor: pointer;color: #00b6f3;">Đăng ký tài khoản</span>
        </div>
    </div>
</form>
{{-- Bắt Lỗi Sự Kiện Đăng Nhập --}}
<script>
    $('.fromlogin-btn').click(function () {
    // if ($('.form-message').text() != '' || $('#email_login').val() == '' || $('#pass_login').val() != '') {
    //     $(".fromlogin").submit(function (e) {
    //         e.preventDefault();
    //     });
    // }
    // alert("haha");
})
</script>
{{-- Js về đăng ký tài khoản --}}
<script>

    $("#btn-register-account").on("click", function() {

        if (document.getElementById("customer_checkbox_user").checked == true) {

            if ($('.form-message').text() == '' && $('#fullname').val() != '' && $('#email').val() != '' &&
                $('#phonenumber').val() != '' && $('#password').val() != '' && $('#password_confirmation')
                .val() != '') {
                $(".loading").css({"display": "block"});
                $(".overlay-loading").css({"display": "block"});
                
                var customer_name = $("input[name='customer_name_user']").val();
                var customer_phone = $("input[name='customer_phone_user']").val();
                var customer_email = $("input[name='customer_email_user']").val();
                var customer_password = $("input[name='customer_password1_user']").val();
                var customer_password2 = $("input[name='customer_password2_user']").val();
                var _token = $("input[name='_token']").val();

                $.ajax({
                    url: '{{ url('/user/create-customer') }}',
                    method: 'POST',
                    data: {
                        customer_name: customer_name,
                        customer_phone: customer_phone,
                        customer_email: customer_email,
                        customer_password: customer_password,
                        customer_password2: customer_password2,
                        _token: _token,
                    },
                    success: function(data) {
                        $(".loading").css({"display": "none"});
                        $(".overlay-loading").css({"display": "none"});

                        if (data == "emailalreadyexists") {
                            message_toastr("warning", "Cảnh Báo !",
                                " Email Đã Tồn Tại Trong Hệ Thống !");
                        } else {
                            $(".fromsignup").css({
                                "display": "none"
                            });
                            $(".from-verycode").css({
                                "display": "block"
                            });
                        }
                    },
                    error: function() {
                        $(".loading").css({"display": "none"});
                        $(".overlay-loading").css({"display": "none"});
                        alert("Bug Huhu :<<");
                    }
                })
            }
        } else {
            message_toastr("warning", "Cảnh Báo !", " Bạn Chưa Đồng Ý Điều Khoản !");
        }

    });

    $("#submit_verycode").on("click", function() {
        if ($('.form-message').text() == '' && $('#very_code').val() != '') {
            $(".loading").css({"display": "block"});
            $(".overlay-loading").css({"display": "block"});

            var verycoderg = $("input[name='very_code']").val();
            var _token = $("input[name='_token']").val();

            $.ajax({
                url: '{{ url('/user/verification-code-rg') }}',
                method: 'POST',
                data: {
                    verycoderg: verycoderg,
                    _token: _token,
                },
                success: function(data) {

                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});

                    if (data == "error_very_code") {
                        message_toastr("error", "Mã Xác Nhận Sai !!! Nhập Lại Nhé Ahihi :>",
                            "Lỗi !");
                    } else {
                        message_toastr("success", "Đăng Ký Tài Khoản Thành Công !", "Thành Công !");
                        $(".from-verycode").css({
                            "display": "none"
                        });
                        $("#overlay").css({
                            "display": "none"
                        });
                        $(".fromlogin").css({
                            "display": "block"
                        });

                    }
                },
                error: function() {

                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});

                    alert("Bug Huhu :<<");
                }
            })
        }
    });

    //    Js Về Quên Mật Khẩu 
</script>

<script>
    $("#submit_emailorname").on("click", function() {
        if ($('.form-message').text() == '' && $('#EmailorAccountOld').val() != '') {

            $(".loading").css({"display": "block"});
            $(".overlay-loading").css({"display": "block"});

            var customer_name_mail = $("input[name='emailorname']").val();
            var _token = $("input[name='_token']").val();

            $.ajax({
                url: '{{ url('/user/find-account-recovery-pw') }}',
                method: 'POST',
                data: {
                    customer_name_mail: customer_name_mail,
                    _token: _token,
                },
                success: function(data) {

                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});

                    if (data == "account_not_found") {
                        message_toastr("warning", "Tại Khoản Không Tồn Tại Trong Hệ Thống!",
                            "Cảnh Báo !");
                    } else {
                        $(".code_confirmation").css({
                            "display": "block"
                        });
                        $(".form-vecovery-password").css({
                            "display": "block"
                        });
                    }
                },
                error: function() {
                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});
                    alert("Bug Huhu :<<");
                }
            })
        }

    });
    $("#submit_code_confirmation").on("click", function() {
        if ($('.form-message').text() == '' && $('#very_code_rc').val() != '') {

            $(".loading").css({"display": "block"});
            $(".overlay-loading").css({"display": "block"});

            var verycoderc = $("input[name='very_code_rc']").val();
            var _token = $("input[name='_token']").val();
            $.ajax({
                url: '{{ url('/user/verification-code-rc') }}',
                method: 'POST',
                data: {
                    verycoderc: verycoderc,
                    _token: _token,
                },
                success: function(data) {
                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});

                    if (data == "error_very_code") {
                        message_toastr("error", "Mã Xác Nhận Sai !!! Nhập Lại Nhé Ahihi :>",
                            "Lỗi !");
                    } else {
                        message_toastr("success", "Hãy Đặt Lại Mật Khẩu !",
                            "Xác Nhận Thành Công !");
                        $(".code_confirmation").css({
                            "display": "none"
                        });
                        $(".password_confirmation").css({
                            "display": "block"
                        });
                    }
                },
                error: function() {
                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});
                    alert("Bug Huhu :<<");
                }
            })
        }

    });
    $("#submit_password_confirmation").on("click", function() {
        if ($('.form-message').text() == '' && $('#newpass').val() != ''  && $('#newpassconfir').val() != '') {
            $(".loading").css({"display": "block"});
            $(".overlay-loading").css({"display": "block"});

            var password1 = $("input[name='password1']").val();
            var password2 = $("input[name='password2']").val();
            var _token = $("input[name='_token']").val();

            $.ajax({
                url: '{{ url('/user/confirm-password') }}',
                method: 'POST',
                data: {
                    password1: password1,
                    password2: password2,
                    _token: _token,
                },
                success: function(data) {
                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});
                    if (data == 'success') {
                        message_toastr("success", "Mật Khẩu Đã Được Đặt Lại!", "Hoàn Thành !");

                        $(".password_confirmation").css({
                            "display": "none"
                        });
                        $("#overlay").css({
                            "display": "none"
                        });
                        $(".fromlogin").css({
                            "display": "block"
                        });
                        /* Bug Không Xác Định */
                        $(".form-vecovery-password").css({
                            "display": "none"
                        });

                    } else {
                        message_toastr("error", "Có Lỗi Xãy Ra !", "Lỗi !");
                    }
                },
                error: function() {
                    $(".loading").css({"display": "none"});
                    $(".overlay-loading").css({"display": "none"});
                    alert("Bug Huhu :<<");
                }
            })
        }
    });
</script>
