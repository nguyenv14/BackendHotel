<?php

namespace App\Http\Controllers;

use Analytics;
use App\Models\Admin;
use App\Models\Customers;
use App\Models\Order;
use App\Models\Statistical;
use App\Models\Evaluate;
use App\Models\ManipulationActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Analytics\Period;

session_start();

class DashboardController extends Controller
{
    public function show_dashboard()
    {
       /* Doanh Thu Hôm Nay */
       $now = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
       $statical = Statistical::where('order_date', $now)->first();
       if($statical){
        $todays_revenue = $statical->sales;
       }else{
        $todays_revenue = 0;
       }
       /* Đơn Hàng Hôm Nay */
       $todays_order = Order::where('created_at', 'like', $now . '%')->count();  
       /* Đánh Giá Hôm Nay */
       $evaluate_order = Evaluate::where('created_at', 'like', $now . '%')->count();  
       /* Đếm Số Khách Hàng Đang Online */
       $startOfDay = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
       $endOfDay = Carbon::now('Asia/Ho_Chi_Minh')->endOfDay();
       $ip_customer = request()->ip();
       $count_customer_online = ManipulationActivity::distinct()->where('manipulation_activity_type',1)->whereBetween('created_at', [$startOfDay, $endOfDay])->where('manipulation_activity_ip',$ip_customer)->count('manipulation_activity_ip');

        $count_admin = Admin::count();
        $count_customer = Customers::count();
        $count_order = Order::count();

        // $pages_one_day = Analytics::fetchMostVisitedPages(Period::days(30));
        // Truy xuất các trình duyệt hàng đầu
        // $top_browser = Analytics::fetchTopBrowsers(Period::days(365));
        return view('admin.dashboard')->with(compact('count_admin', 'count_customer', 'count_order','todays_revenue','todays_order','evaluate_order','count_customer_online'));
    }

    public function chart_statistical(Request $request)
    {

        $statistical = Statistical::orderby('statistical_id','ASC')->get();
        $chart_data = array();

        foreach ($statistical as $value) {
            $chart_data[] = array(
                'order_date' => $value->order_date,
                'sales' => $value->sales,
                'order_refused' => $value->order_refused,
                'price_order_refused' => $value->price_order_refused,
                'quantity_order_room' => $value->quantity_order_room,
                'total_order' => $value->total_order,
            );
        }
        $data = json_encode($chart_data);
        echo $data;
    }

    public function chart_visitors(Request $request)
    {
         //Truy xuất dữ liệu khách truy cập và số lần xem trang trong 15 ngày
        // $visitors = Analytics::fetchVisitorsAndPageViews(Period::days(15));
        // dd($visitors);
        // Truy xuất dữ liệu lấy tổng số khách truy cập và số lần xem trang
        //$total_visitors = Analytics::fetchTotalVisitorsAndPageViews(Period::days(1));

        // Truy xuất các liên kết giới thiệu hàng đầu
        // $top_referrers = Analytics::fetchTopReferrers(Period::days(7));

        // Truy xuất loại người dùng
        // $user_types = Analytics::fetchUserTypes(Period::days(7));

        // Lấy các trang được truy cập nhiều nhất trong ngày
        //$pages = Analytics::fetchMostVisitedPages(Period::days(1));

        // Truy xuất các trình duyệt hàng đầu
        //  $top_browser = Analytics::fetchTopBrowsers(Period::days(365));

        //Truy xuất dữ liệu khách truy cập và số lần xem trang trong 30 ngày
        $visitors = Analytics::fetchVisitorsAndPageViews(Period::days(30));
        $chart_data = array();
        foreach ($visitors as $value) {
            $date = $value['date']->format('Y-m-d');
            $chart_data[] = array(
                'date' => $date,
                'pageViews' => $value['pageViews'],
                'visitors' => $value['visitors'],
            );
        }

        $data = json_encode($chart_data);
        echo $data;

    }

    public function statistical(){
        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $statical = Statistical::where('order_date', $now)->first();
        if ($statical) {
            $order = Order::where('created_at', 'like', $now . '%')->get();
            $statical->total_order = $order->count();

            $order_completion = Order::where('created_at', 'like', $now . '%')->where('order_status', 1)->get();
            
            if ($order_completion->count()) {
                $sales = 0;
                $quantity_order_room = 0;
                foreach ($order_completion as $v_order) {
                    $price_room = $v_order->orderdetails->price_room;
                    $hotel_fee = $v_order->orderdetails->hotel_fee;
                    if ($v_order->coupon_name_code != 'Không Có') {
                        $coupon_sale_price = $v_order->coupon_sale_price;
                    } else {
                        $coupon_sale_price = 0;
                    }
                    $sales = $sales + ($price_room + $hotel_fee - $coupon_sale_price);
                    $count_orderdetails = Order::where('order_code', $v_order->order_code)->count();
                    $quantity_order_room = $quantity_order_room + $count_orderdetails;
                }
                $statical->sales = $sales;
                $statical->quantity_order_room = $quantity_order_room;
            }

            $order_ref = Order::where('created_at', 'like', $now . '%')
                ->where(function ($query) {
                    $query->where('order_status', -1)
                        ->orwhere('order_status', -2);
                })->get();

            if ($order_ref->count()) {
                $price_order_refused = 0;
                $order_refused = $order_ref->count();
                foreach ($order_ref as $v_order) {
                    $price_room = $v_order->orderdetails->price_room;
                    $hotel_fee = $v_order->orderdetails->hotel_fee;
                    if ($v_order->coupon_name_code != 'Không Có') {
                        $coupon_sale_price = $v_order->coupon_sale_price;
                    } else {
                        $coupon_sale_price = 0;
                    }
                    $price_order_refused = $price_order_refused + ( $price_room + $hotel_fee - $coupon_sale_price );
                }
                $statical->price_order_refused = $price_order_refused;
                $statical->order_refused = $order_refused;
            }
            $statical->save();
        } else {
            $statis = new Statistical();
            $statis->order_date = $now;
            $statis->sales = 0;
            $statis->order_refused = 0;
            $statis->price_order_refused = 0;
            $statis->quantity_order_room = 0;
            $statis->total_order = 0;
            $statis->save();
        }
    }


}
