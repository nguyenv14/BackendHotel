
<form id="code_confirmation" class="code_confirmation" action="" method="post">
    @csrf
    <div for="input-fromsignup" class="fromsignup-close">
        <i class="close-box fa-solid fa-xmark"></i>
    </div>
    <div class="fromsignup-box" style="padding-bottom: 40px">
        <div style="padding-bottom: 20px" class="fromsignup-title">
            <label for=""><span>Xác Nhận Mã Bảo Mật</span></label>
        </div>
        <div class="fromlogin-text">
            <span style="line-height: 20px">Xin Chào , Chúng Tôi Vừa Gửi Mã 8 Số Về Mail Của Bạn ! Hãy Kiểm Tra Email Của Bạn ! </span>
        </div>
        <div style="margin: 0px;" class="fromsignup-item-input-box">
            <input id="very_code_rc"  class="fromsignup-item-input" type="text" name="very_code_rc" required>
            <label for="" class="fromsignup-item-lable">Nhập Mã 8 Số Mà Bạn Vừa Nhận Được</label>
            <span class="form-message"></span>
        </div>
        <input style="margin-top: 28px" class="fromsignup-btn" type="button" value="Gửi" id="submit_code_confirmation">
    </div>


</form>