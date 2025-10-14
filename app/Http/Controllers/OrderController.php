<?php

namespace App\Http\Controllers;

use App\Models\ManipulationActivity;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\TypeRoom;
use App\Models\Coupon;
use App\Repositories\OrderRepository\OrderRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Statistical;
use Carbon\Carbon;

session_start();

class OrderController extends Controller
{
    /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $orderRepo;
    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }
    public function sort_order(Request $request)
    {
        switch ($request->type) {
            case '0':
                $result = $this->orderRepo->getAllByPaginate(5);
                break;
            case '1':
                $result = Order::where('order_status', 0)->orderby('order_id','DESC')->get();
                break;
            case '2':
                $result = Order::where('order_status', -1)->orderby('order_id','DESC')->get();
                break;
            case '3':
                $result = Order::where('order_status', -2)->orderby('order_id','DESC')->get();
                break;
            case '4':
                $result = Order::where('order_status', 1)->orwhere('order_status', 2)->orderby('order_id','DESC')->get();
                break;
            case '5':
                $result = Order::join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_status', 1)->orderby('order_id','DESC')->get();
                break;
            case '6':
                $result = Order::join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_status', 0)->orderby('order_id','DESC')->get();
                break;
            case '7':
                $result = Order::join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_method', 4)->orderby('order_id','DESC')->get();
                break;
            case '8':
                $result = Order::join('tbl_payment', 'tbl_payment.payment_id', 'tbl_order.payment_id')
                    ->where('tbl_payment.payment_method', 1)->orderby('order_id','DESC')->get();
                break;
            default:
                # code...
                break;
        }
        $output = $this->orderRepo->output_item($result);
        echo $output;
    }

    public function list_items()
    {
        $items = $this->orderRepo->getAllByPaginate(5);
        return view('admin.Order.manager_order')->with(compact('items'));
    }
    public function load_items()
    {
        $items = $this->orderRepo->getAllByPaginate(5);
        $output = $this->orderRepo->output_item($items);
        echo $output;
    }
    public function view_order(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->first();
        $orderer = Orderer::where('orderer_id', $order['orderer_id'])->first();
        $orderdetails = OrderDetails::where('order_code', $order['order_code'])->first();

        return view('admin.Order.view_order')->with(compact('orderer', 'orderdetails'));
    }

    public function update_status_item(Request $request)
    {
        $order = Order::where('order_code', $request->order_code)->first();
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
        $result = $this->orderRepo->searchIDorName($request->key_sreach);
        $output = $this->orderRepo->output_item($result);
        echo $output;
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

    public function statistical(){
        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $statical = Statistical::where('order_date', $now)->first();
        if($statical == ''){
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
                    $price_order_refused = $price_order_refused + ( $price_room + $hotel_fee - $coupon_sale_price );
                }
                $statical->price_order_refused = $price_order_refused;
                $statical->order_refused = $order_refused;
            }
            $statical->save();
        // }
    }
}
