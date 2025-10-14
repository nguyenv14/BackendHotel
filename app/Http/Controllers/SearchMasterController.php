<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Hotel; 
use App\Models\Room; 
use App\Models\Brand; 
use App\Models\Area;
use App\Models\Coupon;
use App\Models\TypeRoom; 
use Session;
use Carbon\Carbon;
session_start();

class SearchMasterController extends Controller
{
    public function show_mastersearch(Request $request){
        $brand = Brand::get();
        $area = Area::get();


        $min_price = TypeRoom::orderby('type_room_price','ASC')->first();
        if($min_price['type_room_condition'] == 1){ 
            $min_price = $min_price['type_room_price'] - ($min_price['type_room_price'] / 100 * $min_price['type_room_price_sale']);
            $min_price = substr( $min_price, 0, -3); /* Cắt chuỗi 3 số sau cùng */
        }else{
            $min_price = $min_price['type_room_price'];
            $min_price = substr( $min_price, 0, -3); /* Cắt chuỗi 3 số sau cùng */
        }
        $max_price = TypeRoom::orderby('type_room_price','DESC')->first();
        $max_price = $max_price['type_room_price'];
        $max_price = substr( $max_price, 0, -3); /* Cắt chuỗi 3 số sau cùng */

        return view('pages.searchmaster')->with(compact('brand','area','min_price','max_price'));
    }
    
    public function search_name_or_area(Request $request){
        $hotels = Hotel::join('tbl_room','tbl_hotel.hotel_id','=','tbl_room.hotel_id')
        ->join('tbl_area','tbl_hotel.area_id','=','tbl_area.area_id')
        ->join('tbl_type_room','tbl_type_room.room_id','=','tbl_room.room_id')
        ->where('tbl_hotel.area_id','like','%'.$request->area_id)
        ->where('tbl_hotel.hotel_name','like','%'.$request->hotel_name.'%')
        ->get();
        $hotels = $this->super_unique($hotels,'hotel_name');
        $output = $this->output_master_search($hotels);
        echo $output;
    }

    public function handle_mastersearch(Request $request){
        /* Handle Input Checkbox */
        if($request->list_id_brand){
            $list_id_brand = $request->list_id_brand;
        }else{
            $brand = Brand::get();
            foreach($brand as $key => $v_brand){
                $list_id_brand[$key] = $v_brand->brand_id;
            }
        }
        if($request->list_id_area){
            $list_id_area = $request->list_id_area;
        }else{
            $area = Area::get();
            foreach($area as $key => $v_area){
                $list_id_area[$key] = $v_area->area_id;
            }
        }
        if($request->list_id_star){
            $list_id_star = $request->list_id_star;
        }else{
            $list_id_star = ['1','2','3','4','5'];
        }
        if($request->list_id_type_hotel){
            $list_id_type_hotel = $request->list_id_type_hotel;
        }else{
            $list_id_type_hotel = ['1','2','3'];
        }
        $hotel_name = $request->searchbyvoice;    
        if(!$request->price_one || !$request->price_two){
            $min_price = TypeRoom::orderby('type_room_price','ASC')->first();
            if($min_price['type_room_condition'] == 1){ 
                $price_start = $min_price['type_room_price'] - ($min_price['type_room_price'] / 100 * $min_price['type_room_price_sale']);
            }else{
                $price_start = $min_price['type_room_price'];
            }
            $max_price = TypeRoom::orderby('type_room_price','DESC')->first();
            $price_end = $max_price['type_room_price'];
        }else{
            $price_start = $request->price_one.'000';
            $price_end = $request->price_two.'000';
        }
        if(!$request->list_id_type_sort){
            $value = 'tbl_hotel.hotel_id';
            $type = 'ASC';
        }else if($request->list_id_type_sort == 'new'){
            $value = 'tbl_hotel.hotel_id';
            $type = 'DESC';
        }else if($request->list_id_type_sort == 'max_price'){
            $value = 'tbl_type_room.type_room_price';
            $type = 'DESC';
        }else if($request->list_id_type_sort == 'min_price'){
            $value = 'tbl_type_room.type_room_price';
            $type = 'ASC';
        }else if($request->list_id_type_sort == 'trend'){
            $value = 'tbl_hotel.hotel_view';
            $type = 'DESC';
        }

        $hotels = Hotel::join('tbl_room','tbl_hotel.hotel_id','=','tbl_room.hotel_id')
        ->join('tbl_area','tbl_hotel.area_id','=','tbl_area.area_id')
        ->join('tbl_type_room','tbl_type_room.room_id','=','tbl_room.room_id')
        ->wherein('tbl_hotel.brand_id',$list_id_brand)
        ->wherein('tbl_hotel.area_id', $list_id_area)
        ->wherein('tbl_hotel.hotel_rank',$list_id_star)
        ->wherein('tbl_hotel.hotel_type',$list_id_type_hotel)
        ->where('tbl_hotel.hotel_name','like','%'.$hotel_name.'%')
        ->whereBetween('tbl_type_room.type_room_price',[$price_start,$price_end])
        ->orderby($value,$type)
        ->get();
        $hotels = $this->super_unique($hotels,'hotel_name');
       $output = $this->output_master_search($hotels);
       echo $output;
        
    }

    public function output_master_search($hotels){
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
        $output = '';
        if($hotels){

            foreach($hotels as $key => $hotel){
                $output.='
                <a class="add_recently_viewed"
                href="'.URL('khach-san-chi-tiet?hotel_id=' . $hotel['hotel_id']).'"
                data-id="'.$hotel['hotel_id'] .'" data-name="'.$hotel['hotel_name'].'"
                data-star="';
                for ($i = 0; $i < $hotel['hotel_rank']; $i++){
                    $output.="<i class='fa-solid fa-star'></i>";
                }
                $output.='"
                data-area=" Quận '.$hotel['area_name'].'"
                data-url_image="'.asset('public/fontend/assets/img/hotel/'.$hotel['hotel_image']).'">';
    
                $output.="
                <div class='trendinghotel_boxcontent item'>
                    <div class='trendinghotel_boxcontent_img_text'>
                        <div class='trendinghotel_img trendinghotel_img_$key'>
                            <div class='trending_img_box_top'>
                            
                            ";
              
                                if($hotel['type_room_condition'] == 1){
                                    $output.='
                                    <div class="trending_sale">
                                        <span
                                            class="trending_sale_text">-'.$hotel['type_room_price_sale'].'%</span>
                                    </div>
                                    ';
                                }else{
                                    $output.='
                                    <div class="trending_sale_test">
                                    <span class="trending_sale_text"></span>
                                </div>
                                    ';
                                }
                                $output.='
                                <div class="trending_love">
                                    <i class="fa-solid fa-heart"></i>
                                </div>
                            </div>
    
                            <div class="trending_img_box_bottom">
                                <div class="trending_img_box_bottom_evaluate">
                                    <span class="trending_img_box_bottom_evaluate_text chunhay">Ưu Đãi
                                        Nhất</span>
                                </div>
                                <div class="trending_img_box_bottom_img">
                                    <img height="54px" width="42px" style="object-fit: cover;"
                                        src="'.asset('public/fontend/assets/img/khachsan/trending/icon_tag_travellers_2021.svg').'"
                                        alt="">
                                </div>
                            </div>
                        </div>
                        <div class="trendinghotel_text">
                            <div class="trendinghotel_text-title">
                                '.$hotel['hotel_name'].'
                            </div>
                            <div class="Box-star-type" style="display: flex; align-items: baseline">
                                <div class="recentlyviewed_boxcontent-item-star">';
                                for ($i = 0; $i < $hotel['hotel_rank']; $i++){
                                    $output.='<i class="fa-solid fa-star"></i>';
                                }
                                $output.='
                                </div>
                                <div class="hotel-type">';
                                    if ($hotel['hotel_type'] == 1){
                                        $output.= ' <span> Khách Sạn </span>';
                                    }elseif($hotel['hotel_type'] == 2){
                                        $output.= ' <span> Khách Sạn Căn Hộ </span>';
                                    }else{
                                        $output.= ' <span> Khu Nghĩ Dưỡng </span>';
                                    }     
                                $output.='
                                </div>
                            </div>
                            <div class="trendinghotel_place">
                                <div>
                                    <i class="fa-solid fa-location-dot"></i>
                                    Quận '.$hotel['area_name'].'
                                </div>
                            </div>
                            <div class="trendinghotel_text-evaluate">
                                '.$hotel->evaluate($hotel['hotel_id']).'
                            </div>
                            '.$hotel->order_time($hotel['hotel_id']).'
                            <div class="trendinghotel_text-box-price">';
                                if ($hotel['type_room_condition'] == 1){
                                    $output.='
                                <div class="trendinghotel_text-box-price-one">
                                    <span>'. number_format($hotel['type_room_price'], 0, ',', '.') .'đ</span>
                                </div>
                                <div class="trendinghotel_text-box-price-two">';
                                    $price_sale = $hotel['type_room_price'] - ($hotel['type_room_price'] / 100) * $hotel['type_room_price_sale'];
                                    $output.='
                                    <span>'.number_format($price_sale, 0, ',', '.') .'đ</span>
                                </div>';
                                }
                                else{
                                    $output.='
                                <div style="margin-top:-5px " class="trendinghotel_text-box-price-two">
                                    <span>&nbsp;</span>
                                </div>';
                                    $price_sale = $hotel['type_room_price'];
                                    $output.='
                                <div class="trendinghotel_text-box-price-two">
                                    <span>'.number_format($price_sale, 0, ',', '.') .'đ</span>
                                </div>';
                                }
                              
                                $coupon_rd = array_rand($coupons->toarray());
                                $output.='
                                <div class="trendinghotel_text-box-price-three">
                                    <div class="trendinghotel_text-box-price-three-l">
                                        <div class="trendinghotel_text-box-price-three-l-1"><span>Mã : </span>
                                        </div>
                                        <div class="trendinghotel_text-box-price-three-l-2">
                                            <span>'.$coupons[$coupon_rd]->coupon_name_code .'</span>
                                        </div>
                                        <div class="trendinghotel_text-box-price-three-l-3">
                                            '.$coupons[$coupon_rd]->coupon_price_sale.'%</div>
                                    </div>';
                                        $price_sale_end = $price_sale - ($price_sale / 100) * $coupons[$coupon_rd]->coupon_price_sale;
                                    $output.='
                                    <div class="trendinghotel_text-box-price-three-r">
                                        <span>'. number_format($price_sale_end, 0, ',', '.') .'đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .trendinghotel_img_'.($key).'{
                        width: 284px;
                        height: 160px;
                        background-size: 100%;
                        border-radius: 10px;
                        background-image: url('. asset('public/fontend/assets/img/hotel/' .$hotel['hotel_image']).');
                    }
    
                    .fix {}
    
                    ;
                </style>
            </a>
                ';
            }
        }else{
            $output .='
            <div class="container_search-empty">
                <img src="'.asset('public/fontend/assets/img/no-search-hotel.svg').'" alt="" sizes="" srcset="">
                <div style="text-align: center">
                    <p>Không tìm thấy kết quả nào với các tiêu chí tìm kiếm của bạn.</p>
                    <p>Vui lòng thay đổi tiêu chí tìm kiếm</p>
                </div>
                <div style="text-align: center">
                    <button class="btn-search-hotel">Tìm Kiếm</button>
                </div>
            </div>';
        }

        return $output;

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
}