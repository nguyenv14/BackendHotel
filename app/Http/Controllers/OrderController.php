<?php

namespace App\Http\Controllers;

use App\Models\ManipulationActivity;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\TypeRoom;
use App\Models\Coupon;
use App\Models\Hotel;
use App\Repositories\OrderRepository\OrderRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Statistical;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

session_start();

class OrderController extends Controller
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepo;
    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }
    
    /**
     * Kiểm tra user có role cụ thể không
     */
    private function hasRole($user, $role)
    {
        return $user->roles()->where('roles_name', $role)->exists();
    }
    
    /**
     * Kiểm tra user có bất kỳ role nào trong danh sách không
     */
    private function hasAnyRole($user, $roles)
    {
        return $user->roles()->whereIn('roles_name', $roles)->exists();
    }
    public function sort_order(Request $request)
    {
        $users = auth()->user();
        
        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (!$this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
            abort(403, 'Bạn không có quyền truy cập chức năng này');
        }
        
        // Tạo base query với phân quyền (bao gồm hotel_id từ request nếu có)
        $query = $this->getOrderQueryByRoleAndRequest2($users, $request);
        
        // Áp dụng filter theo type
        $result = $this->applySortFilter($query, $request->type, $users);
        
        $output = $this->orderRepo->output_item($result);
        echo $output;
    }
    
    /**
     * Tạo query base theo role và request (cho sort/search)
     */
    private function getOrderQueryByRoleAndRequest2($user, $request)
    {
        $query = Order::query();
        
        if ($this->hasRole($user, 'hotel_manager')) {
            // Hotel manager: lấy theo hotel_id của user
            $query->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                  ->where('tbl_order_details.hotel_id', $user->hotel_id);
        } elseif ($request->has('hotel_id')) {
            // Admin có hotel_id: lấy theo hotel được chọn
            $query->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                  ->where('tbl_order_details.hotel_id', $request->hotel_id);
        }
        // Nếu admin không có hotel_id: không filter (lấy tất cả)
        
        return $query;
    }
    
    /**
     * Tạo query base theo role của user
     */
    private function getOrderQueryByRole($user)
    {
        $query = Order::query();
        
        // Nếu là hotel_manager, chỉ lấy order của hotel riêng
        if ($this->hasRole($user, 'hotel_manager')) {
            $query->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                  ->where('tbl_order_details.hotel_id', $user->hotel_id);
        }
        
        return $query;
    }
    
    /**
     * Áp dụng filter theo type
     */
    private function applySortFilter($query, $type, $user)
    {
        switch ($type) {
            case '0': // Tất cả
                return $this->hasRole($user, 'hotel_manager') ? 
                    $query->paginate(5) : 
                    $this->orderRepo->getAllByPaginate(5);
                    
            case '1': // Chờ xử lý
                return $query->where('order_status', 0)->orderBy('order_id', 'DESC')->get();
                
            case '2': // Đã từ chối
                return $query->where('order_status', -1)->orderBy('order_id', 'DESC')->get();
                
            case '3': // Đã hủy
                return $query->where('order_status', -2)->orderBy('order_id', 'DESC')->get();
                
            case '4': // Đã duyệt/Hoàn thành
                return $query->whereIn('order_status', [1, 2])->orderBy('order_id', 'DESC')->get();
                
            case '5': // Đã thanh toán
                return $query->join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_status', 1)->orderBy('order_id', 'DESC')->get();
                    
            case '6': // Chưa thanh toán
                return $query->join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_status', 0)->orderBy('order_id', 'DESC')->get();
                    
            case '7': // Thanh toán online
                return $query->join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_method', 4)->orderBy('order_id', 'DESC')->get();
                    
            case '8': // Thanh toán tiền mặt
                return $query->join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_method', 1)->orderBy('order_id', 'DESC')->get();
                    
            default:
                return collect(); // Trả về collection rỗng
        }
    }

    public function list_items(Request $request)
    {
        $users = auth()->user();
        
        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (!$this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
            abort(403, 'Bạn không có quyền truy cập chức năng này');
        }
        
        // Hotel manager: tự động lấy hotel_id từ user
        if ($this->hasRole($users, 'hotel_manager')) {
            $hotel = Hotel::query()->where('hotel_id', $users->hotel_id)->first();
            $items = Order::query()
                ->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                ->where('tbl_order_details.hotel_id', $users->hotel_id)
                ->paginate(5);
            return view('admin.Hotel.ManagerHotel.Order.manager_order')->with(compact('items', 'hotel'));
        } 
        
        // Admin: phải có hotel_id trong request
        if ($request->has('hotel_id')) {
            $hotel = Hotel::query()->where('hotel_id', $request->hotel_id)->first();
            if (!$hotel) {
                abort(404, 'Không tìm thấy hotel');
            }
            $items = Order::query()
                ->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                ->where('tbl_order_details.hotel_id', $request->hotel_id)
                ->paginate(5);
            return view('admin.Hotel.ManagerHotel.Order.manager_order')->with(compact('items', 'hotel'));
        } else {
            // Admin không có hotel_id: hiển thị tất cả order
            $items = $this->orderRepo->getAllByPaginate(5);
            return view('admin.Hotel.ManagerHotel.Order.manager_order_admin')->with(compact('items'));
        }
    }
    public function load_items(Request $request)
    {
        $users = auth()->user();
        
        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (!$this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
            abort(403, 'Bạn không có quyền truy cập chức năng này');
        }
        
        // Lấy items theo role và hotel_id
        $items = $this->getOrderItemsByRoleAndRequest($users, $request);
        
        $output = $this->orderRepo->output_item($items);
        echo $output;
    }
    
    /**
     * Lấy danh sách order theo role của user
     */
    private function getOrderItemsByRole($user)
    {
        if ($this->hasRole($user, 'hotel_manager')) {
            // Hotel manager chỉ load order của hotel riêng
            $query = Order::query()
                ->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                ->where('tbl_order_details.hotel_id', $user->hotel_id);
            return $query->paginate(5);
        } else {
            // Admin có thể load tất cả order
            return $this->orderRepo->getAllByPaginate(5);
        }
    }
    
    /**
     * Lấy danh sách order theo role và request (có hotel_id)
     */
    private function getOrderItemsByRoleAndRequest($user, $request)
    {
        if ($this->hasRole($user, 'hotel_manager')) {
            // Hotel manager: lấy theo hotel_id của user
            $query = Order::query()
                ->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                ->where('tbl_order_details.hotel_id', $user->hotel_id);
            return $query->paginate(5);
        } elseif ($request->has('hotel_id')) {
            // Admin có hotel_id: lấy theo hotel được chọn
            $query = Order::query()
                ->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                ->where('tbl_order_details.hotel_id', $request->hotel_id);
            return $query->paginate(5);
        } else {
            // Admin không có hotel_id: lấy tất cả
            return $this->orderRepo->getAllByPaginate(5);
        }
    }
    public function view_order(Request $request)
    {
        $users = auth()->user();
        
        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (!$this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
            abort(403, 'Bạn không có quyền truy cập chức năng này');
        }
        
        $order = Order::where('order_id', $request->order_id)->first();
        
        // Kiểm tra quyền xem order cụ thể
        if ($this->hasRole($users, 'hotel_manager')) {
            // Hotel manager chỉ xem được order của hotel riêng
            $orderdetails = OrderDetails::where('order_code', $order['order_code'])->first();
            if ($orderdetails->hotel_id != $users->hotel_id) {
                abort(403, 'Bạn không có quyền xem order này');
            }
        }
        
        $orderer = Orderer::where('orderer_id', $order['orderer_id'])->first();
        $orderdetails = OrderDetails::where('order_code', $order['order_code'])->first();

        // Admin và hotel_manager dùng view riêng
        if ($this->hasRole($users, 'hotel_manager')) {
            return view('admin.Hotel.ManagerHotel.Order.view_order')->with(compact('orderer', 'orderdetails'));
        } else {
            return view('admin.Hotel.ManagerHotel.Order.view_order_admin')->with(compact('orderer', 'orderdetails'));
        }
    }

    public function update_status_item(Request $request)
    {
        $users = auth()->user();
        
        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (!$this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
            abort(403, 'Bạn không có quyền truy cập chức năng này');
        }
        
        $order = Order::where('order_code', $request->order_code)->first();
        
        // Kiểm tra quyền cập nhật order cụ thể
        if ($this->hasRole($users, 'hotel_manager')) {
            // Hotel manager chỉ cập nhật được order của hotel riêng
            $orderdetails = OrderDetails::where('order_code', $order['order_code'])->first();
            if ($orderdetails->hotel_id != $users->hotel_id) {
                abort(403, 'Bạn không có quyền cập nhật order này');
            }
        }
        
        $order->order_status = $request->order_status;
        $order->save();
        /* Còn Thiếu Xử Lý Về Sau Này */
        if ($request->order_status == 1 || $request->order_status == -1) {
            // $this->email_order_to_customer($request->order_code , $request->order_status);
        }

        if ($request->order_status == -1) {
            ManipulationActivity::noteManipulationAdmin("Hủy Đơn Hàng ( Order Code : " . $request->order_code . ")");

            /* Hoàn Lại Số Lượng Mã Giảm Giá (Nếu Có) Và Số Lượng Phòng*/
            if ($order['coupon_name_code'] != 'Không có') {
                $coupon = Coupon::where('coupon_name_code', $order['coupon_name_code'])->first();
                $coupon->coupon_qty_code = $coupon->coupon_qty_code + 1;
                $coupon->save();
            }
            $type_room = TypeRoom::where('type_room_id', $order->orderdetails->type_room_id)->first();
            $type_room->type_room_quantity = $type_room->type_room_quantity + 1;
            $type_room->save();
            /* Hàm Tính Doanh Thu */
            $this->statistical();
            echo "refuse";
        } else if ($request->order_status == 1) {
            $payment = Payment::where('payment_id', $order->payment_id)->first();
            $payment->payment_status = 1;
            $payment->save();
            ManipulationActivity::noteManipulationAdmin("Duyệt Đơn Hàng ( Order Code : " . $request->order_code . ")");
            /* Hàm Tính Doanh Thu */
            $this->statistical();
            echo "browser";
        }
    }

    public function email_order_to_customer($order_code, $order_status)
    {

        $order = Order::where('order_code', $order_code)->first();
        $orderdetails = OrderDetails::where('order_code', $order_code)->get();

        if ($order_status == 1) {
            $type = "Đơn Hàng " . $order->order_code . " Đã Được Duyệt !";
            $subject = "Đồ Án Cơ Sở 2 - Đơn Hàng Của Bạn Đã Được Duyệt !";
        } else if ($order_status == -1) {
            $type = "Đơn Hàng " . $order->order_code . " Đã Bị Từ Chối !";
            $subject = "Đồ Án Cơ Sở 2 - Đơn Hàng Của Bạn Đã Bị Từ Chối !";
        }

        $to_name = "Lê Khả Nhân - Mail Laravel";
        $to_email = $order->shipping->shipping_email;

        $data = array(
            "type" => $type,
            "order" => $order,
            "orderdetails" => $orderdetails,
        );
        Mail::send('admin.Order.email_order_to_customer', $data, function ($message) use ($to_name, $to_email, $subject) {
            $message->to($to_email)->subject($subject); //send this mail with subject
            $message->from($to_email, $to_name); //send from this mail
        });
    }
    public function search_items(Request $request)
    {
        $users = auth()->user();
        
        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (!$this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
            abort(403, 'Bạn không có quyền truy cập chức năng này');
        }
        
        // Validate input
        $searchKey = trim($request->key_sreach);
        if (empty($searchKey)) {
            return response()->json(['error' => 'Từ khóa tìm kiếm không được để trống']);
        }
        
        // Tạo query base với phân quyền (bao gồm hotel_id từ request nếu có)
        $query = $this->getOrderQueryByRoleAndRequest2($users, $request);
        
        // Áp dụng search filter
        $result = $this->applySearchFilter($query, $searchKey);
        
        $output = $this->orderRepo->output_item($result);
        echo $output;
    }
    
    /**
     * Áp dụng search filter
     */
    private function applySearchFilter($query, $searchKey)
    {
        return $query->where(function($q) use ($searchKey) {
            $q->where('order_code', 'like', '%'.$searchKey.'%')
              ->orWhere('orderer_name', 'like', '%'.$searchKey.'%')
              ->orWhere('orderer_phone', 'like', '%'.$searchKey.'%')
              ->orWhere('orderer_email', 'like', '%'.$searchKey.'%');
        })->orderBy('order_id', 'DESC')->get();
    }
    public function move_to_bin(Request $request)
    {
        $this->orderRepo->move_bin($request->order_id);
        return redirect('admin/order/all-order');
    }
    public function count_bin()
    {
        $result = $this->orderRepo->count_bin();
        echo $result;
    }
    public function list_bin()
    {
        $items = $this->orderRepo->getItemBinByPaginate(5);
        return view('admin.order.soft_deleted_order')->with(compact('items'));
    }
    public function load_bin()
    {
        $orders = $this->orderRepo->getItemBinByPaginate(5);
        $output = $this->orderRepo->output_item_bin($orders);
        echo $output;
    }
    public function search_bin(Request $request)
    {
        $output = $this->orderRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result = $this->orderRepo->delete_item($request->order_id);
    }

    public function un_bin(Request $request)
    {
        $result = $this->orderRepo->restore_item($request->order_id);
    }

    public function statistical()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $statical = Statistical::where('order_date', $now)->first();
        if ($statical == '') {
            $statis = new Statistical();
            $statis->order_date = $now;
            $statis->sales = 0;
            $statis->order_refused = 0;
            $statis->price_order_refused = 0;
            $statis->quantity_order_room = 0;
            $statis->total_order = 0;
            $statis->save();
        }
        // if ($statical) {
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
                $price_order_refused = $price_order_refused + ($price_room + $hotel_fee - $coupon_sale_price);
            }
            $statical->price_order_refused = $price_order_refused;
            $statical->order_refused = $order_refused;
        }
        $statical->save();
        // }
    }
}
