<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\ManipulationActivity;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use App\Models\Customers;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mail;
use Session;

session_start();

class CheckOutController extends Controller
{
    public function show_payment(Request $request)
    {
       
        if(session()->has('schedule')){
            $hotel = Hotel::where('hotel_id', $request->hotel_id)->where('hotel_status', 1)->first();
            $type_room = TypeRoom::where('type_room_id', $request->type_room_id)->where('type_room_status', 1)->first();
            $gallery_room = GalleryRoom::where('room_id', $type_room->room_id)->first();
            $room = Room::where('room_id', $type_room->room_id)->where('room_status', 1)->first();
            return view('pages.bookroom')->with(compact('hotel', 'room', 'gallery_room', 'type_room'));
        }else{
            $this->message('warning','Có lỗi khi vào trang thanh toán !');
            return redirect('/');
        }
    }

    public function price_list(Request $request)
    {
        $type_room = TypeRoom::where('type_room_id', $request->type_room_id)->where('type_room_status', 1)->first();
        $hotel_id = $type_room->room->hotel_id;
        $service_charge = ServiceCharge::where('hotel_id', $hotel_id)->first();
        $schedule = session()->get('schedule');
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-d-m');
        $coupon_name_code = session()->get('coupon');
        $coupon = Coupon::where('coupon_name_code', $coupon_name_code)->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->first();
        $output = '
        <div class="contentBox-Text-Bottom-Box">
        <div class="contentBox-Text-Bottom-Box-One">
            <div class="contentBox-Text-Bottom-Box-One-Box">
                <div class="contentBox-Text-Bottom-Box-One-Item">
                    <span>1 Phòng x ' . $schedule['total_date'] . ' Ngày</span>
                </div>
                <div class="contentBox-Text-Bottom-Box-One-Item">';
        if ($type_room->type_room_condition == 0) {
            $price_room = $type_room->type_room_price * $schedule['total_date'];
            $output .= '
                            <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                            <span style="margin-left: 17px;">' . number_format($price_room, 0, ',', '.') . 'đ</span>
                        </div>
                        <div class="contentBox-Text-Bottom-Box-One-Item-Bottom">
                            ';
        } elseif ($type_room->type_room_condition == 1) {
            $price = $type_room->type_room_price * $schedule['total_date'];
            $price_room = $price - ($price * $type_room->type_room_price_sale) / 100;
            $output .= '
                            <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                            <span style="margin-left: 17px;">' . number_format($price_room, 0, ',', '.') . 'đ</span>
                        </div>
                        <div class="contentBox-Text-Bottom-Box-One-Item-Bottom">
                            <div class="contentBox-Text-Bottom-Box-One-Item-Bottom-l">
                            <span>-' . $type_room->type_room_price_sale . '%</span>
                        </div>
                        <div class="contentBox-Text-Bottom-Box-One-Item-Bottom-r">
                            <span>' . number_format($price, 0, ',', '.') . 'đ</span>
                        </div>
                            ';
        }
        $output .= '
                    </div>
                </div>
            </div>

            <div class="contentBox-Text-Bottom-Box-One-Box">
                <div class="contentBox-Text-Bottom-Box-One-Item">
                    <span>Thuế và phí dịch vụ khách sạn</span>
                </div>
                <div class="contentBox-Text-Bottom-Box-One-Item">
                    <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                    ';
        if ($service_charge == null) {
            $price_fee = 0;
            $output .= ' <span style="margin-left: 17px;">0đ</span>';
        } else {
            if ($service_charge->servicecharge_condition == 1) {
                $price_fee = ($price_room * $service_charge->servicecharge_fee) / 100;
                $output .= ' <span style="margin-left: 17px;">' . number_format($price_fee, 0, ',', '.') . 'đ</span>';
            } else {
                $price_fee = $service_charge->servicecharge_fee;
                $output .= ' <span style="margin-left: 17px;">' . number_format($price_fee, 0, ',', '.') . 'đ</span>';
            }
        }
        $sum_price_room = $price_room + $price_fee;
        $output .= '
                    </div>
                </div>
            </div>

            <div class="contentBox-Text-Bottom-Box-One-Box">
                <div class="contentBox-Text-Bottom-Box-One-Item">
                    <span>Tổng giá phòng</span>
                </div>
                <div class="contentBox-Text-Bottom-Box-One-Item">
                    <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                        <span style="margin-left: 17px;">' . number_format($sum_price_room, 0, ',', '.') . 'đ</span>
                    </div>
                </div>
            </div>';
        if ($coupon != null) {

            if ($coupon->coupon_condition == 1) {
                $price_sale_coupon = $sum_price_room * $coupon->coupon_price_sale / 100;
            } else {
                $price_sale_coupon = $coupon_price_sale;
            }

            $output .= '
                <div class="contentBox-Text-Bottom-Box-One-Box">
                <div
                    class="contentBox-Text-Bottom-Box-One-Item contentBox-Text-Bottom-Box-One-Item-Layout">
                    <div class="contentBox-Text-Bottom-Box-One-Item-Layout-item">
                        <span>Mã giảm giá</span>
                    </div>
                    <div class="contentBox-Text-Bottom-Box-One-Item-Layout-item-Sale">
                        <span>' . $coupon['coupon_name_code'] . '</span>
                    </div>
                </div>
                <div class="contentBox-Text-Bottom-Box-One-Item">
                    <div class="contentBox-Text-Bottom-Box-One-Item-Top">
                        <span style="color: #68c78f;margin-left: 17px;">-' . number_format($price_sale_coupon, 0, ',', '.') . 'đ</span>
                    </div>
                </div>
            </div>
                ';
        } else {
            $price_sale_coupon = 0;
        }

        $total_payment = $sum_price_room - $price_sale_coupon;

        $output .= '
            <div class="Totalpayment">
                <div class="Totalpayment-left">
                    <div class="Totalpayment-left-top">
                        <span>Tổng tiền thanh toán</span>
                    </div>
                    <div class="Totalpayment-bottom">
                        <span>Đã bao gồm thuế, phí, VAT</span>
                    </div>
                </div>
                <div class="Totalpayment-right">
                    <div class="Totalpayment-right-Top">
                    <span>' . number_format($total_payment, 0, ',', '.') . 'đ</span>
                    </div>
                    <div class="Totalpayment-right-Bottom">
                        <span>(Giá cho 2 người lớn)</span>
                    </div>
                </div>
            </div>';

        if ($coupon != null) {
            $output .= '
            <div class="Content-end">
                <i class="fa-solid fa-money-bill"></i>
                <span style="margin-left: 5px;">
                    Chúc mừng! Bạn đã tiết kiệm được
                    ' . number_format($price_sale_coupon, 0, ',', '.') . 'đ</span>
            </div>
            ';
        }
        $output .= '
        </div>
    </div>
        ';
        echo $output;

        /* Thêm Toàn Bộ Dữ Liệu Vào Session Để Lưu Vào Database Cho Sau Này */
        $order_details = session()->get('order_details');
        if (!$order_details) {
            $order_details = array(
                'order_code' => '',
                'hotel_id' => '',
                'room_id' => '',
                'type_room_id' => '',
                'hotel_name' => '',
                'room_name' => '',
                'price_room' => '',
                'coupon_name_code' => '',
                'coupon_price_sale' => '',
                'hotel_fee' => '',
                'total_payment' => '',
            );
        }
        $order_details['order_code'] = 'MYHOTEL' . rand(0000, 9999);
        $order_details['hotel_id'] = $type_room->room->hotel->hotel_id;
        $order_details['room_id'] = $type_room->room->room_id;
        $order_details['type_room_id'] = $request->type_room_id;
        $order_details['hotel_name'] = $type_room->room->hotel->hotel_name;
        $order_details['room_name'] = $type_room->room->room_name;
        $order_details['price_room'] = $price_room;
        $order_details['hotel_fee'] = $price_fee;
        if ($price_sale_coupon == 0) {
            $order_details['coupon_name_code'] = 'Không Có';
        } else {
            $order_details['coupon_name_code'] = $coupon['coupon_name_code'];
        }
        $order_details['coupon_price_sale'] = $price_sale_coupon;
        $order_details['total_payment'] = $total_payment;
        session()->put('order_details', $order_details);

    }

    public function put_coupon(Request $request)
    {
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $coupon = Coupon::where('coupon_name_code', $request->coupon_name_code)
        ->where('coupon_end_date', '>=', $TimeNow)
        ->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->first();
        if ($coupon) {
            session()->put('coupon', $coupon->coupon_name_code);
        } else {
            echo 'false';
        }
    }

    public function check_coupon(Request $request)
    {
        $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $coupon = Coupon::where('coupon_name_code', $request->coupon_name_code)
        ->where('coupon_end_date', '>=', $TimeNow)
        ->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->first();
        if ($coupon) {
            session()->put('coupon', $coupon->coupon_name_code);
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function special_requirements(Request $request)
    {
        $require = session()->get('require');
        if (!$require) {
            $require = array(
                'type_bed' => '',
                'special_requirements' => '0',
                'own_require' => 'Không Có',
                'bill_require' => '0',
            );
        }
        if ($request->type_bed) {
            $require['type_bed'] = $request->type_bed;
        }

        if ($request->own_require) {
            $require['own_require'] = $request->own_require;
        }

        if ($request->bill_require) {
            if ($request->bill_require == -1) {
                $require['bill_require'] = '0';
            } else {
                $require['bill_require'] = $request->bill_require;
            }
        }

        if ($request->special_requirements) {
            if ($request->special_requirements == -1) {
                $require['special_requirements'] = 0;
            } else {
                $require['special_requirements'] = $request->special_requirements;
            }
        }
        session()->put('require', $require);

    }

    public function put_orderer(Request $request)
    {
        $orderer = session()->get('orderer');
        if (!$orderer) {
            $orderer = array(
                'orderer_name' => '',
                'orderer_phone' => '',
                'orderer_email' => '',
            );
        }

        if ($request->name) {
            $orderer['orderer_name'] = $request->name;
        }
        if ($request->phone) {
            if (is_numeric($request->phone) && preg_match('/\d/', $request->phone) === 1 && strlen($request->phone) == 10) {
                $orderer['orderer_phone'] = $request->phone;
            } else {
                echo 'false';
            }
        }

        if ($request->email) {
            if ($this->validate_email($request->email)) {
                $orderer['orderer_email'] = $request->email;
            } else {
                echo 'false';
            }
        }

        session()->put('orderer', $orderer);
    }

    public function validate_email($email)
    {
        return (preg_match("/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/", $email) || !preg_match("/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)) ? false : true;
    }
    /* TK TEST MOMO */
    /*
    Số Thẻ : 9704 0000 0000 0018
    Tên Chủ Thẻ : NGUYEN VAN A
    Ngày Phát Hành : 03/07
    OTP : OTP
    SDT : 0987654321
     */

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    public function momo_payment()
    {
        $order_details = session()->get('order_details');
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderId = $order_details['order_code'] . rand(0000, 9999); // Mã đơn hàng
        $orderInfo = "Thanh toán qua MoMo";
        $amount = $order_details['total_payment'];
        $redirectUrl = url('/thanh-toan/momo-payment-callback?order_code=' . $order_details['order_code']);
        $ipnUrl = url('/thanh-toan/momo-payment-callback?order_code=' . $order_details['order_code']);
        $extraData = "";
        $requestId = time() . "";
        $requestType = "payWithATM";
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array('partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true); // decode json

        return redirect($jsonResult['payUrl']);
    }

    public function momo_payment_callback(Request $request)
    {
        if ($request->message == "Successful.") {
            $payment = array(
                'payment_method' => 1,
                'payment_status' => 1,
            );
            session()->put('payment', $payment);
            $this->order();
            return redirect('/thanh-toan/hoa-don');
        } else {
            $order_details = session()->get('order_details');
            $this->message("warning", "Bạn Đã Hủy Thanh Toán");
            return redirect('dat-phong?hotel_id=' . $order_details['hotel_id'] . '&type_room_id=' . $order_details['room_id']);
        }
    }

    public function order()
    {
        /*
        OrderStatus = -1 : Hệ Thống Hủy Đơn
        OrderStatus = -2 : Khách Hàng Hủy Đơn
        OrderStatus = 1 : Khách Hàng Đến Khách Sạn Đã Thanh Toán Nên Duyệt Phòng
        OrderStatus = 2 : Khách Hàng Đến Khách Sạn Đã Thanh Toán Nên Duyệt Phòng - Đã Đánh Giá Phòng
        */
        /*
        payment_status = 1 : Đã Thanh Toán 
        payment_status = 0 : Chưa Thanh Toán
        
        payment_method = 1 : Momo
        payment_method = 4 : Khi Đến Nhận Phòng
        */
        // Thêm dữ liệu vào bảng payment
        $data_payment = session()->get('payment');
        $payment = new Payment();
        $payment['payment_method'] = $data_payment['payment_method'];
        $payment['payment_status'] = $data_payment['payment_status'];
        $payment->save();
        $payment_id = DB::getPdo('tbl_payment')->lastInsertId();

        // Thêm Dữ Liệu Vào Bảng Orderer
        $orderer = new Orderer();
        if (session()->get('customer_id')) {
            $orderer->customer_id = session()->get('customer_id');
        } else {
            $data_orderer = session()->get('orderer');
            $orderer->orderer_name = $data_orderer['orderer_name'];
            $orderer->orderer_phone = $data_orderer['orderer_phone'];
            $orderer->orderer_email = $data_orderer['orderer_email'];
        }
        $require = session()->get('require');
        $orderer->orderer_type_bed = $require['type_bed'];
        $orderer->orderer_special_requirements = $require['special_requirements'];
        $orderer->orderer_own_require = $require['own_require'];
        $orderer->orderer_bill_require = $require['bill_require'];
        $orderer->save();
        $orderer_id = DB::getPdo('tbl_orderer')->lastInsertId();

        //Thêm Dữ Liệu Vào Bảng Order
        $order_details = session()->get('order_details');
        $schedule = session()->get('schedule');
        $order = new Order();

        $order->orderer_id = $orderer_id;
        $order->payment_id = $payment_id;
        $order->order_status = 0;
        $order->order_code = $order_details['order_code'];
        $order->start_day = $schedule['date_start'];
        $order->end_day = $schedule['date_end'];
        if ($order_details['coupon_name_code']) {
            $order->coupon_name_code = $order_details['coupon_name_code'];
        } else {
            $order->coupon_name_code = 'Không Có';
        }

        $order->coupon_sale_price = $order_details['coupon_price_sale'];
        $order->save();

        $orderdetails = new OrderDetails();
        $orderdetails->order_code = $order_details['order_code'];
        $orderdetails->hotel_id = $order_details['hotel_id'];
        $orderdetails->hotel_name = $order_details['hotel_name'];
        $orderdetails->room_id = $order_details['room_id'];
        $orderdetails->room_name = $order_details['room_name'];
        $orderdetails->type_room_id = $order_details['type_room_id'];
        $orderdetails->price_room = $order_details['price_room'];
        $orderdetails->hotel_fee = $order_details['hotel_fee'];
        $orderdetails->save();

        /* Giảm Số Lượng Mã Giảm Giá (Nếu Có) Và Số Lượng Phòng*/
        if($order_details['coupon_name_code']){
            $coupon = Coupon::where('coupon_name_code',$order_details['coupon_name_code'])->first();
            $coupon->coupon_qty_code = $coupon->coupon_qty_code - 1;
            $coupon->save();
        }
        $type_room = TypeRoom::where('type_room_id',$order_details['type_room_id'])->first();
        $type_room->type_room_quantity = $type_room->type_room_quantity - 1;
        $type_room->save();

        ManipulationActivity::noteManipulationCustomer("Hệ Thống Lưu Đơn Hàng Vào Database");
        $this->email_order_to_customer();

    }

    public function email_order_to_customer()
    {
        $order_details = session()->get('order_details');
        if (session()->get('customer_id')) {
            $customer = Customers::where('customer_id', session()->get('customer_id'))->where('customer_status', 1)->first();
            $customer_name = $customer->customer_name;
            $customer_email = $customer->customer_email;
            $customer_phone = $customer->customer_phone;
        } else {
            $data_orderer = session()->get('orderer');
            $customer_name = $data_orderer['orderer_name'];
            $customer_email = $data_orderer['orderer_email'];
            $customer_phone =  $data_orderer['orderer_phone'];
        }

        $to_name = "MyHotel - Tìm Kiếm Khách Sạn Tại Khu Vực Đà Nẵng";
        $to_email = $customer_email;

        $data = array(
            "customer_name" => $customer_name,
            "customer_email" => $customer_email,
            "customer_phone" => $customer_phone,
            "order_details" => $order_details,
        );
        Mail::send('pages.mail', $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email)->subject("MyHotel - Yêu Cầu Đặt Phòng Của Bạn Đã Được Ghi Nhận Và Đang Chờ Xử Lý!"); //send this mail with subject
            $message->from($to_email, $to_name); //send from this mail
        });
        ManipulationActivity::noteManipulationCustomer("Hệ Thống Gửi Mail Đến Người Dùng");
    }

    public function show_receipt()
    {
        if (Session::get('order_details') != null && Session::get('payment') != null && Session::get('require') != null && Session::get('schedule') != null) {
            $orderdetails = session()->get('order_details');
            $type_room = TypeRoom::where('type_room_id', $orderdetails['type_room_id'])->first();
            $gallery_room = GalleryRoom::where('room_id', $orderdetails['room_id'])->first();
            if (session()->get('customer_id')) {
                $customer_email = Customers::where('customer_id', session()->get('customer_id'))->where('customer_status', 1)->first('customer_email');
                $customer_email = $customer_email['customer_email'];
            } else {
                $customer = session()->get('orderer');
                $customer_email = $customer['orderer_email'];
            }
            ManipulationActivity::noteManipulationCustomer("Vào Trang Hóa Đơn Kết Quả Đặt Phòng");
            return view('pages.hoadon')->with(compact('type_room', 'customer_email','gallery_room'));
        } else {
            ManipulationActivity::noteManipulationCustomer("Lỗi Khi Vào Trang Hóa Đơn Kết Quả Đặt Phòng");
            return redirect('/');
        }

    }
    public function un_set_order()
    {
        session()->forget('payment');
        // session()->forget('coupon');
        // session()->forget('order_details');
        // session()->forget('require');
        // session()->forget('schedule');
        ManipulationActivity::noteManipulationCustomer("Hệ Thống Hủy Toàn Bộ Session Liên Quan Đến Đơn Hàng- Thanh Toán");
    }

    public function direct_payment()
    {
        $payment = array(
            'payment_method' => 4,
            'payment_status' => 0,
        );
        session()->put('payment', $payment);
        $this->order();
        $this->message("success", "Đặt Phòng Thành Công!");
        ManipulationActivity::noteManipulationCustomer("Đặt Phòng Thành Công!");
        return redirect('/thanh-toan/hoa-don');
    }

    public function load_coupon(){
         /* Thời Gian Hiện Tại */
         $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
         $order_details = session()->get('order_details');
         /* Lấy Mã Giảm Giá */
         $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
         $output = '';
         foreach($coupons as $coupon){
            $output .= ' <div class="coupon_body">
            <div class="coupon_item-left">
                <span class="coupon_item-name">'.$coupon->coupon_name_code.'</span> <br>
                <span class="coupon_item-number">Khách sạn giảm đến '.$coupon->coupon_price_sale.'%</span> <br>
                <span class="coupon_item-time">Hạn sử dụng: '.$coupon->coupon_end_date.'</span> <br>
                <span class="coupon_item-">Điều kiện & thể lệ</span>
            </div>';
            if($coupon->coupon_name_code == $order_details['coupon_name_code']){
                $output.= ' 
            <div class="coupon_item-right">
                <span class="coupon_item-right-cancel">Hủy</span>
            </div>';
            }else{
                $output.= ' 
                <div class="coupon_item-right">
                    <span class="coupon_item-right-apply" data-coupon_name_code ="'.$coupon->coupon_name_code.'">Sử Dụng</span>
                </div>';
            }
        $output .= '
        </div>';
         }

         echo $output;
    }
    public function unset_coupon(){
       session()->forget('coupon');
       echo 'true';
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
