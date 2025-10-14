<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Redirect;
class ProtectAuthLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){
            return $next($request);
        }else{
            $this->message("warning","Bạn Cần Đăng Nhập Trước Khi Truy Cập Đường Dẫn Này!");
            return redirect('admin/auth/login');
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
