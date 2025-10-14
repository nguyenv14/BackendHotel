<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\Models\ManipulationActivity;
use Auth;
session_start();

class CouponController extends Controller
{
    public function add_coupon(){
        return view('admin.Coupon.add_coupon');
    }
    public function save_coupon(Request $request){
       $data = $request->all();
       $coupon = new Coupon; /* Khởi Tạo Đối Tượng Từ Model Coupon */
       $coupon->coupon_name = $data['coupon_name'];
       $coupon->coupon_name_code = $data['coupon_name_code'];
       $coupon->coupon_desc = $data['coupon_desc'];
       $coupon->coupon_qty_code = $data['coupon_qty_code'];
       $coupon->coupon_condition = $data['coupon_condition'];
       $coupon->coupon_price_sale = $data['coupon_price_sale'];
       $coupon->coupon_start_date = $data['coupon_start_date'];
       $coupon->coupon_end_date = $data['coupon_end_date'];
       /* Model Nhận Toàn  Bộ Dữ Liệu Từ Request */
       $coupon->save(); /* Gọi phương thức save() của model để lưu vào database */
       ManipulationActivity::noteManipulationAdmin( "Thêm Mã Giảm Giá ( Tên : ". $data['coupon_name'].")");
       $this->message("success","Thêm Mã Giảm Giá Thành Công!");
       return redirect('admin/coupon/list-coupon');
    }
    public function list_coupon(){
        $coupon = Coupon::orderby('coupon_id','DESC')->get();
        ManipulationActivity::noteManipulationAdmin( "Xenh Danh Sách Mã Giảm Giá ");
        return view('admin.Coupon.list_coupon')->with(compact('coupon'));
    }
    public function edit_coupon(Request $request){
        $coupon_old = Coupon::where('coupon_id',$request->coupon_id)->first();
        return view('admin.Coupon.edit_coupon')->with(compact('coupon_old'));
    }

    public function update_coupon(Request $request){
        $datanew = $request->all();
        $coupon = Coupon::find($datanew['coupon_id']);
        $coupon->coupon_name =  $datanew['coupon_name'];
        $coupon->coupon_name_code = $datanew['coupon_name_code'];
        $coupon->coupon_desc = $datanew['coupon_desc'];
        $coupon->coupon_qty_code = $datanew['coupon_qty_code'];
        $coupon->coupon_condition = $datanew['coupon_condition'];
        $coupon->coupon_price_sale = $datanew['coupon_price_sale'];
        $coupon->coupon_start_date = $datanew['coupon_start_date'];
        $coupon->coupon_end_date = $datanew['coupon_end_date'];
        $coupon->save();
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Mã Giảm Giá ( ID : ". $datanew['coupon_id'].")");
        $this->message("success","Cập Nhật Giảm Giá Thành Công!");
        return redirect('admin/coupon/list-coupon');
     }

     public function delete_coupon(Request $request){
        $coupon_id = $request->coupon_id;
        $coupon = Coupon::find($coupon_id);
        $coupon->delete();
        $this->message("success","Xóa Giảm Giá Thành Công!");
        ManipulationActivity::noteManipulationAdmin("Xóa Mã Giảm Giá ( ID : ".$request->coupon_id.")");
        return redirect('admin/coupon/list-coupon');
     }

     public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }
}

