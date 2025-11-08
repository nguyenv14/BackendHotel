<?php
namespace App\Http\Controllers;

use App\Services\Api\AreaService;
use Illuminate\Http\Request;

class ApiAreaController extends Controller
{
    private AreaService $areaService;

    public function __construct(AreaService $areaService)
    {
        $this->areaService = $areaService;
    }

    public function getAreas(Request $request)
    {
        return $this->areaService->getAreas();
    }

    public function getAreaListHaveHotel(Request $request)
    {
        return $this->areaService->getAreaListHaveHotel();
    }
}
