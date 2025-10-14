<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Redirect;

class AdminAccess
{
    /* Trong Middleware Không Cho Phép Sử Model */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user()->hasRoles('admin')){
            return $next($request);
        }
        $this->message("warning","Chỉ Có Quản Trị Viên Mới Được Truy Cập Vào Đường Dẫn Này!");
        return redirect()->back();
    }

    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        Session::put('message', $message);
    }
}
