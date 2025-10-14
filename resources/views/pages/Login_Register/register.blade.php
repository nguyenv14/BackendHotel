<div class="fromsignup-overlay" id="overlay">
</div>
<form id="form-register" class="fromsignup" action="" method="post">
    <div class="fromsignup-close">
        <i class="close-box fa-solid fa-xmark"></i>
    </div>
    <div class="fromsignup-box">
        <div style="padding-bottom: 20px" class="fromsignup-title">
            <label for=""><span>Đăng ký</span></label>
        </div>
        @csrf
        {{-- <a href="user/login-facebook">
            <div class="fromsignup-item">
                <div class="fromsignup-item-logo">
                    <!-- <i class="fa-brands fa-facebook"></i> -->
                    <img width="30px" height="30px" style="object-fit: cover;margin-left: -4px;"
                        src="{{ asset('public/fontend/assets/img/icon/fb-login-icon.png') }}" alt="">
                </div>
                <div class="fromsignup-item-text">
                    <span style="margin-left: -3px;">Đăng ký bằng facebook</span>
                </div>
            </div>
        </a>
      <a href="user/login-google">
        <div class="fromsignup-item">
            <div class="fromsignup-item-logo">
                <img width="24px" height="24px" style="object-fit: cover;"
                    src="{{ asset('public/fontend/assets/img/icon/gg-login-icon.png') }}" alt="">
            </div>
            <div class="fromsignup-item-text">
                <span>Đăng ký bằng Google</span>
            </div>
        </div>
      </a>
        <div class="fromsignup-text">
            <span>Hoặc ký bằng số điện thoại</span>
        </div> --}}

        <div style="margin: 0;" class="fromsignup-item-input-box">
            <input id="fullname" class="fromsignup-item-input" type="text" name="customer_name_user" required>
            <label for="fullname" class="fromsignup-item-lable">Nhập tên đăng nhập</label>
            <span class="form-message"></span>
        </div>

        <div class="fromsignup-item-input-box">
            <input id="phonenumber" class="fromsignup-item-input" type="text" name="customer_phone_user" required>
            <label for="" class="fromsignup-item-lable">Nhập điện
                thoại</label>
            <span class="form-message"></span>
        </div>

        <div class="fromsignup-item-input-box">
            <input id="email" class="fromsignup-item-input" type="text" name="customer_email_user" required>
            <label for="email" class="fromsignup-item-lable">Nhập email</label>
            <span class="form-message"></span>
        </div>

        <div class="fromsignup-item-input-box">
            <input id="password" class="fromsignup-item-input" type="password" name="customer_password1_user" required>
            <label for="password" class="fromsignup-item-lable">Nhập mật khẩu</label>
            <span class="form-message"></span>
        </div>

        <div class="fromsignup-item-input-box">
            <input id="password_confirmation" class="fromsignup-item-input" type="password"
                name="customer_password2_user" required>
            <label for="password_confirmation" class="fromsignup-item-lable">Nhập lại mật
                khẩu</label>
            <span class="form-message"></span>
        </div>

        <div style="margin-top: 23px" class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
        
        <div>
            <input id="customer_checkbox_user" type="checkbox" name="customer_checkbox_user"
                value="on">
            <label>Tôi đồng ý với tất cả các Điều khoản & Điều kiện</label>
            <span class="form-message"></span>
        </div>

        <input style="margin-top: 10px" class="fromsignup-btn" type="button" value="Đăng ký" id="btn-register-account">
        <div style="padding-top: 16px; padding-bottom: 40px;" class="fromsignup-end">
            <span>Bạn đã có tài khoản?</span>
            <span id="loginaccount" style="cursor: pointer;color: #00b6f3;">Đăng nhập</span>
        </div>
    </div>


</form>
