<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Evaluate;
use App\Models\Order;
use App\Models\OrderDetailRestaurant;
use App\Models\Orderer;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\ServiceCharge;
use Illuminate\Http\Request;

class ApiOrderRestaurantController extends Controller
{
    public function getOrderListByCustomerId(Request $request){
        $orderer = Orderer::where('customer_id', $request->customer_id)->get('orderer_id')->toArray();
        $list_id_orderer = array();
        foreach ($orderer as $key => $v_orderer) {
            $list_id_orderer[$key] = $v_orderer['orderer_id'];
        }
        if($request->order_status == 0){
            $orders = Order::query()
                ->wherein('orderer_id', $list_id_orderer)
                ->where('order_type', 1)
                ->where("order_status", 0)
                ->orderby('order_id', 'DESC')
                ->get();
        }else if($request->order_status == 1){
            $orders = Order::query()
                ->wherein('orderer_id', $list_id_orderer)
                ->where('order_type', 1)
                ->whereIn("order_status", [1, 2])
                ->orderby('order_id', 'DESC')
                ->get();
        }else{
            $orders = Order::query()
                ->whereIn('orderer_id', $list_id_orderer)
                ->where('order_type', 1)
                ->whereIn("order_status", [-1, -2])
                ->orderby('order_id', 'DESC')
                ->get();
        }
        if($orders->count() > 0){
            $data = $this->convertOrderToJson($orders);
            return response()->json([
                'status_code' => 200,
                'message' => 'Thanh Cong!',
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

    public function cancelOrderByCustomer(Request $request){
        $order = Order::query()->where("order_id", $request->order_id)->first();

        $order->order_status = -2;
        $order->save();

        $orderer = Orderer::query()->where('customer_id', $request->customer_id)->get('orderer_id')->toArray();
        $list_id_orderer = array();
        foreach ($orderer as $key => $v_orderer) {
            $list_id_orderer[$key] = $v_orderer['orderer_id'];
        }
        $orders = Order::query()
            ->whereIn('orderer_id', $list_id_orderer)
            ->where('order_type', 1)
            ->where("order_status", 0)
            ->orderby('order_id', 'DESC')
            ->get();

        if($orders->count() > 0){
            $data = $this->convertOrderToJson($orders);
            return response()->json([
                'status_code' => 200,
                'message' => 'Thanh Cong!',
                'data' => $data,
            ]);
        }else{
            return response()->json([
                'status_code' => 404,
                'message' => 'Không truy xuất được dữ liệu',
                'data' => "Không có đơn hàng nào cả!",
            ]) ;
        }
    }

    public function evaluateCustomer(Request $request){
        $customer = Customers::where("customer_id", $request->customer_id)->first();

        $evaluate = new Evaluate();
        $evaluate->customer_id = $customer->customer_id;
        $evaluate->customer_name = $customer->customer_name;
        $evaluate->hotel_id = $request->hotel_id;
        $evaluate->room_id = $request->room_id;
        $evaluate->type_room_id = $request->type_room_id;
        $evaluate->evaluate_title = $request->evaluate_content;
        $evaluate->evaluate_content = $request->evaluate_content;
        $evaluate->evaluate_loaction_point = $request->evaluate_loaction_point;
        $evaluate->evaluate_service_point = $request->evaluate_service_point;
        $evaluate->evaluate_price_point = $request->evaluate_price_point;
        $evaluate->evaluate_sanitary_point = $request->evaluate_sanitary_point;
        $evaluate->evaluate_convenient_point = $request->evaluate_convenient_point;
        $evaluate->save();

        $order = Order::where("order_id", $request->order_id)->first();
        $order->order_status = 2;
        $order->save();

        $orderer = Orderer::where('customer_id', $request->customer_id)->get('orderer_id')->toArray();
        $list_id_orderer = array();
        foreach ($orderer as $key => $v_orderer) {
            $list_id_orderer[$key] = $v_orderer['orderer_id'];
        }
        $orders = Order::wherein('orderer_id', $list_id_orderer)->whereIn("order_status", [1, 2])->orderby('order_id', 'DESC')->get();
        if($orders->count() > 0){
            $data = $this->convertOrderToJson($orders);
            return response()->json([
                'status_code' => 200,
                'message' => 'Thanh Cong!',
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

    public  function convertOrderToJson($result){
        foreach($result as $rs){
            $data_order = null;
            $orderer = Orderer::where("orderer_id", $rs->orderer_id)->first();
            $payment = Payment::where("payment_id", $rs->payment_id)->first();
            $orderDetails = OrderDetailRestaurant::query()->where("order_code", $rs->order_code)->get();
            foreach ($orderDetails as $orderDetail) {
                $data_order[] = array(
                    "order_details_id" => $orderDetail->order_details_id,
                    "restaurant_menu_name" => $orderDetail->menu->menu_item_name,
                    "restaurant_menu_price" => $orderDetail->restaurant_menu_price,
                    "restaurant_menu_quantity" => $orderDetail->restaurant_menu_quantity,
                );
            }
            $restaurant = Restaurant::query()->where('restaurant_id', $rs->restaurant_id)->first();
            $data[] = array(
                "orderId" => $rs->order_id,
                "startDay" => $rs->start_day,
                "ordererId" => $rs->orderer_id,
                "paymentId" => $rs->payment_id,
                "payment" => $payment,
                "orderer" => $orderer,
                "order_type" => 0,
                "total_price" => $rs->total_price,
                "restaurant_name" => $restaurant->restaurant_name,
                "restaurant_placedetails" => $restaurant->restaurant_placedetails,
                "restaurant_image" => $restaurant->restaurant_image,
                "area_name" => $restaurant->area->area_name,
                "orderStatus" => $rs->order_status,
                "orderCode" => $rs->order_code,
                "couponNameCode" => $rs->coupon_name_code,
                "couponSalePrice" => $rs->coupon_sale_price,
                "createdAt" => $rs->created_at,
                "orderDetailRestaurant" => $data_order,
            );
        }
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
