<?php
namespace App\Http\Controllers;

use App\Services\Api\BannerService;
use Illuminate\Http\Request;

class ApiBannerController extends Controller
{
    private BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function getBannerList(Request $request)
    {
        return $this->bannerService->getBannerList();
    }
}
