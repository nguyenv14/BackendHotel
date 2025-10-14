<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Customers;
use Illuminate\Http\Request;

class ApiCustomerController extends Controller{
    public function logIn(Request $request){
        $result = Customers::where('customer_password', md5($request->customer_password))->Where('customer_email', $request->customer_email)->first();
        if($result){
            // $data[] = array(
            //     "customer_id" => $result->customer_id,
            //     "customer_name" => $result->customer_name,
            //     "customer_email" => $result->customer_email,
            //     "customer_password" => $result->customer_password,2
            // );
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
            // $data[] = array(
            //     "customer_id" => $result->customer_id,
            //     "customer_name" => $result->customer_name,
            //     "customer_email" => $result->customer_email,
            //     "customer_password" => $result->customer_password,2
            // );
            return response()->json([
                'status_code' => 405,
                'message' => 'Email đã có người dùng!',
                'data' => null,
            ]) ;
        }else{
            $customer = new Customers();
            $customer->customer_name = $request->customer_name;
            $customer->customer_email = $request->customer_email;
            $customer->customer_phone = $request->customer_phone;
            $customer->customer_password = md5($request->customer_password);
            $customer->save();
            return response()->json([
                'status_code' => 200,
                'message' => 'Thành công',
                'data' => 1,
            ]) ;
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

