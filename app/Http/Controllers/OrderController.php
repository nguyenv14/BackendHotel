<?php
namespace App\Http\Controllers;

use App\Http\Enums\OrderStatus;
use App\Models\Coupon;
use App\Models\Hotel;
use App\Models\ManipulationActivity;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\Statistical;
use App\Models\TypeRoom;
use App\Repositories\OrderRepository\OrderRepositoryInterface;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

session_start();

class OrderController extends Controller
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepo;
    protected $orderService;
    public function __construct(OrderRepositoryInterface $orderRepo, OrderService $orderService)
    {
        $this->orderRepo    = $orderRepo;
        $this->orderService = $orderService;
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
        if (! $this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
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
                return $query->where('order_status', OrderStatus::WAITING_FOR_APPROVAL)->orderBy('order_id', 'DESC')->get();

            case '2': // Đã từ chối
                return $query->where('order_status', OrderStatus::CANCELLED_BY_ADMIN)->orderBy('order_id', 'DESC')->get();

            case '3': // Đã hủy
                return $query->where('order_status', OrderStatus::CANCELLED_BY_CUSTOMER)->orderBy('order_id', 'DESC')->get();

            case '4': // Đã duyệt/Check-in/Checkout/Hoàn thành
                return $query->whereIn('order_status', [
                    OrderStatus::WAITING_FOR_APPROVAL,
                    OrderStatus::CHECK_IN,
                    OrderStatus::CHECK_OUT,
                    OrderStatus::COMPLETED,
                ])->orderBy('order_id', 'DESC')->get();

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
        return $this->orderService->listItemsOrderByHotelManagerOrOrder($request->all());
    }

    public function search_items(Request $request)
    {
        $item = $this->orderService->search($request->all());
        return $item;
    }

    public function getHotels()
    {
        $item = $this->orderService->getHotels();
        return $item;
    }

    public function load_items(Request $request)
    {
        $users = auth()->user();

        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (! $this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
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
        if (! $this->hasAnyRole($users, ['admin', 'hotel_manager'])) {
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

        $orderer      = Orderer::where('orderer_id', $order['orderer_id'])->first();
        $orderdetails = OrderDetails::where('order_code', $order['order_code'])->first();
        $order_full   = $order; // Truyền order đầy đủ để có order_status
        
        // Lấy thông tin type room
        $typeRoom = null;
        if ($orderdetails && $orderdetails->type_room_id) {
            $typeRoom = TypeRoom::where('type_room_id', $orderdetails->type_room_id)->first();
        }

        // Admin và hotel_manager dùng view riêng
        if ($this->hasRole($users, 'hotel_manager')) {
            return view('admin.Hotel.ManagerHotel.Order.view_order')->with(compact('orderer', 'orderdetails', 'order_full', 'typeRoom'));
        } else {
            return view('admin.Hotel.ManagerHotel.Order.view_order_admin')->with(compact('orderer', 'orderdetails', 'order_full', 'typeRoom'));
        }
    }

    public function update_status_item(Request $request)
    {
        $users = auth()->user();

        // Kiểm tra quyền truy cập: chỉ admin và hotel_manager
        if (! $this->hasAnyRole($users, ['admin', 'hotel_manager', 'hotel_staff'])) {
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

        $oldStatus = $order->order_status;

        // Xử lý hủy đơn (status 0 → -1 hoặc -2)
        if ($request->order_status == OrderStatus::CANCELLED_BY_ADMIN || $request->order_status == OrderStatus::CANCELLED_BY_CUSTOMER) {
            // Chỉ cho phép hủy khi đơn đang ở trạng thái chờ duyệt (status 0)
            if ($oldStatus != OrderStatus::WAITING_FOR_APPROVAL) {
                if ($request->expectsJson() || $request->ajax()) {
                    echo "error";
                    return;
                }
                return redirect()->back()->with('error', 'Đơn hàng không ở trạng thái có thể hủy');
            }

            $order->order_status = $request->order_status;
            $order->save();

            if ($request->order_status == OrderStatus::CANCELLED_BY_ADMIN) {
                ManipulationActivity::noteManipulationAdmin("Hủy Đơn Hàng ( Order Code : " . $request->order_code . ")");
            } else {
                ManipulationActivity::noteManipulationAdmin("Khách Hàng Hủy Đơn Hàng ( Order Code : " . $request->order_code . ")");
            }

            // Hoàn lại số lượng mã giảm giá và phòng
            if ($order['coupon_name_code'] != 'Không có') {
                $coupon                  = Coupon::where('coupon_name_code', $order['coupon_name_code'])->first();
                $coupon->coupon_qty_code = $coupon->coupon_qty_code + 1;
                $coupon->save();
            }
            $type_room                     = TypeRoom::where('type_room_id', $order->orderdetails->type_room_id)->first();
            $type_room->type_room_quantity = $type_room->type_room_quantity + 1;
            $type_room->save();

            if ($request->expectsJson() || $request->ajax()) {
                echo "refuse";
                return;
            }
            return redirect()->back()->with('success', 'Đơn hàng đã được hủy');
        }

        // Xử lý duyệt đơn (status 0 → 1)
        if ($request->order_status == OrderStatus::WAITING_FOR_APPROVAL) {
            // Chỉ cho phép duyệt khi đơn đang ở trạng thái chờ duyệt (status 0)
            if ($oldStatus != OrderStatus::WAITING_FOR_APPROVAL) {
                if ($request->expectsJson() || $request->ajax()) {
                    echo "error";
                    return;
                }
                return redirect()->back()->with('error', 'Đơn hàng không ở trạng thái có thể duyệt');
            }

            // Tạo mã check-in nếu chưa có
            if (! $order->checkin_code) {
                $order->checkin_code = $this->generateCheckinCode($order->order_code);
            }

            // Cập nhật status từ 0 → 1 (CHECK_IN)
            $order->order_status = OrderStatus::CHECK_IN;
            $order->save();

            // Cập nhật payment status nếu có
            $payment = Payment::where('payment_id', $order->payment_id)->first();
            if ($payment && $payment->payment_status == 0) {
                $payment->payment_status = 1;
                $payment->save();
            }

            ManipulationActivity::noteManipulationAdmin("Duyệt Đơn Hàng ( Order Code : " . $request->order_code . ")");

            // Gửi email với checkin_code
            $this->email_order_to_customer($request->order_code, OrderStatus::CHECK_IN);

            if ($request->expectsJson() || $request->ajax()) {
                echo "browser";
                return;
            }
            return redirect()->back()->with('success', 'Đơn hàng đã được duyệt thành công');
        }

        // Xử lý các trường hợp khác (checkout, complete, etc.)
        // Chỉ cập nhật nếu status thay đổi
        if ($oldStatus != $request->order_status) {
            // Kiểm tra luồng hợp lệ:
            // - Status 1 (CHECK_IN) → Status 2 (CHECK_OUT) → Status 3 (COMPLETED)
            // - Status 1 (CHECK_IN) → Status 4 (NO_SHOW) (tự động bởi cron)
            $validTransitions = [
                OrderStatus::CHECK_IN => [OrderStatus::CHECK_OUT, OrderStatus::NO_SHOW],
                OrderStatus::CHECK_OUT => [OrderStatus::COMPLETED],
            ];
            if (isset($validTransitions[$oldStatus])) {
                if (! in_array($request->order_status, $validTransitions[$oldStatus])) {
                    echo "error";
                    return;
                }
            }
            $order->order_status = $request->order_status;
            $order->save();
            // Ghi log cho các thao tác quan trọng
            if ($request->order_status == OrderStatus::CHECK_OUT) {
                ManipulationActivity::noteManipulationAdmin("Check-out Đơn Hàng ( Order Code : " . $request->order_code . ")");
            } elseif ($request->order_status == OrderStatus::COMPLETED) {
                ManipulationActivity::noteManipulationAdmin("Hoàn Thành Đơn Hàng ( Order Code : " . $request->order_code . ")");
                // Tính lại doanh thu khi hoàn thành
                $this->statistical();
            }
        }
    }

    public function email_order_to_customer($order_code, $order_status)
    {
        $order = Order::where('order_code', $order_code)->first();
        if (! $order) {
            return;
        }

        $orderdetails = OrderDetails::where('order_code', $order_code)->get();
        $orderer      = Orderer::where('orderer_id', $order->orderer_id)->first();

        if (! $orderer || ! $orderer->orderer_email) {
            return;
        }

        // Khi duyệt đơn (status = CHECK_IN): gửi mail với checkin_code
        if ($order_status == OrderStatus::CHECK_IN) {
            $orderDetail = $orderdetails->first();
            if (! $orderDetail) {
                return;
            }

            // Lấy thông tin type room
            $typeRoom = null;
            if ($orderDetail->type_room_id) {
                $typeRoom = TypeRoom::where('type_room_id', $orderDetail->type_room_id)->first();
            }

            // Chuẩn bị order_details dạng array để tương thích với template mail
            // price_room trong OrderDetails đã là base_price (giá phòng gốc)
            // hotel_fee là service_price (phí dịch vụ)
            // total_price trong order đã là tổng cuối cùng (base_price + service_price - coupon_price)

            $orderDetailsArray = [
                'order_code'        => $order->order_code,
                'hotel_name'        => $orderDetail->hotel_name,
                'room_name'         => $orderDetail->room_name,
                'type_room_bed'     => $typeRoom ? ($typeRoom->type_room_bed ?? 'N/A') : 'N/A',
                'type_room_price'   => $typeRoom ? ($typeRoom->type_room_price ?? 0) : 0,
                'type_room_condition' => $typeRoom ? ($typeRoom->type_room_condition ?? 'N/A') : 'N/A',
                'base_price'        => $orderDetail->price_room, // Giá phòng gốc (base_price)
                'price_room'        => $orderDetail->price_room, // Giá phòng gốc (để tương thích)
                'hotel_fee'         => $orderDetail->hotel_fee,  // Phí dịch vụ
                'coupon_name_code'  => $order->coupon_name_code ?? 'Không Có',
                'coupon_price_sale' => $order->coupon_sale_price ?? 0,
            ];

            $data = [
                'customer_name'     => $orderer->orderer_name,
                'customer_email'    => $orderer->orderer_email,
                'customer_phone'    => $orderer->orderer_phone,
                'order_details'     => $orderDetailsArray,
                'coupon_price_sale' => $order->coupon_sale_price ?? 0,
                'total_price'       => $order->total_price,
                'checkin_code'      => $order->checkin_code, // Gửi checkin_code khi đã duyệt
                'order_status'      => OrderStatus::CHECK_IN, // Status = 1 (CHECK_IN)
            ];

            $to_name  = "MyHotel - Tìm Kiếm Khách Sạn Tại Khu Vực Đà Nẵng";
            $to_email = $orderer->orderer_email;
            $subject  = "MyHotel - Đơn Hàng Của Bạn Đã Được Duyệt!";

            Mail::send('pages.mail', $data, function ($message) use ($to_name, $to_email, $subject) {
                $message->to($to_email)->subject($subject);
                $message->from($to_email, $to_name);
            });
        } else if ($order_status == OrderStatus::CANCELLED_BY_ADMIN) {
            // Khi từ chối đơn: gửi mail thông báo từ chối
            $type    = "Đơn Hàng " . $order->order_code . " Đã Bị Từ Chối !";
            $subject = "Đồ Án Cơ Sở 2 - Đơn Hàng Của Bạn Đã Bị Từ Chối !";

            $to_name  = "Lê Khả Nhân - Mail Laravel";
            $to_email = $orderer->orderer_email;

            $data = [
                "type"         => $type,
                "order"        => $order,
                "orderdetails" => $orderdetails,
                "order_status" => $order_status,
            ];
            Mail::send('admin.Order.email_order_to_customer', $data, function ($message) use ($to_name, $to_email, $subject) {
                $message->to($to_email)->subject($subject);
                $message->from($to_email, $to_name);
            });
        }
    }

    /**
     * Áp dụng search filter
     */
    private function applySearchFilter($query, $searchKey)
    {
        return $query->where(function ($q) use ($searchKey) {
            $q->where('order_code', 'like', '%' . $searchKey . '%')
                ->orWhere('orderer_name', 'like', '%' . $searchKey . '%')
                ->orWhere('orderer_phone', 'like', '%' . $searchKey . '%')
                ->orWhere('orderer_email', 'like', '%' . $searchKey . '%');
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
        $now      = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $statical = Statistical::where('order_date', $now)->first();
        if ($statical == '') {
            $statis                      = new Statistical();
            $statis->order_date          = $now;
            $statis->sales               = 0;
            $statis->order_refused       = 0;
            $statis->price_order_refused = 0;
            $statis->quantity_order_room = 0;
            $statis->total_order         = 0;
            $statis->save();
        }
        // if ($statical) {
        $order                 = Order::where('created_at', 'like', $now . '%')->get();
        $statical->total_order = $order->count();

        // Tính doanh thu cho các đơn đã được duyệt (status = 0, 1, 2, 3) - đã thanh toán
        $order_completion = Order::where('created_at', 'like', $now . '%')
            ->whereIn('order_status', [
                OrderStatus::WAITING_FOR_APPROVAL,
                OrderStatus::CHECK_IN,
                OrderStatus::CHECK_OUT,
                OrderStatus::COMPLETED,
            ])
            ->get();

        if ($order_completion->count()) {
            $sales               = 0;
            $quantity_order_room = 0;
            foreach ($order_completion as $v_order) {
                $price_room = $v_order->orderdetails->price_room;
                $hotel_fee  = $v_order->orderdetails->hotel_fee;
                if ($v_order->coupon_name_code != 'Không Có') {
                    $coupon_sale_price = $v_order->coupon_sale_price;
                } else {
                    $coupon_sale_price = 0;
                }
                $sales               = $sales + ($price_room + $hotel_fee - $coupon_sale_price);
                $count_orderdetails  = Order::where('order_code', $v_order->order_code)->count();
                $quantity_order_room = $quantity_order_room + $count_orderdetails;
            }
            $statical->sales               = $sales;
            $statical->quantity_order_room = $quantity_order_room;
        }

        $order_ref = Order::where('created_at', 'like', $now . '%')
            ->where(function ($query) {
                $query->where('order_status', OrderStatus::CANCELLED_BY_ADMIN)
                    ->orwhere('order_status', OrderStatus::CANCELLED_BY_CUSTOMER);
            })->get();

        if ($order_ref->count()) {
            $price_order_refused = 0;
            $order_refused       = $order_ref->count();
            foreach ($order_ref as $v_order) {
                $price_room = $v_order->orderdetails->price_room;
                $hotel_fee  = $v_order->orderdetails->hotel_fee;
                if ($v_order->coupon_name_code != 'Không Có') {
                    $coupon_sale_price = $v_order->coupon_sale_price;
                } else {
                    $coupon_sale_price = 0;
                }
                $price_order_refused = $price_order_refused + ($price_room + $hotel_fee - $coupon_sale_price);
            }
            $statical->price_order_refused = $price_order_refused;
            $statical->order_refused       = $order_refused;
        }
        $statical->save();
        // }
    }

    /**
     * Tạo mã check-in duy nhất cho đơn hàng
     *
     * @param string $order_code
     * @return string
     */
    private function generateCheckinCode($order_code)
    {
        // Tạo mã check-in dựa trên order_code và timestamp
        // Format: CHK + 4 ký tự cuối của order_code + 6 số ngẫu nhiên
        $orderSuffix  = substr($order_code, -4);
        $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $checkinCode  = 'CHK' . strtoupper($orderSuffix) . $randomNumber;

        // Kiểm tra mã đã tồn tại chưa (rất hiếm nhưng để đảm bảo)
        while (Order::where('checkin_code', $checkinCode)->exists()) {
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $checkinCode  = 'CHK' . strtoupper($orderSuffix) . $randomNumber;
        }

        return $checkinCode;
    }

    /**
     * Check-in đơn hàng (admin/hotel_manager) - Form submit
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function admin_checkin_order(Request $request)
    {
        $users = auth()->user();

        // Kiểm tra quyền truy cập
        if (! $this->hasAnyRole($users, ['admin', 'hotel_manager', 'hotel_staff'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này');
        }

        $request->validate([
            'order_code' => 'required',
            'checkin_code' => 'required',
        ]);

        $order = Order::where('order_code', $request->order_code)->first();

        if (! $order) {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }

        // Kiểm tra quyền cập nhật order cụ thể
        if ($this->hasRole($users, 'hotel_manager') || $this->hasRole($users, 'hotel_staff')) {
            $orderdetails = OrderDetails::where('order_code', $order->order_code)->first();
            if ($orderdetails->hotel_id != $users->hotel_id) {
                return redirect()->back()->with('error', 'Bạn không có quyền cập nhật order này');
            }
        }

        // Kiểm tra mã check-in
        if ($order->checkin_code != $request->checkin_code) {
            return redirect()->back()->with('error', 'Mã check-in không đúng');
        }

        // Chỉ có thể check-in khi status = 1 (CHECK_IN - đã duyệt, chờ check-in)
        if ($order->order_status != OrderStatus::CHECK_IN) {
            return redirect()->back()->with('error', 'Đơn hàng không ở trạng thái có thể check-in');
        }

        // Check-in thành công - cập nhật trạng thái từ 1 (CHECK_IN) sang 2 (CHECK_OUT)
        $order->order_status = OrderStatus::CHECK_OUT;
        $order->save();

        ManipulationActivity::noteManipulationAdmin("Check-in Đơn Hàng ( Order Code : " . $request->order_code . ")");

        return redirect()->back()->with('success', 'Check-in thành công');
    }

    /**
     * Check-out đơn hàng (admin/hotel_manager) - Form submit
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function admin_checkout_order(Request $request)
    {
        $users = auth()->user();

        // Kiểm tra quyền truy cập
        if (! $this->hasAnyRole($users, ['admin', 'hotel_manager', 'hotel_staff'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này');
        }

        $request->validate([
            'order_code' => 'required',
        ]);

        $order = Order::where('order_code', $request->order_code)->first();

        if (! $order) {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }

        // Kiểm tra quyền cập nhật order cụ thể
        if ($this->hasRole($users, 'hotel_manager') || $this->hasRole($users, 'hotel_staff')) {
            $orderdetails = OrderDetails::where('order_code', $order->order_code)->first();
            if ($orderdetails->hotel_id != $users->hotel_id) {
                return redirect()->back()->with('error', 'Bạn không có quyền cập nhật order này');
            }
        }

        // Chỉ có thể check-out khi status = 2 (CHECK_OUT - đã check-in xong)
        if ($order->order_status != OrderStatus::CHECK_OUT) {
            return redirect()->back()->with('error', 'Đơn hàng không ở trạng thái có thể check-out. Vui lòng check-in trước.');
        }

        // Check-out thành công - cập nhật trạng thái từ 2 (CHECK_OUT) sang 3 (COMPLETED)
        $order->order_status = OrderStatus::COMPLETED;
        $order->save();

        ManipulationActivity::noteManipulationAdmin("Check-out Đơn Hàng ( Order Code : " . $request->order_code . ")");

        /* Hàm Tính Doanh Thu */
        $this->statistical();

        return redirect()->back()->with('success', 'Check-out thành công');
    }

    /**
     * Hoàn thành đơn hàng (admin/hotel_manager) - Form submit
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function admin_complete_order(Request $request)
    {
        $users = auth()->user();

        // Kiểm tra quyền truy cập
        if (! $this->hasAnyRole($users, ['admin', 'hotel_manager', 'hotel_staff'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này');
        }

        $request->validate([
            'order_code' => 'required',
        ]);

        $order = Order::where('order_code', $request->order_code)->first();

        if (! $order) {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }

        // Kiểm tra quyền cập nhật order cụ thể
        if ($this->hasRole($users, 'hotel_manager') || $this->hasRole($users, 'hotel_staff')) {
            $orderdetails = OrderDetails::where('order_code', $order->order_code)->first();
            if ($orderdetails->hotel_id != $users->hotel_id) {
                return redirect()->back()->with('error', 'Bạn không có quyền cập nhật order này');
            }
        }

        // Chỉ có thể hoàn thành khi status = 3 (COMPLETED - đã checkout)
        // Thực ra status 3 đã là completed rồi, nên hàm này có thể không cần thiết
        // Nhưng giữ lại để tương thích
        if ($order->order_status == OrderStatus::COMPLETED) {
            return redirect()->back()->with('info', 'Đơn hàng đã được hoàn thành rồi');
        }

        // Hoàn thành đơn hàng
        $order->order_status = OrderStatus::COMPLETED;
        $order->save();

        ManipulationActivity::noteManipulationAdmin("Hoàn Thành Đơn Hàng ( Order Code : " . $request->order_code . ")");

        /* Hàm Tính Doanh Thu */
        $this->statistical();

        return redirect()->back()->with('success', 'Đơn hàng đã được hoàn thành');
    }
}

