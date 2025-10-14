<?php

namespace App\Http\Controllers;

use App\Models\Evaluate;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\Models\Statistical;
use Carbon\Carbon;
session_start();

class CheckOrderCustomerController extends Controller
{
    /* Kiểm Tra Đơn Đặt Phòng */
    public function show_check_order()
    {
        $customer_id = session()->get('customer_id');
        if (!$customer_id) {
            $meta = array(
                'title' => 'Kiểm Tra Đơn Phòng',
                'description' => 'MyHotel - Trang Tìm Kiếm Và Đặt Phòng Khách Sạn Trong Khu Vực Đà Nẵng',
                'keywords' => 'Khách Sạn Đà Nẵng , Đà Nẵng , Du Lịch , Đặt Khách Sạn , Khách Sạn Giá Rẻ',
                'canonical' => request()->url(),
                'sitename' => 'nhuandeptraivanhanbro.doancoso2.laravel.vn',
                'image' => '',
            );
            return view('pages.checkorder')->with(compact('meta'));
        } else {
            return redirect('kiem-tra-don-hang/thong-tin-don-hang?customer_id=' . $customer_id);
        }
    }

    public function check_order(Request $request)
    {
        $order_code = $request->order_code;
        $email_or_phone_order = $request->email_or_phone_order;

        $check_order = Order::join('tbl_orderer', 'tbl_orderer.orderer_id', '=', 'tbl_order.orderer_id')
            ->join('tbl_customers', 'tbl_customers.customer_id', '=', 'tbl_orderer.customer_id')
            ->where('tbl_order.order_code', $order_code)
            ->where(function ($query) use ($email_or_phone_order) {
                $query->where('tbl_customers.customer_phone', '=', $email_or_phone_order)
                    ->orWhere('tbl_customers.customer_email', '=', $email_or_phone_order);
            })->first();
        if (!$check_order) {
            $check_order = Order::join('tbl_orderer', 'tbl_orderer.orderer_id', '=', 'tbl_order.orderer_id')
                ->where('tbl_order.order_code', $order_code)
                ->where(function ($query) use ($email_or_phone_order) {
                    $query->where('tbl_orderer.orderer_phone', '=', $email_or_phone_order)
                        ->orWhere('tbl_orderer.orderer_email', '=', $email_or_phone_order);
                })->first();
        }

        if ($check_order) {
            echo 'true';
        } else {
            echo 'false';
        }

    }

    /* Chi Tiết Đơn Đặt Phòng */
    public function order_infomation(Request $request)
    {
        $meta = array(
            'title' => 'Kiểm Tra Đơn Phòng',
            'description' => 'MyHotel - Trang Tìm Kiếm Và Đặt Phòng Khách Sạn Trong Khu Vực Đà Nẵng',
            'keywords' => 'Khách Sạn Đà Nẵng , Đà Nẵng , Du Lịch , Đặt Khách Sạn , Khách Sạn Giá Rẻ',
            'canonical' => request()->url(),
            'sitename' => 'nhuandeptraivanhanbro.doancoso2.laravel.vn',
            'image' => '',
        );
        if ($request->customer_id) {
            $orderer = Orderer::where('customer_id', $request->customer_id)->get('orderer_id')->toArray();
            $list_id_orderer = array();
            foreach ($orderer as $key => $v_orderer) {
                $list_id_orderer[$key] = $v_orderer['orderer_id'];
            }
            $order = Order::wherein('orderer_id', $list_id_orderer)->orderby('order_id', 'DESC')->get();
        } elseif ($request->order_code) {
            $order = Order::where('order_code', $request->order_code)->get();
        }
        return view('pages.orderdetails')->with(compact('meta', 'order', ));
    }

    public function loading_order_status(Request $request)
    {
        $order = Order::where('order_code', $request->order_code)->first();
        if ($order->order_status == 0) {
            $status = 'Đang Chờ Duyệt';
        } elseif ($order->order_status == -1) {
            $status = 'Đã Bị Hủy';
        } elseif ($order->order_status == -2) {
            $status = 'Đã Hủy';
        } elseif ($order->order_status == 1 || $order->order_status == 2) {
            $status = 'Đã Duyệt';
        }
        $output = '
        <div class="info-customer-right-status">
            ' . $status . '
        </div>';

        if ($order->order_status == 0) {
            $output .= '<button id="btn-cancel-order" class="info-customer-right-btn" data-order_code=' . $request->order_code . '>Huỷ Đơn</button>';
        } elseif ($order->order_status == 1) {
            $output .= '<button id="boxdanhgia" class="info-customer-right-btn btn-info-order" data-order_code=' . $request->order_code . '>Đánh Giá</button>';
        }
        echo $output;
    }

    public function insert_evaluate(Request $request)
    {
        $data = $request->all();
        $evaluate = new Evaluate();

        $orderdetail = OrderDetails::where('order_code', $data['order_code'])->first();
        if (session()->has('customer_id')) {
            $evaluate->customer_id = session()->get('customer_id');
            $evaluate->customer_name = session()->get('customer_name');
        } else {
            $evaluate->customer_name = $orderdetail->order->orderer->orderer_name;
        }
        $evaluate->hotel_id = $orderdetail->hotel_id;
        $evaluate->room_id = $orderdetail->room_id;
        $evaluate->type_room_id = $orderdetail->type_room_id;
        $evaluate->evaluate_title = $data['title_evaluate'];
        $evaluate->evaluate_content = $data['content_evaluate'];
        $evaluate->evaluate_loaction_point = $data['loaction_point'];
        $evaluate->evaluate_service_point = $data['service_point'];
        $evaluate->evaluate_price_point = $data['price_point'];
        $evaluate->evaluate_sanitary_point = $data['sanitary_point'];
        $evaluate->evaluate_convenient_point = $data['convenient_point'];
        $evaluate->save();

        $order = Order::where('order_code', $data['order_code'])->first();
        $order->order_status = 2;
        $order->save();
        echo 'true';
    }

    public function cancel_order(Request $request)
    {
        $order = Order::where('order_code', $request->order_code)->first();
        $order->order_status = -2;
        $order->save();

        /* Hoàn Lại Số Lượng Mã Giảm Giá (Nếu Có) Và Số Lượng Phòng*/
        if ($order['coupon_name_code'] != 'Không có') {
            $coupon = Coupon::where('coupon_name_code', $order['coupon_name_code'])->first();
            $coupon->coupon_qty_code = $coupon->coupon_qty_code + 1;
            $coupon->save();
        }
        $type_room = TypeRoom::where('type_room_id', $order->orderdetails->type_room_id)->first();
        $type_room->type_room_quantity = $type_room->type_room_quantity + 1;
        $type_room->save();

        $this->statistical();

        echo 'true';
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

    public function message($type, $content)
    {
        $message = array(
            "type" => "$type",
            "content" => "$content",
        );
        session()->flash('message', $message);
    }

}
