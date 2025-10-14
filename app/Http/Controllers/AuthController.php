<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\Roles;
use App\Rules\Captcha;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Redirect;
use Session;
use Illuminate\Support\Facades\Auth;

session_start();
class AuthController extends Controller
{
    public function show_register(){
        return view('admin.AuthLoginAndRegister.auth_register');
    }
    public function registration_processing(Request $request){
        $rules = [
            'admin_name' => 'required|string|min:3|max:256|',
            'admin_phone' => 'required|min:3|max:256|',
            'admin_email' => 'required|email|min:3|max:256|',
            'admin_password' => 'required|string|min:3|max:256|',
            'admin_password_retype' => 'required|string|min:3|max:256|',
            // 'g-recaptcha-response' => new Captcha(), //dòng kiểm tra Captcha
        ];
        $customMessages = [
            'required' => 'Trường :attribute Này Không Được Trống!.'
        ];
        $this->validate($request, $rules, $customMessages);
        if($request->admin_password !=  $request->admin_password_retype){
            $this->message('warning','Vui lòng nhập mật khẩu trùng nhau!');
            return redirect()->back();
        }
        $data = $request->all();
        $admin = new Admin();
        $admin->admin_name =  $data['admin_name'];
        $admin->admin_phone =  $data['admin_phone'];
        $admin->admin_email =  $data['admin_email'];
        $admin->admin_password =  md5($data['admin_password']);
        $admin->save();

        $this->message('success','Đăng Ký Thành Công! - Hãy Cấp Quyền Cho Tài Khoản Vừa Đăng Ký');
        return redirect('admin/auth/all-admin');
    }
    public function show_login(){
     
        if(isset($_COOKIE['Admin_Email']) && isset($_COOKIE['Admin_Password'])){
            if(Auth::attempt(['admin_email' =>$_COOKIE['Admin_Email'], 'admin_password' => $_COOKIE['Admin_Password']])){
                ActivityLog::noteAdminLog("Đăng Nhập Tự Động");
                
                // Kiểm tra nếu user có hotel_id (hotel manager)
                $user = Auth::user();
                if($user->hotel_id) {
                    $this->message('success','Đăng Nhập Tự Động Thành Công! Chào mừng Hotel Manager!');
                    return redirect('/admin/hotel/manager?hotel_id=' . $user->hotel_id);
                } else {
                    $this->message('success','Đăng Nhập Tự Động Bằng Cookie Thành Công!');
                    return redirect('/admin/dashboard');
                }
            }else{
                return view('admin.AuthLoginAndRegister.auth_login');
            }
        }
       else{
        return view('admin.AuthLoginAndRegister.auth_login');
        }
    }
    
    public function login_processing(Request $request){
        $rules = [
            'admin_email' => 'required|email|min:3|max:256|',
            'admin_password' => 'required|string|min:3|max:256|',
        ];
        $customMessages = [
            'required' => 'Trường :attribute Này Không Được Trống!.'
        ];
        $this->validate($request, $rules, $customMessages);
        if(Auth::attempt(['admin_email' =>$request->admin_email, 'admin_password' => $request->admin_password])){
            if($request->SaveLoginCooke == "ON"){
                setcookie("Admin_Email", $request->admin_email, time() + 999999);
                setcookie("Admin_Password", $request->admin_password, time() + 999999);
            }
            ActivityLog::noteAdminLog("Đăng Nhập");
            
            // Kiểm tra nếu user có hotel_id (hotel manager)
            $user = Auth::user();
            if($user->hotel_id) {
                $this->message('success','Đăng Nhập Thành Công! Chào mừng Hotel Manager!');
                return redirect('/admin/hotel/manager?hotel_id=' . $user->hotel_id);
            } else {
                $this->message('success','Đăng Nhập Bằng Authen Thành Công!');
                return redirect('/admin/dashboard');
            }
        }else{
            $this->message('warning','Tài Khoản Hoặc Mật Khẩu Không Chính Xác!');
            return redirect()->back();
        }
    }

    public function logout(){
        ActivityLog::noteAdminLog("Đăng Xuất");
        Auth::logout();
        setcookie("Admin_Email", "", time() - 60);
        setcookie("Admin_Password", "", time() - 60);
        $this->message('success','Đăng Xuất Thành Công!');
        return redirect('admin/auth/login');
    }

    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }
}
