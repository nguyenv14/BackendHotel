<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TypeRoom;
use App\Models\Coupon;
use Carbon\Carbon;

class Room extends Model
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
    ];
    public $timestamps = false;
    protected $fillable = [
        'hotel_id', 'room_name', 'room_amount_of_people', 'room_acreage', 'room_view', 'room_status', /* Trường Trong Bảng */
    ];
    protected $primaryKey = 'room_id'; /* Khóa Chính */
    protected $table = 'tbl_room'; /* Tên Bảng */
    public function hotel()
    {
      return $this->belongsTo('App\Models\Hotel', 'hotel_id');
    }
    public function typeroom(){
        return $this->belongsTo('App\Models\TypeRoom', 'room_id' , 'room_id');
    }
    public function galleryroom(){
        return $this->belongsTo('App\Models\GalleryRoom', 'room_id' , 'room_id');
    }

    public function loading_type_room($room_id){

        $type_room = TypeRoom::where('room_id', $room_id)->get();
        $Room = Room::where('room_id', $room_id)->first();
        $hotel_id = $Room['hotel_id'];
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
        $output = '';
        foreach ($type_room as $key => $typeroom) {
            $coupon_rd = array_rand($coupons->toarray());
            $output .= '
            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-BoxLayout">

            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left">
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Title">
                    <span>Lựa chọn ' . ($key+1) . '</span>
                </div>
                <div style="color: #48bb78;"
                    class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                    <i class="fa-regular fa-credit-card"></i>
                    <span style="margin-left: 5px;">Miễn phí hoàn hủy</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                    <i class="fas fa-kitchen-set"></i>
                    <span style="margin-left: 5px;">Không bao gồm bữa ăn sáng</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span style="margin-left: 5px;">Chính sách hành khách và trẻ em</span>
                </div>
                <div style="color:#ed8936;"
                    class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                    <i class="fa-solid fa-bolt"></i>
                    <span style="margin-left: 5px;">Xác nhận trong 30 phút</span>
                </div>
                <div style="color: #e53e3e;"
                    class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                    <span>Chỉ còn 2 phòng trống!</span>
                </div>
            </div>

            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center">
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-box">';
            if ($typeroom->type_room_bed == 3) {
                $output .= '<div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-icon">
                    <img width="17px" height="17px" style="object-fit: cover;"
                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-text">
                    <span>1 giường đơn</span>
                </div>
                <div
                    class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-text-or">
                    <span>--Hoặc--</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-icon">
                    <img width="17px" height="17px" style="object-fit: cover;"
                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                    <img width="17px" height="17px" style="object-fit: cover;"
                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-text">
                    <span>2 giường đơn</span>
                </div>
            </div>
            </div>';
            } elseif ($typeroom->type_room_bed == 2) {
                $output .= '
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-icon">
                    <img width="17px" height="17px" style="object-fit: cover;"
                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                    <img width="17px" height="17px" style="object-fit: cover;"
                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-text">
                    <span>2 giường đơn</span>
                </div>
            </div>
        </div>';
            } elseif ($typeroom->type_room_bed == 1) {
                $output .= '
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-icon">
                    <img width="17px" height="17px" style="object-fit: cover;"
                        src="https://img.icons8.com/ios-glyphs/30/undefined/bed.png" />
                    </div>
                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-center-text">
                    <span>1 giường đơn</span>
                    </div>
                </div>
                </div>';
            }
            $output .= '
            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right"> ';
            if ($typeroom->type_room_condition == 0) {
                $price_sale = $typeroom->type_room_price;
                $output .= '
            <div  style="margin-top:23px " class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-Three">
                <span>' . number_format($price_sale, 0, ',', '.') . 'đ</span>
            </div>';
            } elseif ($typeroom->type_room_condition == 1) {
                $price_sale = $typeroom->type_room_price - ($typeroom->type_room_price / 100) * $typeroom->type_room_price_sale;
                $output .= '
            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-one">
                <span>-' . $typeroom->type_room_price_sale . '%</span>
            </div>
            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-Two">
                <span>' . number_format($typeroom->type_room_price, 0, ',', '.') . 'đ</span>
            </div>
            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-Three">
                <span>' . number_format($price_sale, 0, ',', '.') . 'đ</span>
            </div>';
            }
            $price_sale_end = $price_sale - ($price_sale / 100) * $coupons[$coupon_rd]->coupon_price_sale;
            $output .= '
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-four">
                    <span>/ phòng / đêm</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-five-box">
                    <div
                        class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-five-box-Topbox">
                        <div
                            class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-five-box-text">
                            <span>Nhập mã: </span>
                            <span style="color: #00b6f3;">' . $coupons[$coupon_rd]->coupon_name_code . '</span>
                        </div>
                        <div
                            class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-five-box-textcode">
                            <span>-' . $coupons[$coupon_rd]->coupon_price_sale . '%</span>
                        </div>
                    </div>
                    <div
                        class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-five-box-Bottom">
                        <span>' . number_format($price_sale_end, 0, ',', '.') . 'đ</span>
                    </div>
                </div>
                ';
                if($typeroom->type_room_quantity == 0){
                    $output.='
                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-six">
                        <span>Hết Phòng</span>
                    </div>
                    ';
                }else{
                    $output.='
                    <a href="'.URL('dat-phong?hotel_id='.$hotel_id.'&type_room_id='.$typeroom->type_room_id).'">
                    <div id="add-coupon-room" class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-six" data-coupon_name_code ='.$coupons[$coupon_rd]->coupon_name_code.'>
                        <span>Đặt phòng</span>
                    </div> 
                    </a>
                    ';
                }
               $output.='
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-seven">
                    <span>Giá cuối cùng</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-eight">
                    <span>(Giá cho 1 đêm, ' . $typeroom->room->room_amount_of_people . ' người lớn)</span>
                </div>
            </div>

        </div>
            ';
        }
        echo $output;
    }
}
