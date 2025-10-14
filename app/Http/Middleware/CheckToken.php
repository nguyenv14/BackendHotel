<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Resources\ApiResource; // Sử dụng ApiResource để trả về lỗi

class CheckToken
{
    public function handle($request, Closure $next)
    {
        try {
            // Kiểm tra và xác thực token
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Token đã hết hạn
            return ApiResource::error('Token đã hết hạn.', 401)->toResponse($request);
        } catch (TokenInvalidException $e) {
            // Token không hợp lệ
            return ApiResource::error('Token không hợp lệ.', 401)->toResponse($request);
        } catch (JWTException $e) {
            // Token không được cung cấp
            return ApiResource::error('Token không được cung cấp.', 401)->toResponse($request);
        }

        return $next($request);
    }
}