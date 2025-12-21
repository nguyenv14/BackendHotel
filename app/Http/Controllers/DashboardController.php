<?php

namespace App\Http\Controllers;

use Analytics;
use App\Models\Admin;
use App\Models\Customers;
use App\Models\Order;
use App\Models\Statistical;
use App\Models\Evaluate;
use App\Models\ManipulationActivity;
use App\Models\Hotel;
use App\Models\OrderDetails;
use App\Models\Orderer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $filter_type = $request->input('filter_type', 'month'); // day, month, year
        $date = $request->input('date', Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d'));

        // Khởi tạo query base - chỉ lấy đơn khách sạn
        $query = Order::where('order_type', 0);

        // Áp dụng filter theo ngày tháng năm dựa trên start_day
        if ($filter_type == 'day') {
            $query = $query->whereDate('start_day', $date);
        } elseif ($filter_type == 'month') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $startDate = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfMonth()->format('Y-m-d');
            $query = $query->whereBetween('start_day', [$startDate, $endDate]);
        } elseif ($filter_type == 'year') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $startDate = $carbonDate->copy()->startOfYear()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfYear()->format('Y-m-d');
            $query = $query->whereBetween('start_day', [$startDate, $endDate]);
        }

        // Lấy tất cả orders
        $orders = $query->get();

        // Group theo ngày/tháng/năm tùy theo filter_type
        $groupedData = [];
        
        foreach ($orders as $order) {
            $orderDate = Carbon::createFromFormat('Y-m-d', $order->start_day);
            
            // Xác định key để group
            if ($filter_type == 'day') {
                $key = $order->start_day;
            } elseif ($filter_type == 'month') {
                $key = $orderDate->format('Y-m-d'); // Hiển thị từng ngày trong tháng
            } else { // year
                $key = $orderDate->format('Y-m-d'); // Hiển thị từng ngày trong năm
            }

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'order_date' => $key,
                    'sales' => 0,
                    'order_refused' => 0,
                    'price_order_refused' => 0,
                    'quantity_order_room' => 0,
                    'total_order' => 0
                ];
            }

            // Tính tổng số đơn
            $groupedData[$key]['total_order']++;

            // Tính doanh thu (đơn hoàn thành: status = 1 hoặc 2)
            if ($order->order_status == 1 || $order->order_status == 2) {
                $groupedData[$key]['sales'] += ($order->total_price ?? 0);
                
                // Tính số phòng từ order_details
                $orderDetails = DB::table('tbl_order_details')
                    ->where('order_code', $order->order_code)
                    ->count();
                $groupedData[$key]['quantity_order_room'] += $orderDetails;
            }

            // Tính đơn hủy (status = -1 hoặc -2)
            if ($order->order_status == -1 || $order->order_status == -2) {
                $groupedData[$key]['order_refused']++;
                $groupedData[$key]['price_order_refused'] += ($order->total_price ?? 0);
            }
        }

        // Chuyển thành array và sắp xếp theo ngày
        $chart_data = array_values($groupedData);
        usort($chart_data, function($a, $b) {
            return strcmp($a['order_date'], $b['order_date']);
        });

        return response()->json($chart_data);
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
        // $visitors = Analytics::fetchVisitorsAndPageViews(Period::days(30));
        // $chart_data = array();
        // foreach ($visitors as $value) {
        //     $date = $value['date']->format('Y-m-d');
        //     $chart_data[] = array(
        //         'date' => $date,
        //         'pageViews' => $value['pageViews'],
        //         'visitors' => $value['visitors'],
        //     );
        // }

        // $data = json_encode($chart_data);
        // echo $data;

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

    /**
     * Lấy top khách sạn có đơn đặt cao nhất với filter theo ngày tháng năm
     */
    public function top_hotels_by_orders(Request $request)
    {
        $filter_type = $request->input('filter_type', 'month'); // day, month, year
        $date = $request->input('date', Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d'));
        
        // Đơn giản hóa query - tính doanh thu từ order trực tiếp
        $query = DB::table('tbl_order')
            ->join('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
            ->join('tbl_hotel', 'tbl_order_details.hotel_id', '=', 'tbl_hotel.hotel_id')
            ->where('tbl_order.order_type', 0) // Chỉ lấy đơn khách sạn
            ->where('tbl_order.order_status', '!=', -1) // Loại bỏ đơn bị hủy
            ->where('tbl_order.order_status', '!=', -2);

        // Áp dụng filter theo ngày tháng năm
        if ($filter_type == 'day') {
            $query->whereDate('tbl_order.created_at', $date);
        } elseif ($filter_type == 'month') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $query->whereYear('tbl_order.created_at', $carbonDate->year)
                  ->whereMonth('tbl_order.created_at', $carbonDate->month);
        } elseif ($filter_type == 'year') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $query->whereYear('tbl_order.created_at', $carbonDate->year);
        }

        // Lấy dữ liệu order với hotel để tính toán
        $orders = $query->select(
                'tbl_hotel.hotel_id',
                'tbl_hotel.hotel_name',
                'tbl_order.order_code',
                'tbl_order.total_price'
            )
            ->get()
            ->groupBy('hotel_id');

        // Tính số đơn và doanh thu cho mỗi hotel
        $hotelData = [];
        foreach ($orders as $hotelId => $hotelOrders) {
            $uniqueOrders = $hotelOrders->unique('order_code');
            $hotelData[] = (object)[
                'hotel_id' => $hotelId,
                'hotel_name' => $hotelOrders->first()->hotel_name,
                'total_orders' => $uniqueOrders->count(),
                'total_revenue' => $uniqueOrders->sum('total_price')
            ];
        }

        // Sắp xếp theo số đơn giảm dần và lấy top 10
        usort($hotelData, function($a, $b) {
            return $b->total_orders - $a->total_orders;
        });
        $hotels = collect(array_slice($hotelData, 0, 10));

        $chart_data = array();
        foreach ($hotels as $hotel) {
            $chart_data[] = array(
                'hotel_name' => $hotel->hotel_name,
                'total_orders' => (int)$hotel->total_orders,
                'total_revenue' => (float)$hotel->total_revenue
            );
        }

        return response()->json($chart_data);
    }

    /**
     * Lấy top người đặt phòng nhiều nhất
     */
    public function top_customers_by_orders(Request $request)
    {
        $filter_type = $request->input('filter_type', 'month'); // day, month, year
        $date = $request->input('date', Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d'));
        
        // Sử dụng subquery để tránh lỗi GROUP BY
        $baseQuery = DB::table('tbl_order')
            ->join('tbl_orderer', 'tbl_order.orderer_id', '=', 'tbl_orderer.orderer_id')
            ->leftJoin('tbl_customers', 'tbl_orderer.customer_id', '=', 'tbl_customers.customer_id')
            ->where('tbl_order.order_type', 0) // Chỉ lấy đơn khách sạn
            ->where('tbl_order.order_status', '!=', -1) // Loại bỏ đơn bị hủy
            ->where('tbl_order.order_status', '!=', -2);

        // Áp dụng filter theo ngày tháng năm
        if ($filter_type == 'day') {
            $baseQuery->whereDate('tbl_order.start_day', $date);
        } elseif ($filter_type == 'month') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $baseQuery->whereYear('tbl_order.start_day', $carbonDate->year)
                  ->whereMonth('tbl_order.start_day', $carbonDate->month);
        } elseif ($filter_type == 'year') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $baseQuery->whereYear('tbl_order.start_day', $carbonDate->year);
        }
        // Group by theo customer_id hoặc orderer_id thực sự
        // Sử dụng CAST để tránh lỗi collation
        $customers = $baseQuery->select(
                'tbl_customers.customer_id',
                'tbl_orderer.orderer_id',
                DB::raw('MAX(COALESCE(CAST(tbl_customers.customer_name AS CHAR), CAST(tbl_orderer.orderer_name AS CHAR))) as customer_name'),
                DB::raw('MAX(COALESCE(CAST(tbl_customers.customer_email AS CHAR), CAST(tbl_orderer.orderer_email AS CHAR))) as customer_email'),
                DB::raw('MAX(COALESCE(CAST(tbl_customers.customer_phone AS CHAR), CAST(tbl_orderer.orderer_phone AS CHAR))) as customer_phone'),
                DB::raw('COUNT(DISTINCT tbl_order.order_code) as total_orders'),
                DB::raw('COALESCE(SUM(tbl_order.total_price), 0) as total_spent')
            )
            ->groupBy('tbl_customers.customer_id', 'tbl_orderer.orderer_id')
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get();
        $chart_data = array();
        foreach ($customers as $customer) {
            $chart_data[] = array(
                'customer_name' => $customer->customer_name ?? 'Khách vãng lai',
                'total_orders' => (int)$customer->total_orders,
                'total_spent' => (float)($customer->total_spent ?? 0)
            );
        }

        return response()->json($chart_data);
    }

}
