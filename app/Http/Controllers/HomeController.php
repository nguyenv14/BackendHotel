<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\ManipulationActivity;
use App\Models\Area;
use App\Models\BannerADS;
use App\Models\Hotel;
use App\Models\Coupon;
use Session;
use Carbon\Carbon;
use Location;
session_start();

class HomeController extends Controller
{
    public function index(){
        $meta = array(
            'title' => 'Trang Chủ',
            'description' => 'MyHotel - Trang Tìm Kiếm Và Đặt Phòng Khách Sạn Trong Khu Vực Đà Nẵng',
            'keywords' => 'Khách Sạn Đà Nẵng , Đà Nẵng , Du Lịch , Đặt Khách Sạn , Khách Sạn Giá Rẻ',
            'canonical' => request()->url(),
            'sitename' => 'nhuandeptraivanhanbro.doancoso2.laravel.vn',
            'image' => '',
        );
        $areas = Area::take(7)->get();
      
        $hotel_flashsale = Hotel::join('tbl_room','tbl_hotel.hotel_id','=','tbl_room.hotel_id')
        ->join('tbl_area','tbl_hotel.area_id','=','tbl_area.area_id')
        ->join('tbl_type_room','tbl_type_room.room_id','=','tbl_room.room_id')
        ->where('tbl_type_room.type_room_condition',1)
        ->orderby('tbl_type_room.type_room_price_sale','DESC')
        ->get();
        $hotel_flashsale = $this->super_unique($hotel_flashsale,'hotel_name');
        $hotel_flashsale = array_slice($hotel_flashsale, 4);
        /* Thời Gian Hiện Tại */
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        /* Lấy Mã Giảm Giá */
        $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
        $BannerADS = BannerADS::where('bannerads_page', 1)->where('bannerads_status', 1)->inRandomOrder()->first();
        return view('pages.home')->with(compact('areas','meta','hotel_flashsale','coupons','BannerADS'));

    }

    function super_unique($array,$key)
    {
       $temp_array = [];
       foreach ($array as $v) {
           if (!isset($temp_array[$v[$key]]))
           $temp_array[$v[$key]] = $v;
       }
       $array = array_values($temp_array);
       return $array;
    }

    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }

    /* Loading Hành Trình */
    public function loading_schedule(){
        $output = '';
        if(session()->has('schedule')){
            $schedule =session()->get('schedule');
            $output .= '
            <div class="BoxSearch-Bottom-Two-Go">
            <div class="BoxSearch-Bottom-Two-Go-Top">
                Ngày đến
            </div>
            <div class="BoxSearch-Bottom-Two-Go-Bottom">
                <input id="date-start"
                    style="border: none ; font-weight: 550;
                        line-height: 19px;height: 15px;font-size: 14px"
                    type="text" value="'.$schedule['date_start'].'">
            </div>
        </div>
        <div class="BoxSearch-Bottom-Two-Time">
        '.$schedule['total_date'].' <i class="fa-solid fa-moon"></i>
        </div>
        <div class="BoxSearch-Bottom-Two-Back">
            <div class="BoxSearch-Bottom-Two-Back-Top">
                Ngày về
            </div>
            <div class="BoxSearch-Bottom-Two-Back-Bottom">
                <input id="date-end"
                    style="border: none ; font-weight: 550;
                    line-height: 19px;height: 15px;font-size: 14px"
                    type="text" value="'.$schedule['date_end'].'">
            </div>
        </div>
            ';
        }
        echo $output;
    }
    /* Cập Nhật Hành Trình */
    public function update_schedule(Request $request){
        $date_start = Carbon::create($request->date_start);
        $date_end = Carbon::create($request->date_end);
        if($date_start->isToday() || $date_start->isFuture() ){
            if($date_start->lessThan($date_end)){
                $schedule = session()->get('schedule');
                $schedule['date_start'] =  $request->date_start;
                $schedule['date_end'] =  $request->date_end;
                $schedule['total_date'] =  $date_start->diffInDays($date_end);
                session()->put('schedule', $schedule);
            }
        }
    }
    /*Khởi Tạo Giá Trị*/
    public function inint_value(){
        if(!session()->has('schedule')){
            $schedule = array(
                'date_start' => Carbon::now('Asia/Ho_Chi_Minh')->startOfDay()->format('d-m-Y'),
                'date_end' =>  Carbon::now('Asia/Ho_Chi_Minh')->endOfDay()->addDay()->format('d-m-Y'),
                'total_date' => Carbon::now('Asia/Ho_Chi_Minh')->startOfDay()->diffInDays(Carbon::now('Asia/Ho_Chi_Minh')->endOfDay()->addDay())
            );
            session()->put('schedule', $schedule);
        }
     
        if(!session()->has('user')){
            $currentUserInfo = Location::get(request()->ip());
            if($currentUserInfo){
                $customer_location = $currentUserInfo->cityName;
            }
            else{
                $customer_location = 'Không xác định';
            }
            $user = array(
                'customer_ip' =>  request()->ip(),
                'customer_location' => $customer_location,
                'customer_agent' => request()->userAgent(),
            );
            session()->put('user', $user);
        } 

    }
}