
<form class="form-vecovery-password" action="" method="post">
    @csrf
    <div for="input-fromsignup" class="fromsignup-close">
        <i class="close-box fa-solid fa-xmark"></i>
    </div>
    <div class="fromsignup-box" style="padding-bottom: 40px">
        <div style="padding-bottom: 20px" class="fromsignup-title">
            <label for=""><span>Khôi Phục Tài Khoản</span></label>
        </div>
        <div class="fromlogin-text">
            <span style="line-height: 20px">Hãy nhập Tài Khoản hoặc Email bạn từng đăng ký ! </span>
        </div>
        <div style="margin: 0px;" class="fromsignup-item-input-box">
            <input id="EmailorAccountOld" class="fromsignup-item-input" type="text" name="emailorname" required>
            <label for="" class="fromsignup-item-lable">Nhập email hoặc tài khoản</label>
            <span class="form-message"></span>
        </div>
        <input style="margin-top: 28px" class="fromsignup-btn" type="button" value="Gửi" id="submit_emailorname">
    </div>


</form>
