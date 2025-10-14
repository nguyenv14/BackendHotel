<?php
namespace App\Http\Controllers;

use App\Models\Social;
use App\Rules\Captcha;
use App\Models\Customers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mail;
use Socialite;
use App\Models\Activitylog;
use Location;

session_start();

class LoginAndRegister extends Controller
{
    /* Đăng Nhập */
    public function login_customer(Request $request)
    {
       
        $data = $request->validate([
            'customer_email' => 'required', /* Nghiên cứu thêm validate của lava có thể truyền vào |string|min5|max15 để very */
            'customer_password' => 'required',
            'g-recaptcha-response' => new Captcha(), //dòng kiểm tra Captcha
        ]);
        $Email = $data['customer_email'];
        $Password = $data['customer_password'];
        
        $result = Customers::where('customer_password', md5($Password))->where('customer_email', $Email)->where('customer_status', 1)->first();
        if ($result) {
            session()->put('customer_id',  $result->customer_id);
            session()->put('customer_name', $result->customer_name);
            Activitylog::noteCustomerLog($result->customer_id, $result->customer_name,"Đăng Nhập");
            $this->message("success","Đăng Nhập Thành Công!");
            return redirect()->back();

        } else {
            $this->message("error","Đăng Nhập Thất Bại!");
            return redirect()->back();
        }
    }

    /* Đăng Ký */ /* Bug Chưa Bắt Được Ngoại Lệ Captcha */
    public function create_customer(Request $request)
    {
        $result = Customers::where('customer_email',$request->customer_email)->first();
        if($result){
            echo "emailalreadyexists";
        }else{
            $currentUserInfo = Location::get(request()->ip());
            if($currentUserInfo != null){
                $customer_located = $currentUserInfo->cityName;
            }else {
                $customer_located = "Không Xác Định";
            }
           $this->validate($request,[
                'customer_name' => 'required',
                'customer_phone' => 'required',
                'customer_email' => 'required',
                'customer_password' => 'required',
                'g-recaptcha-response' => new Captcha(), //dòng kiểm tra Captcha
            ]);
            $data = array(
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_password' => md5($request->customer_password),
                'customer_status' => 1,
                'customer_ip' => request()->ip(),
                'customer_located' => $customer_located,
                'customer_device' =>request()->userAgent(),
            );
            session()->put('rg_customer_name', $data['customer_name']);
            session()->put('rg_customer_email', $data['customer_email']);
            session()->put('rg_customer_allinfo', $data);
            
            $request->session()->forget('rg_code');
            $this->RandomCode();
            return redirect('/user/MailToCustomer');
        }
    }

    public function verification_code_rg(Request $request)
    {
        $rg = session()->get('rg_customer_email');
        if (isset($rg)) {
            $coderg = session()->get('rg_code');
        }
        if ((isset($request->verycoderg) && $request->verycoderg == $coderg)) {
            echo "success_very_code";
            return redirect('/user/successful-create-account');
        } else {
            echo "error_very_code";
        }
    }

    public function successful_create_account()
    {
        $data = session()->get('rg_customer_allinfo');
        DB::table('tbl_customers')->insert($data);
        $customer_id = DB::getPdo('tbl_customers')->lastInsertId();
        Activitylog::noteCustomerLog($customer_id,$data['customer_name'],"Đăng Ký");
        session()->forget('rg_customer_name');
        session()->forget('rg_customer_email');
        session()->forget('rg_customer_allinfo');
        session()->forget('rg_code');

    }

    /* Khôi Phục Mật Khẩu */
    public function find_account_recovery_pw(Request $request)
    {
        $result = DB::table('tbl_customers')->select('customer_id', 'customer_name', 'customer_email')->where('customer_name', $request->customer_name_mail)->orWhere('customer_email', $request->customer_name_mail)->first();
        if ($result) {
            session()->put('rc_customer_id', $result->customer_id);
            session()->put('rc_customer_name', $result->customer_name);
            session()->put('rc_customer_email', $result->customer_email);
            $this->RandomCode();
            return redirect('user/MailToCustomer');
        } else {
            echo "account_not_found";
        }

    }

    public function verification_code_rc(Request $request)
    {
        $rc = session()->get('rc_customer_email');
        if (isset($rc)) {
            $coderc = session()->get('rc_code');
        }
        if (isset($request->verycoderc) && $request->verycoderc == $coderc) {
            echo "success_very_code";
        } else {
            echo "error_very_code";
        }
    }

    public function confirm_password(Request $request)
    {
        $rc_customer_id = session()->get('rc_customer_id');
        if (isset($request->password1) && isset($request->password2)) {
            if ($request->password1 == $request->password2) {
                $data = array();
                $data['customer_password'] = md5($request->password1);
                DB::table('tbl_customers')->where('customer_id', $rc_customer_id)->update($data);
                $customer = Customers::where('customer_id',$rc_customer_id)->first();
                Activitylog::noteCustomerLog($customer->customer_id, $customer->customer_name,"Khôi Phục Mật Khẩu");
                session()->forget('rc_customer_id');
                session()->forget('rc_customer_name');
                session()->forget('rc_customer_email');
                session()->forget('rc_code');
                echo 'success';
                return;
            } else {
                echo 'error';
                return;
            }
        }
        echo 'error';
        return;
    }

    /* Đăng Xuất */
    public function logout()
    {
        Activitylog::noteCustomerLog( session()->get('customer_id'), session()->get('customer_name'),"Đăng Xuất");
        session()->forget('customer_id');
        session()->forget('customer_name');
        $this->message("success","Đăng Xuất Thành Công!");
        return redirect()->back();
    }

    /* Các Hàm Dùng Chung Cho Đăng Ký , Khôi Phục */
    public function RandomCode()
    {
        $randomrg = rand(10000000, 99999999);
        $randomrc = rand(10000000, 99999999);
        $rg = session()->get('rg_customer_name');
        $rc = session()->get('rc_customer_name');

        if (isset($rg)) {
            session()->put('rg_code', $randomrg);
        };

        if (isset($rc)) {
            session()->put('rc_code', $randomrc);
        };
    }
    public function MailToCustomer()
    {
        $rc = session()->get('rc_customer_name');
        if (isset($rc)) {
            $name = session()->get('rc_customer_name');
            $mail_customer = session()->get('rc_customer_email');
            $code = session()->get('rc_code');
            $type = "Bạn đã yêu cầu khôi phục tài khoản của mình!";
            $to_name = "Lê Khả Nhân - Mail Laravel";
            $to_email = "$mail_customer";
        };
        $rg = session()->get('rg_customer_name');
        if (isset($rg)) {
            $name = session()->get('rg_customer_name');
            $mail_customer = session()->get('rg_customer_email');
            $code = session()->get('rg_code');
            $type = "Bạn đã yêu cầu đăng ký tài khoản!";
            $to_name = "Lê Khả Nhân - Mail Laravel";
            $to_email = "$mail_customer";
        };
        $data = array(
            "name" => "$name",
            "code" => "$code",
            "type" => "$type",
        );

        Mail::send('pages.Login_Register.mailtocustomer', $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email)->subject("Xin Chào ! Lê Khả Nhân Đang Test Mail Chút Hihi"); //send this mail with subject
            $message->from($to_email, $to_name); //send from this mail
        });

    }

    /* Login Facebook - Chưa Hiểu Hàm Này ! */
    public function login_facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function login_facebook_callback()
    {
        $provider = Socialite::driver('facebook')->user();
        $account = Social::where('provider', 'facebook')->where('provider_user_id', $provider->getId())->first();
        if ($account) {
            $account_name = Customers::where('customer_id', $account->user)->first();
            session()->put('customer_id',  $account_name->customer_id);
            session()->put('customer_name', $account_name->customer_name);
            Activitylog::noteCustomerLog($account_name->customer_id,$account_name->customer_name,"Đăng Nhập Bằng FB");
           
        } else {
            $Social = new Social([
                'provider_user_id' => $provider->getId(),
                'provider' => 'facebook',
            ]);
            $orang = Customers::where('customer_email', $provider->getEmail())->first();
            if (!$orang) {

                $currentUserInfo = Location::get(request()->ip());
                if($currentUserInfo != null){
                    $customer_located = $currentUserInfo->cityName;
                }else {
                    $customer_located = "Không Xác Định";
                }

                $orang = Customers::create([
                    'customer_name' => $provider->getName(),
                    'customer_email' => $provider->getEmail(),
                    'customer_status' => 1,
                    'customer_ip' => request()->ip(),
                    'customer_located' => $customer_located,
                    'customer_device' =>request()->userAgent(),
                ]);
            }
            $Social->login()->associate($orang);
            $Social->save();
            $account_name = Customers::where('customer_id', $Social->user)->first();
            // $account_name = Customers::where('customer_id',$orang->customer_id)->first();
            session()->put('customer_id',  $account_name->customer_id);
            session()->put('customer_name', $account_name->customer_name);
            Activitylog::noteCustomerLog($account_name->customer_id,$account_name->customer_name,"Đăng Nhập Bằng FB");
        }
        $this->message("success","Đăng Nhập Facebook Thành Công!");
        return redirect('/');
    }

    public function login_google()
    {
        return Socialite::driver('google')->redirect();
    }

    public function login_google_callback()
    {
        $users = Socialite::driver('google')->stateless()->user();
        // return $users->id;
        $authUser = $this->findOrCreateUser($users, 'google');
        if( $authUser->customer_id != null){
            $cus_id = $authUser->customer_id ;
        }else{
            $cus_id = $authUser->user;
        }
        $account_name = Customers::where('customer_id', $cus_id )->first();
        session()->put('customer_id',  $account_name->customer_id);
        session()->put('customer_name', $account_name->customer_name);
        Activitylog::noteCustomerLog($account_name->customer_id,$account_name->customer_name,"Đăng Nhập Bằng GG");
        $this->message("success","Đăng Nhập Google Thành Công!");
        return redirect('/');
    }
    public function findOrCreateUser($users, $provider)
    {
        $authUser = Social::where('provider_user_id', $users->id)->first();
        if ($authUser) {
            return $authUser;
        }else{ 
            $social = new Social([
                'provider_user_id' => $users->id,
                'provider' => $provider,
            ]);
            $orang = Customers::where('customer_email', $users->email)->first();
            if (!$orang) {
                $currentUserInfo = Location::get(request()->ip());
                if($currentUserInfo != null){
                    $customer_located = $currentUserInfo->cityName;
                }else {
                    $customer_located = "Không Xác Định";
                }
                $orang = Customers::create([
                    'customer_name' => $users->name,
                    'customer_email' => $users->email,
                    'customer_status' => 1,
                    'customer_ip' => request()->ip(),
                    'customer_located' => $customer_located,
                    'customer_device' =>request()->userAgent(),
                ]);
            }
            $social->login()->associate($orang);
            $social->save();
            $account_name = Customers::where('customer_id', $social->user)->first();    
            return $account_name;
        }

    }
    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }
}
