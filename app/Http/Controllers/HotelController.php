<?php

namespace App\Http\Controllers;

use App\Models\BannerADS;
use App\Models\Coupon;
use App\Models\GalleryHotel;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\OrderDetails;
use App\Models\Room;
use App\Models\TypeRoom;
use App\Models\Evaluate;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;

session_start();

class HotelController extends Controller
{
    /* Trang Khách Sạn */
    public function index()
    {
        $meta = array(
            'title' => 'Khách Sạn',
            'description' => 'MyHotel - Trang Tìm Kiếm Và Đặt Phòng Khách Sạn Trong Khu Vực Đà Nẵng',
            'keywords' => 'Khách Sạn Đà Nẵng , Đà Nẵng , Du Lịch , Đặt Khách Sạn , Khách Sạn Giá Rẻ',
            'canonical' => request()->url(),
            'sitename' => 'nhuandeptraivanhanbro.doancoso2.laravel.vn',
            'image' => '',
        );
        /* Lấy Khách Sạn Đang Thịnh Hành Dựa Vào Số Lần Đặt Phòng Trong Khách Sạn*/
        $order_details = DB::table('tbl_order_details')->select(DB::raw('hotel_id, COUNT(tbl_order_details.hotel_id) as count_hotel'))
        ->groupBy('hotel_id')->orderBy('count_hotel', 'DESC')->get(); 
      
        $list_id_order = array();
        foreach($order_details as $key => $order){ 
            $list_id_order[$key] = $order->hotel_id;
            if($key == 3){
                break;
            }
        }
        $hotel_trend = Hotel::wherein('hotel_id',$list_id_order)->where('hotel_status', 1)->get();

        /* Lấy Khách Sạn Mới Dựa Vào hotel_id DESC */
        $hotel_new = Hotel::wherenotin('hotel_id',$list_id_order)->where('hotel_status', 1)->orderby('hotel_id','DESC')->take(4)->get();
        $list_id_new = array();
        foreach($hotel_new as $key => $hotel){
            $list_id_new[$key] = $hotel->hotel_id;
        }
        /* Lấy Khách Sạn Được Xem Nhiều Nhất (Thịnh Hành) hotel_id DESC */
        $hotel_view = Hotel::wherenotin('hotel_id',$list_id_order)->wherenotin('hotel_id',$list_id_new)->where('hotel_status', 1)->orderby('hotel_view','DESC')->take(4)->get();
        $list_id_view = array();
        foreach($hotel_view as $key => $hotel){
            $list_id_view[$key] = $hotel->hotel_id;
        }
        $all_hotel = Hotel::wherenotin('hotel_id',$list_id_order)->wherenotin('hotel_id',$list_id_new)->wherenotin('hotel_id',$list_id_view)->where('hotel_status', 1)->inRandomOrder()->get();
        /* Lấy Ngẩu Nhiên Tất Cả Mã Giảm Giá*/
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
        /* Lấy Khẩu Nhiên Banner ADS */
        $BannerADS = BannerADS::where('bannerads_page', 2)->where('bannerads_status', 1)->inRandomOrder()->first();
        return view('pages.hotel')->with(compact('meta', 'hotel_trend' , 'hotel_new' ,'hotel_view', 'all_hotel' ,'coupons' ,'BannerADS'));
    }

    /* Trang Chi Tiết Khách Sạn */
    public function DetailsHotel(Request $request)
    {
        $hotel = Hotel::where('hotel_id', $request->hotel_id)->first();
        $hotel->hotel_view = $hotel->hotel_view + 1;
        $hotel->save();
        $video = GalleryHotel::where('hotel_id', $request->hotel_id)->where('gallery_hotel_type', 2)->first();
        $images_hotel = GalleryHotel::where('hotel_id', $request->hotel_id)->where('gallery_hotel_type', 1)->get();

        /* SEO */
        $representative_image = GalleryHotel::where('hotel_id', $request->hotel_id)->where('gallery_hotel_type', 1)->orderby('gallery_hotel_id', 'DESC')->first();
        $folder = preg_replace('/\s+/', '', $hotel->hotel_name);

        $meta = array(
            'title' => 'Thông Tin Khách Sạn ' . $hotel->hotel_name . '',
            'description' => $hotel->hotel_desc,
            'keywords' => $hotel->hotel_tag_keyword,
            'canonical' => request()->url(),
            'sitename' => 'nhuandeptraivanhanbro.doancoso2.laravel.vn',
            'image' => URL('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $representative_image->gallery_hotel_image),
        );
        /* END SEO */

        /* Lấy Ra Phòng Đề Xuất - Theo Tiêu Chí Phòng Được Đặt Nhiều Nhất - Dựa Vào OrderDetails */
        $orderdetails = OrderDetails::where('hotel_id', $request->hotel_id)->first('room_id');
        if ($orderdetails) {
            $suggested_room = Room::where('hotel_id', $request->hotel_id)->where('room_id', $orderdetails->room_id)->first();
        } else {
        /* Lấy Theo Phòng Mới Theo Vào */
            $suggested_room = Room::where('hotel_id', $request->hotel_id)->orderby('room_id', 'DESC')->first();
        }
        /* Kết Thúc Phòng Đề Xuất */

        /* Lấy Ra Phòng Trong Khách Sạn */
        $rooms = Room::where('hotel_id', $request->hotel_id)->wherenotin('room_id',[$suggested_room->room_id])->get();

        /* Lấy Ra Ảnh Của Tất Cả Phòng Trong Khách Sạn */
        $all_rooms = Room::where('hotel_id', $request->hotel_id)->get();
        $list_room_id = array();
        foreach ($all_rooms as $key => $room) {
            $list_room_id[$key] = $room->room_id;
        }
        $gallary_room = GalleryRoom::wherein('room_id', $list_room_id)->get();


        /* Thời Gian Hiện Tại */
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        /* Lấy Mã Giảm Giá */
        $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
        /* Khách Sạn Xung Quanh */
        $area_hotel = Hotel::where('area_id', $hotel->area_id)->wherenotin('hotel_id', [$hotel->hotel_id])->get();

        $evaluate_hotel = Evaluate::where('hotel_id', $request->hotel_id)->orderBy('evaluate_id', 'DESC')->get();

         /* Lấy Khẩu Nhiên Banner ADS */
         $BannerADS = BannerADS::where('bannerads_page', 3)->where('bannerads_status', 1)->inRandomOrder()->first();

        return view('pages.hoteldetails')->with(compact('meta', 'hotel', 'video', 'images_hotel', 'representative_image', 'rooms', 'gallary_room', 'area_hotel', 'coupons', 'suggested_room', 'evaluate_hotel','BannerADS'));
    }

    public function detail_convenient_room(Request $request)
    {
        $room_id = $request->room_id;
        $room = Room::where('room_id', $room_id)->first();
        $gallery_room = GalleryRoom::where('room_id', $room_id)->get();
        if ($room_id) {
            $output = $this->load_convenient_room($room, $gallery_room);
            return $output;
        }
    }

    public function load_convenient_room($room, $gallery_room){
        $type_room = TypeRoom::where('room_id',$room->room_id)->first();
        $output = '';
        $folder = preg_replace('/\s+/', '', $room->room_name);
        $output.= '
        <div class="box-inforooms">
        <div class="inforooms-left">
            <div class="inforooms-left-box ">
                <div id="owl-demo" class="inforooms-left-js owl-carousel owl-theme">';
                foreach ($gallery_room as $v_gallery_room) {
                    $output .= '
                        <div class="item">
                            <img width="650px" height="575px" style="object-fit: cover; border-radius: 8px;"
                            src="public/fontend/assets/img/hotel/room/gallery_' . $folder . '/' . $v_gallery_room->gallery_room_image . '" alt="' . $v_gallery_room->gallery_room_name . '">
                        </div>';
                }
            $output.= '   
                </div>
            </div>
        </div>
        <div class="inforooms-right">
            <div class="inforooms-right-box">
                <div class="inforooms-right-top">
                    <div class="inforooms-right-title">
                        <span>'.$room->room_name.'</span>
                    </div>
                    <div class="inforooms-right-btn-X">
                        <i class="fa-solid fa-x"></i>
                    </div>
                </div>
                <div class="inforooms-right-scroll">
                    <div class="inforooms-right-member">
                        <i class="fa-solid fa-user-group"></i> <span>'.$room->room_amount_of_people.' người</span>
                    </div>
                    <ul>
                        <li>Sức chứa tối đa của phòng 3</li>
                        <li>Số khách tiêu chuẩn 2</li>
                        <li>Cho phép ở thêm 1 trẻ em thỏa mãn 3 khách tối đa có thể mất thêm phí.</li>
                        <li>Chi tiết phí phụ thu vui lòng xem tại “Giá cuối cùng”</li>
                    </ul>
                    <div class="inforooms-right-size">
                        <div class="inforooms-right-item">
                            <i class="fa-solid fa-maximize"></i>
                            <span>'.$room->room_acreage.'m2</span>
                        </div>
                        <div style="margin-left: 20px;" class="inforooms-right-item">
                            <i class="fa-solid fa-eye"></i>
                            <span>'.$room->room_view.'</span>
                        </div>
                    </div>
                    <div style="color: #48bb78;" class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                        <i class="fa-regular fa-credit-card"></i>
                        <span style="margin-left: 5px;">Miễn phí hoàn hủy</span>
                    </div>
                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                        <i style="color: #4a5568;"class="fas fa-kitchen-set"></i>
                        <span style="margin-left: 5px;color: #4a5568;">Không bao gồm bữa ăn sáng</span>
                    </div>
                    <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                        <i style="color: #4a5568;" class="fa-solid fa-circle-exclamation"></i>
                        <span style="margin-left: 5px;color: #4a5568;">Chính sách hành khách và trẻ em</span>
                    </div>
                    <div style="color: #4a5568;"
                        class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item-Content">
                        Trẻ em lớn hơn 12 tuổi sẽ được xem như người lớn
                        Quý khách hàng vui lòng nhập đúng số lượng khách và tuổi để có giá chính xác
                    </div>
                    <ul>
                        <li>
                            11/06/2002
                            <ul>
                                <li>Phụ thu người lớn sẽ bị tính phí 480.000 VND.</li>
                                <li>Phụ thu trẻ em:
                                    <ul>
                                        <li>
                                            Trẻ dưới 6 tuổi được miễn phí.
                                        </li>
                                        <li>
                                            Trẻ từ 6 đến 12 tuổi sẽ bị tính phí 186.000 VND
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <div style="color: #48bb78;" class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Item">
                        <i class="fa-solid fa-bolt"></i>
                        <span style="margin-left: 5px;">Xác nhận ngay</span>
                    </div>
                    <div class="inforooms-right-tiennghi">
                        <div class="inforooms-right-tiennghi-tittle">
                            <span>Tiện nghi phòng</span>
                        </div>
                        <div class="inforooms-right-tiennghi-box">
                            <div class="inforooms-right-tiennghi-left">
                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/dieuhoa.png').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Điều hòa nhiệt độ</span>
                                    </div>
                                </div>

                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/cuaso.svg').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Cửa sổ</span>
                                    </div>
                                </div>

                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/dovesinh.svg').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Đồ vệ sinh cá nhân</span>
                                    </div>
                                </div>

                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/tivi.svg').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Màn hình ti vi</span>
                                    </div>
                                </div>


                            </div>
                            <div class="inforooms-right-tiennghi-right">
                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/dieuhoa.png').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Điều hòa nhiệt độ</span>
                                    </div>
                                </div>

                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/cuaso.svg').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Cửa sổ</span>
                                    </div>
                                </div>

                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/dovesinh.svg').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Đồ vệ sinh cá nhân</span>
                                    </div>
                                </div>

                                <div class="inforooms-right-tiennghi-left-group">
                                    <div class="inforooms-right-tiennghi-icon">
                                        <img width="24px" height="24px" style="object-fit: cover;"
                                            src="'.URL('public/fontend/assets/img/thongtinkhachsan/tienich/tivi.svg').'" alt="">
                                    </div>
                                    <div class="inforooms-right-tiennghi-text">
                                        <span>Màn hình ti vi</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="inforooms-right-bottom">';
                if($type_room->type_room_condition == 0){
                $output.='
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-Three">
                    <span>'.number_format($type_room->type_room_price,0,',','.').'đ</span>
                </div>';
                }else{
                $price_sale_end = $type_room->type_room_price - ($type_room->type_room_price / 100 * $type_room->type_room_price_sale);
                $output.='
                <div class="inforooms-right-bottom-ptsale">
                    <span>-'.$type_room->type_room_price_sale.'%</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-Two">
                    <span>'.number_format($type_room->type_room_price,0,',','.').'đ</span>
                </div>
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-Three">
                    <span>'.number_format($price_sale_end,0,',','.').'đ</span>
                </div>
                ';
                }
                $output.='
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-right-four">
                        <span>/ phòng / đêm</span>
                    </div>
                </div>
                <a href="' . URL('dat-phong?hotel_id=' . $room->hotel_id . '&type_room_id=' . $type_room->type_room_id) . '">
                <div class="inforooms-right-bottom-select-rooms">
                    <span>Chọn phòng</span>
                </div>
                </a>
            </div>
        </div>
    </div>
        ';

        return $output;
    }

    public function loading_type_room(Request $request)
    {
        $type_room = TypeRoom::where('room_id', $request->room_id)->paginate(1);
        $Room = Room::where('room_id', $request->room_id)->first();
        $hotel_id = $Room['hotel_id'];
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
        $output = '';
        foreach ($type_room as $typeroom) {
            $coupon_rd = array_rand($coupons->toarray());
            $output .= '
            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-BoxLayout">

            <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left">
                <div class="chooseroomsbox-boxcontent-bottom-text-BoxTwo-left-Title">
                    <span>Lựa chọn ' . $request->page . '</span>
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

    public function sreach_hotel_place(Request $request)
    {
        $key = $request->key;
        $results = Hotel::join('tbl_area', 'tbl_area.area_id', '=', 'tbl_hotel.area_id')
            ->where(function ($query) use ($key) {
                $query->where('tbl_hotel.hotel_name', 'like', '%' . $key . '%')
                    ->orWhere('tbl_area.area_name', 'like', '%' . $key . '%');
            })->where(function ($query) {
            $query->where('tbl_hotel.hotel_status', '=', 1);
        })->get();

        $output = '';
        foreach ($results as $result) {
            $output .= '
            <a href="' . URL('khach-san-chi-tiet?hotel_id=' . $result->hotel_id) . '">
                <div class="infosearchhotel">
                <div class="infosearchhotel-img">
                    <img width="100px" height="70px" style="border-radius: 8px;object-fit: cover;" src="' . URL('public/fontend/assets/img/hotel/' . $result->hotel_image) . '" alt="">
                </div>
                <div class="infosearchhotel-text">
                    <div class="infosearchhotel-name">
                        ' . $result->hotel_name . '
                    </div>
                    <div class="infosearchhotel-place">
                    <i class="fa-solid fa-location-dot"></i>
                    Quận ' . $result->area_name . '
                    </div>
                </div>
                </div>
            </a>
            ';
        }
        echo $output;
    }

    public function message($type, $content)
    {
        $message = array(
            "type" => "$type",
            "content" => "$content",
        );
        session()->flash('message', $message);
    }

}
