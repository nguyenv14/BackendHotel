<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Customers;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class ApiCustomerController extends Controller{
    public function logIn(Request $request){
        $result = Customers::where('customer_password', md5($request->customer_password))->Where('customer_email', $request->customer_email)->first();
        if($result){
            return response()->json([
                'status_code' => 200,
                'message' => 'Đăng nhập thành công!',
                'data' => $result,
            ]) ;
        }else{
            return response()->json([
                'status_code' => 404,
                'message' => 'Sai email đăng nhập hoặc mật khẩu!',
                'data' => null,
            ]);
        }
    }

    public function logInGG(Request $request) {
        $cus = Customers::where("customer_email", $request->customer_email)->where("customer_status", 1)->first();
        if ($cus) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Sai email đăng nhập hoặc mật khẩu!',
                'data' => null,
            ]);
        } else {
            $customer = Customers::where("customer_email", $request->customer_email)->where("customer_status", 0)->first();
            if($customer){
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Đăng nhập thành công!',
                    'data' => $customer,
                ]);
            }else{
                $customer = new Customers();
                $customer->customer_email = $request->customer_email;
                $customer->customer_name = $request->customer_name;
                $customer->customer_status = 0;
                $customer->save();
                $customer = Customers::where("customer_email", $request->customer_email)->where("customer_status", 0)->first();
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Đăng nhập thành công!',
                    'data' => $customer,
                ]);
            }
        }
    }
    public function createCustomer(Request $request){
        $result = Customers::Where('customer_email', $request->customer_email)->first();
        if($result){
            return ApiResponse::error('Email đã tồn tại!', 400);
        }else{
            $customer = new Customers();
            $customer->customer_name = $request->customer_name;
            $customer->customer_email = $request->customer_email;
            $customer->customer_phone = $request->customer_phone;
            $customer->customer_password = md5($request->customer_password);
            $customer->customer_status = 1;
            $customer->save();
            return ApiResponse::success($customer, 'Thành công!');
        }
    }

    public function updateCustomer(Request $request){
        $customer = Customers::where("customer_id", $request->customer_id)->first();
        $customer->customer_name = $request->customer_name;
        $customer->customer_email = $request->customer_email;
        $customer->customer_phone = $request->customer_phone;
        $customer->save();
        $customer = Customers::where("customer_id", $request->customer_id)->first();
        if($customer){
            return response()->json([
                'status_code' => 200,
                'message' => 'Đăng nhập thành công!',
                'data' => $customer,
            ]) ;
        }else{
            return response()->json([
                'status_code' => 404,
                'message' => 'Sai email đăng nhập hoặc mật khẩu!',
                'data' => null,
            ]) ;
        }
    }
}

