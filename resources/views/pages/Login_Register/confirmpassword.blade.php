<form id="password_confir" class="password_confirmation" action="" method="post">
    @csrf
    <div for="input-fromsignup" class="fromsignup-close">
        <i class="close-box fa-solid fa-xmark"></i>
    </div>
    <div class="fromsignup-box" style="padding-bottom: 40px">
        <div style="padding-bottom: 20px" class="fromsignup-title">
            <label for=""><span>Đặt Lại Mật Khẩu</span></label>
        </div>
        <div class="fromlogin-text">
            <span style="line-height: 20px">Hãy Nhập Lại Mật Khẩu Mới ! </span>
        </div>
        <div style="margin: 0px;" class="fromsignup-item-input-box">
            <input id="newpass" class="fromsignup-item-input" type="text" name="password1" required>
            <label for="" class="fromsignup-item-lable">Nhập Mật Khẩu Của Bạn</label>
            <span class="form-message"></span>
        </div>
        <div style="margin-top: 26px;" class="fromsignup-item-input-box">
            <input id="newpassconfir" class="fromsignup-item-input" type="text" name="password2" required>
            <label for="" class="fromsignup-item-lable">Nhập Lại Mật Khẩu</label>
            <span class="form-message"></span>
        </div>
        <input style="margin-top: 28px" class="fromsignup-btn" type="button" value="Gửi" id="submit_password_confirmation">
    </div>
</form>