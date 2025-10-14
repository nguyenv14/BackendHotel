<?php
namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\TypeRoom;
use App\Services\AuthService;
use Google\Service\HangoutsChat\Resource\Rooms;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
  private AuthService $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }
  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');
    return $this->authService->login($credentials);
  }

  public function logout()
  {
    return $this->authService->logout();
  }

  public function getProfile()
  {
    return $this->authService->getProfile();
  }
}