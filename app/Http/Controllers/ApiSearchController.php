<?php
namespace App\Http\Controllers;

use App\Services\Api\SearchService;
use Illuminate\Http\Request;

class ApiSearchController extends Controller
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request)
    {
        return $this->searchService->search(
            $request->searchText,
            $request->typeSearch
        );
    }

    public function filterSearch(Request $request)
    {
        return $this->searchService->filterSearch($request->all());
    }

    public function getRestaurantOrHotelFavourite(Request $request)
    {
        return $this->searchService->getFavourites(
            json_decode($request->favourites, true) ?? []
        );
    }

    public function handle_mastersearch(Request $request)
    {
        return $this->searchService->masterSearch($request->all());
    }
}
