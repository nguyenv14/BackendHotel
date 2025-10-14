<?php
namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customers;
use App\Models\Evaluate;
use App\Models\GalleryRoom;
use App\Models\Hotel;
use App\Models\MenuRestaurant;
use App\Models\Order;
use App\Models\OrderDetailRestaurant;
use App\Models\OrderDetails;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ServiceCharge;
use App\Models\TypeRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApiCheckoutController extends Controller{

    public function orderRoom(Request $request){
        $roomType = TypeRoom::query()->where("type_room_id", $request->type_room_id)->first();
        $room = Room::query()->where("room_id", $roomType->room_id)->first();
        $hotel = Hotel::query()->where("hotel_id", $room->hotel_id)->first();
        $service_change = ServiceCharge::query()->where("hotel_id", $hotel->hotel_id)->first();
        $startDay = $request->startDay;
        $endDay = $request->endDay;
        $order_code = $request->order_code;
        $require_text = $request->require_text;
        $order_require = $request->order_require;
        $customer = Customers::where("customer_id", $request->customer_id)->first();
        $coupon = Coupon::where("coupon_id", $request->coupon_id)->first();
        $day = $request->day;
        $price = $roomType->type_room_price * $day;
        if($roomType->type_room_condition == 1){
            $price = $price - ($price / 100) * $roomType->type_room_price_sale;
        }
        $service_price = 0;
        if($service_change != null){
            if($service_change->servicecharge_condition == 1){
                $service_price = ($price * $service_change->servicecharge_fee) / 100;
            }else{
                $service_price = $service_change->servicecharge_fee;
            }
        }

        $coupon_price = 0;
        $coupon_code = "";
        if($coupon != null){
            $coupon_code = $coupon->coupon_name_code;
            if($coupon->coupon_condition == 1){
                $coupon_price = ($price * $coupon->coupon_price_sale) / 100;
            }else{
                $coupon_price = $coupon->coupon_price_sale;
            }
        }else{
            $coupon_code = "Không có";
        }
        $price = $price + $service_price - $coupon_price;
        $orderer = new Orderer();
        $orderer->customer_id = $customer->customer_id;
        $orderer->orderer_name = $customer->customer_name;
        $orderer->orderer_phone = $customer->customer_phone;
        $orderer->orderer_email = $customer->customer_email;
        $orderer->orderer_type_bed = $roomType->type_room_bed;
        $orderer->orderer_special_requirements = $order_require;
        $orderer->orderer_own_require = $require_text;
        $orderer->save();
        $orderer_id = $orderer->orderer_id;

        $payment = new Payment();
        $payment->payment_method = 4;
        $payment->payment_status = 0;
        $payment->save();

        $payment_id = $payment->payment_id;

        $order_detail = new OrderDetails();
        $order_detail->order_code = $order_code;
        $order_detail->hotel_id = $hotel->hotel_id;
        $order_detail->hotel_name = $hotel->hotel_name;
        $order_detail->room_id = $room->room_id;
        $order_detail->room_name = $room->room_name;
        $order_detail->type_room_id = $roomType->type_room_id;
        $order_detail->price_room = $price;
        $order_detail->hotel_fee = $service_price;
        $order_detail->save();

        $order = new Order();
        $order->start_day = $startDay;
        $order->end_day = $endDay;
        $order->orderer_id = $orderer_id;
        $order->payment_id = $payment_id;
        $order->order_status = 2;
        $order->order_code = $order_code;
        $order->coupon_name_code = $coupon_code;
        $order->coupon_sale_price = $coupon_price;
        $order->order_type = 0;
        $order->total_price = $price;
        $order->save();
        $order = Order::where("order_code", $order_code)->first();

        $this->email_order_to_customer($orderer_id, $order_detail, $order, $price);

        if($order != null){
            $data = $this->convertOrderToJson($order);
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công!',
                'data' => $data,
            ]);
        }else{
            return response()->json([
                'status_code' => 404,
                'message' => 'Không truy xuất được dữ liệu',
                'data' => null,
            ]) ;
        }
    }

    public function orderRestaurant(Request $request)
    {
        $date = $request->date;
        $menuList = $request->input('menuList');
        $restaurant_id = $request->restaurant_id;
        $quantityPerson = $request->person;
        $customer = $request->input('customer');
        $orderCode = $this->generateHotelCode();
        $orderDetails = [];
        $totalPrice = 0;
        foreach ($menuList as $item) {
            $menu = MenuRestaurant::query()
                ->where('menu_item_id',$item['menu_item_id'])
                ->select('menu_item_price')
                ->first();
            $detail = [
                'order_code' => $orderCode,
                'restaurant_id' => $restaurant_id,
                'restaurant_menu_id' => $item['menu_item_id'],
                'restaurant_menu_price' => $menu->menu_item_price * $item['quantity'],
                'restaurant_menu_quantity' => $item['quantity'],
            ];

            $totalPrice += $detail['restaurant_menu_price'];
            $orderDetails[] = $detail;
        }
        OrderDetailRestaurant::query()->insert($orderDetails);
        $payment = Payment::query()->create([
            'payment_method' => 4,
            'payment_status' => 0,
        ]);
        $orderer = Orderer::query()->create([
            'customer_id' => $customer['customer_id'],
            'orderer_name' => $customer['customer_name'],
            'orderer_phone' => $customer['customer_phone'],
            'orderer_email' => $customer['customer_email'],
            'orderer_type_bed' => $quantityPerson,
            'orderer_own_require' => $customer['customer_note'] ?? "Không có",
        ]);
        $order = Order::query()->create([
            'start_day' => $date,
            'orderer_id' => $orderer->orderer_id,
            'payment_id' => $payment->payment_id,
            'order_code' => $orderCode,
            'order_status' => 0,
            'order_type' => 1,
            'total_price' => $totalPrice,
            'restaurant_id' => $restaurant_id,
        ]);
        $data = array(
            'startDay' => $order->start_day,
            'ordererId' => $order->orderer_id,
            'restaurantId' => $order->restaurant_id,
            'orderCode' => $order->order_code,
            'paymentId' => $order->payment_id,
            'totalPrice' => $order->totalPrice,
            'orderStatus' => $order->order_status,
            'orderType' => $order->order_type
        );
        return response()->json([
            'status_code' => 200,
            'message' => 'Không truy xuất được dữ liệu',
            'data' => $data,
        ]);

    }


    public function generateHotelCode()
    {
        // Lấy thời gian hiện tại và định dạng
        $dateTime = Carbon::now()->format('YmdHis'); // Định dạng theo YYYYMMDDHHMMSS
        $hotelCode = 'MYHOTEL' . $dateTime;

        return $hotelCode;
    }

    public function email_order_to_customer($orderer_id, $order_details, $order, $price)
    {
            $data_orderer = Orderer::where("orderer_id", $orderer_id)->first();
            $customer_name = $data_orderer['orderer_name'];
            $customer_email = $data_orderer['orderer_email'];
            $customer_phone =  $data_orderer['orderer_phone'];


        $to_name = "MyHotel - Tìm Kiếm Khách Sạn Tại Khu Vực Đà Nẵng";
        $to_email = $customer_email;

        $data = array(
            "customer_name" => $customer_name,
            "customer_email" => $customer_email,
            "customer_phone" => $customer_phone,
            "order_details" => $order_details,
            "coupon_price_sale" => $order->coupon_sale_price,
            "total_payment" => $order->total_payment,
            "total_price" => $price
        );

        Mail::send('pages.mail', $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email)->subject("MyHotel - Yêu Cầu Đặt Phòng Của Bạn Đã Được Ghi Nhận Và Đang Chờ Xử Lý!"); //send this mail with subject
            $message->from($to_email, $to_name);
        });
    }

    public  function convertOrderRestaurant($rs){
        // foreach($result as $rs){
        $data_order = null;
        $orderer = Orderer::where("orderer_id", $rs->orderer_id)->first();
        $payment = Payment::where("payment_id", $rs->payment_id)->first();
        $order_detail = OrderDetailRestaurant::where("order_code", $rs->order_code)->get();
        $hotel = Hotel::where("hotel_id", $order_detail->hotel_id)->first();
        $room = Room::where("room_id", $order_detail->room_id)->first();
        $type_room = TypeRoom::where("type_room_id", $order_detail->type_room_id)->first();
        $gallery_room = GalleryRoom::where("room_id", $order_detail->room_id)->first();
        $data_order = array(
            "order_details_id" => $order_detail->order_details_id,
            "order_code" => $order_detail->order_code,
            "hotel_id" => $order_detail->hotel_id,
            "hotel_name" => $order_detail->hotel_name,
            "hotel" => $this->convertDataToJson($hotel),
            "room_id" => $order_detail->room_id,
            "room_name" => $order_detail->room_name,
            "room" => $room,
            "type_room_id" => $order_detail->type_room_id,
            "roomType" => $type_room,
            "price_room" => $order_detail->price_room,
            "hotel_fee" => $order_detail->hotel_fee,
            "room_image" => $gallery_room->gallery_room_image,
            "created_at" => $order_detail->created_at,
        );
        $data[] = array(
            "orderId" => $rs->order_id,
            "startDay" => $rs->start_day,
            "endDay" => $rs->end_day,
            "ordererId" => $rs->orderer_id,
            "paymentId" => $rs->payment_id,
            "payment" => $payment,
            "orderer" => $orderer,
            "orderDetail" => $data_order,
            "orderStatus" => $rs->order_status,
            "orderCode" => $rs->order_code,
            "couponNameCode" => $rs->coupon_name_code,
            "couponSalePrice" => $rs->coupon_sale_price,
            "createdAt" => $rs->created_at,
        );
        return $data;
    }

    public  function convertOrderToJson($rs){
        // foreach($result as $rs){
            $data_order = null;
            $orderer = Orderer::where("orderer_id", $rs->orderer_id)->first();
            $payment = Payment::where("payment_id", $rs->payment_id)->first();
            $order_detail = OrderDetails::where("order_code", $rs->order_code)->first();
            $hotel = Hotel::where("hotel_id", $order_detail->hotel_id)->first();
            $room = Room::where("room_id", $order_detail->room_id)->first();
            $type_room = TypeRoom::where("type_room_id", $order_detail->type_room_id)->first();
            $gallery_room = GalleryRoom::where("room_id", $order_detail->room_id)->first();
            $data_order = array(
                "order_details_id" => $order_detail->order_details_id,
                "order_code" => $order_detail->order_code,
                "hotel_id" => $order_detail->hotel_id,
                "hotel_name" => $order_detail->hotel_name,
                "hotel" => $this->convertDataToJson($hotel),
                "room_id" => $order_detail->room_id,
                "room_name" => $order_detail->room_name,
                "room" => $room,
                "type_room_id" => $order_detail->type_room_id,
                "roomType" => $type_room,
                "price_room" => $order_detail->price_room,
                "hotel_fee" => $order_detail->hotel_fee,
                "room_image" => $gallery_room->gallery_room_image,
                "created_at" => $order_detail->created_at,
            );
            $data[] = array(
                "orderId" => $rs->order_id,
                "startDay" => $rs->start_day,
                "endDay" => $rs->end_day,
                "ordererId" => $rs->orderer_id,
                "paymentId" => $rs->payment_id,
                "payment" => $payment,
                "orderer" => $orderer,
                "orderDetail" => $data_order,
                "orderStatus" => $rs->order_status,
                "orderCode" => $rs->order_code,
                "couponNameCode" => $rs->coupon_name_code,
                "couponSalePrice" => $rs->coupon_sale_price,
                "createdAt" => $rs->created_at,
                "orderType" => 0
            );
        return $data;
    }



    public function convertDataToJson($dt){

            $evaluates = Evaluate::where("hotel_id", $dt->hotel_id)->get();
            $service = ServiceCharge::where("hotel_id", $dt->hotel_id)->first();


            $data = array(
                "hotel_id" => $dt->hotel_id,
                "hotel_name" => $dt->hotel_name,
                "hotel_rank" => $dt->hotel_rank,
                "hotel_type" => $dt->hotel_type,
                "brand_id" => $dt->brand_id,
                "evaluates" => $evaluates,
                "service_change" => $service,
                "brand" => $dt->brand,

                "area" => $dt->area,
                "hotel_placedetails" => $dt->hotel_placedetails,
                "hotel_linkplace" => $dt->hotel_linkplace,
                "hotel_jfameplace" => $dt->hotel_jfameplace,
                "hotel_image" => $dt->hotel_image,
                "hotel_desc" => $dt->hotel_desc,
                "hotel_tag_keyword" => $dt->hotel_tag_keyword,
                "hotel_view" => $dt->hotel_view,
                "hotel_status" => $dt->hotel_status,
                "created_at" => $dt->created_at,
                "updated_at" => $dt->updated_at,
            );
        return $data;
    }
}
