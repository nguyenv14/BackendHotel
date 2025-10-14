<?php

namespace App\Services;

use App\Http\Responses\ApiResponse;
use App\Models\Customers;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
  public function login($credentials)
  {
    $user = Customers::query()->where('customer_email', $credentials['email'])->first();

    if (!$user) {
      return ApiResponse::error('Email không tồn tại', Response::HTTP_UNAUTHORIZED);
    }

    if (!(md5($credentials['password']) == $user->customer_password)) {
      return ApiResponse::error('Mật khẩu không đúng', Response::HTTP_UNAUTHORIZED);
    }
    $token = auth('api')->login($user);

    return ApiResponse::success([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'user' => auth('api')->user(),
    ], 'Đăng nhập thành công');
  }

  public function getProfile()
  {
    $user = auth('api')->user();
    if (!$user) {
      return ApiResponse::error('Người dùng không tồn tại', Response::HTTP_UNAUTHORIZED);
    }
    return ApiResponse::success($user, 'Lấy thông tin người dùng thành công');
  }

  public function logout()
    {
        try {
            auth('api')->logout();
            return ApiResponse::success(null, 'Đăng xuất thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Không thể đăng xuất', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
