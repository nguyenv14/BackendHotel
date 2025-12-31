<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\ManagerHotelRepository\ManagerHotelRepositoryInterface;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Customers;
use App\Models\FacilitiesHotel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Session;
session_start();

class ManagerHotelController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $hotelRepo;
    public function __construct(ManagerHotelRepositoryInterface $hotelRepo)
    {
        $this->hotelRepo = $hotelRepo;
    }
    public function list_items(){
        $items = $this->hotelRepo->getAllByPaginate(10);
        return view('admin.Hotel.all_hotel')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->hotelRepo->getAllByPaginate(10);
        $output =  $this->hotelRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        $areas = $this->hotelRepo->getArea();
        $brands = $this->hotelRepo->getBrand();
        $facilities = FacilitiesHotel::where('facilitieshotel_status', 1)->get();
        return view('admin.Hotel.add_hotel')->with(compact('areas','brands','facilities'));
    }
    public function save_item(Request $request){
        $result = $this->hotelRepo->insert_item($request->all(),$request->file('hotel_image'));
        if ($result === false) {
            return redirect()->back()->withInput();
        }
        return redirect('/admin/hotel/all-hotel');
    }
    public function update_status_item(Request $request){
        $result = $this->hotelRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->hotelRepo->searchIDorName($request->key_sreach);
        $output = $this->hotelRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->hotelRepo->move_bin($request->hotel_id);
        return redirect('/admin/hotel/all-hotel');
    }

    public function count_bin(){
        $result = $this->hotelRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->hotelRepo->getItemBinByPaginate(3);
        return view('admin.Hotel.soft_deleted_hotel')->with(compact('items'));
    }
    function load_bin(){
        $hotels =  $this->hotelRepo->getItemBinByPaginate(3);
        $output = $this->hotelRepo->output_item_bin($hotels);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->hotelRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->hotelRepo->delete_item($request->hotel_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->hotelRepo->restore_item($request->hotel_id);
    }







    public function index(Request $request){
        $hotel = $this->hotelRepo->find($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.index')->with(compact('hotel'));
    }
    public function edit_item(Request $request){
        $areas = $this->hotelRepo->getArea();
        $brands = $this->hotelRepo->getBrand();
        $hotel = $this->hotelRepo->find($request->hotel_id);
        $facilities = FacilitiesHotel::where('facilitieshotel_status', 1)->get();
        return view('admin.Hotel.ManagerHotel.edit_hotel')->with(compact('hotel','areas','brands','facilities'));
    }

    public function update_item(Request $request){
        $result = $this->hotelRepo->update_item($request->all(),$request->file('hotel_image'));
        return redirect('/admin/hotel/all-hotel');
    }

    /**
     * Lấy doanh thu theo tháng cho hotel cụ thể
     */
    public function hotel_revenue_by_month(Request $request)
    {
        $hotel_id = $request->input('hotel_id');
        $filter_type = $request->input('filter_type', 'month'); // day, month, year
        $date = $request->input('date', Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d'));

        // Kiểm tra quyền: hotel_manager chỉ xem được hotel của mình
        $user = Auth::user();
        if ($user->hasRoles('hotel_manager', 'admin', 'hotel_staff') && $user->hotel_id != $hotel_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Order::join('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
            ->where('tbl_order_details.hotel_id', $hotel_id)
            ->where('tbl_order.order_type', 0) // Chỉ đơn khách sạn
            ->whereIn('tbl_order.order_status', [1, 2]); // Chỉ đơn hoàn thành

        // Áp dụng filter
        if ($filter_type == 'day') {
            $query->whereDate('tbl_order.start_day', $date);
        } elseif ($filter_type == 'month') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $startDate = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('tbl_order.start_day', [$startDate, $endDate]);
        } elseif ($filter_type == 'year') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $startDate = $carbonDate->copy()->startOfYear()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfYear()->format('Y-m-d');
            $query->whereBetween('tbl_order.start_day', [$startDate, $endDate]);
        }

        // Lấy orders và group theo ngày
        $orders = $query->select(
                'tbl_order.start_day',
                'tbl_order.order_code',
                'tbl_order.total_price'
            )
            ->get();

        // Group theo ngày và tính doanh thu
        $groupedData = [];
        foreach ($orders as $order) {
            $day = $order->start_day;
            if (!isset($groupedData[$day])) {
                $groupedData[$day] = [
                    'date' => $day,
                    'revenue' => 0,
                    'order_codes' => []
                ];
            }
            
            // Chỉ tính một lần cho mỗi order_code
            if (!in_array($order->order_code, $groupedData[$day]['order_codes'])) {
                $groupedData[$day]['revenue'] += ($order->total_price ?? 0);
                $groupedData[$day]['order_codes'][] = $order->order_code;
            }
        }

        // Chuyển thành array và sắp xếp theo ngày
        $chart_data = array_values($groupedData);
        usort($chart_data, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        // Chỉ trả về date và revenue
        $result = array_map(function($item) {
            return [
                'date' => $item['date'],
                'revenue' => $item['revenue']
            ];
        }, $chart_data);

        return response()->json($result);
    }

    /**
     * Lấy top user đặt phòng trong tháng cho hotel cụ thể
     */
    public function hotel_top_customers(Request $request)
    {
        $hotel_id = $request->input('hotel_id');
        $filter_type = $request->input('filter_type', 'month');
        $date = $request->input('date', Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d'));

        // Kiểm tra quyền
        $user = Auth::user();
        if ($user->hasRoles('hotel_manager') && $user->hotel_id != $hotel_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $baseQuery = Order::join('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
            ->join('tbl_orderer', 'tbl_order.orderer_id', '=', 'tbl_orderer.orderer_id')
            ->leftJoin('tbl_customers', 'tbl_orderer.customer_id', '=', 'tbl_customers.customer_id')
            ->where('tbl_order_details.hotel_id', $hotel_id)
            ->where('tbl_order.order_type', 0)
            ->whereIn('tbl_order.order_status', [1, 2]); // Chỉ đơn hoàn thành

        // Áp dụng filter
        if ($filter_type == 'day') {
            $baseQuery->whereDate('tbl_order.start_day', $date);
        } elseif ($filter_type == 'month') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $startDate = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfMonth()->format('Y-m-d');
            $baseQuery->whereBetween('tbl_order.start_day', [$startDate, $endDate]);
        } elseif ($filter_type == 'year') {
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $startDate = $carbonDate->copy()->startOfYear()->format('Y-m-d');
            $endDate = $carbonDate->copy()->endOfYear()->format('Y-m-d');
            $baseQuery->whereBetween('tbl_order.start_day', [$startDate, $endDate]);
        }

        // Lấy dữ liệu order với customer để tính toán
        $orders = $baseQuery->select(
                'tbl_customers.customer_id',
                'tbl_orderer.orderer_id',
                'tbl_order.order_code',
                'tbl_order.total_price',
                DB::raw('COALESCE(CAST(tbl_customers.customer_name AS CHAR), CAST(tbl_orderer.orderer_name AS CHAR)) as customer_name')
            )
            ->get()
            ->groupBy(function($order) {
                return $order->customer_id ?? $order->orderer_id;
            });

        // Tính số đơn và tổng chi tiêu cho mỗi customer
        $customerData = [];
        foreach ($orders as $customerKey => $customerOrders) {
            $uniqueOrders = $customerOrders->unique('order_code');
            $firstOrder = $customerOrders->first();
            $customerData[] = (object)[
                'customer_id' => $customerKey,
                'customer_name' => $firstOrder->customer_name ?? 'Khách vãng lai',
                'total_orders' => $uniqueOrders->count(),
                'total_spent' => $uniqueOrders->sum('total_price')
            ];
        }

        // Sắp xếp theo số đơn giảm dần và lấy top 10
        usort($customerData, function($a, $b) {
            return $b->total_orders - $a->total_orders;
        });
        $customers = collect(array_slice($customerData, 0, 10));

        $chart_data = [];
        foreach ($customers as $customer) {
            $chart_data[] = [
                'customer_name' => $customer->customer_name ?? 'Khách vãng lai',
                'total_orders' => (int)$customer->total_orders,
                'total_spent' => (float)($customer->total_spent ?? 0)
            ];
        }

        return response()->json($chart_data);
    }
    
}