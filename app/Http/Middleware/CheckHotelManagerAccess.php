<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckHotelManagerAccess
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
        // Kiểm tra user đã login chưa
        if (! Auth::check()) {
            return redirect('/admin/auth/login');
        }

        $user = Auth::user();

        // Nếu user có hotel_id (hotel manager)
        if ($user->hotel_id) {
            // Chỉ cho phép truy cập các route hotel manager
            $allowedRoutes = [
                'admin/hotel/manager',
                'admin/hotel/manager/edit-hotel',
                'admin/hotel/manager/update-hotel',
            ];

            $currentPath = $request->path();

            // Kiểm tra xem route hiện tại có được phép không
            $isAllowed = false;
            foreach ($allowedRoutes as $route) {
                if (str_starts_with($currentPath, $route)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (! $isAllowed) {
                // Redirect về trang quản lý hotel của họ
                return redirect('/admin/hotel/manager?hotel_id=' . $user->hotel_id);
            }
        }

        return $next($request);
    }
}
