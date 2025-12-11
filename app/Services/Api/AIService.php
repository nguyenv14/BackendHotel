<?php
namespace App\Services\Api;

use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use App\Models\Hotel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AIService
{
    private HotelService $hotelService;
    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }
    public function getHotelRecommendationPopular()
    {
        $baseUrl = env('AI_SERVICE_URL');
        $url     = $baseUrl . 'recommend/popular';
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $result   = $response->json();
                $data     = $result['data'];
                $hotelIds = collect($data['recommendations'])
                    ->pluck('hotel_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                if (empty($hotelIds)) {
                    return ApiResponse::success([], 'Không có khách sạn được đề xuất.');
                }
                $hotels = Hotel::query()
                    ->whereIn('hotel_id', $hotelIds)
                    ->with('area') // Giả sử model Hotel có relationship 'area'
                    ->get();
                $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
                $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
                return ApiResponse::success($this->hotelService->formatHotelsData($hotels, $coupons), 'Lấy danh sách khách sạn thành công');
            } else {
                return ApiResponse::error('Lấy danh sách khách sạn thất bại', 400);
            }
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    public function getHotelRecommendationForSimilar($hotel_id)
    {
        $baseUrl = env('AI_SERVICE_URL');
        $url     = $baseUrl . 'recommend/similar/' . $hotel_id;
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $result   = $response->json();
                $data     = $result['data'];
                $hotelIds = collect($data['recommendations'])
                    ->pluck('hotel_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                if (empty($hotelIds)) {
                    return ApiResponse::success([], 'Không có khách sạn được đề xuất.');
                }
                $hotels = Hotel::query()
                    ->whereIn('hotel_id', $hotelIds)
                    ->whereNotIn('hotel_id', [$hotel_id])
                    ->with('area') // Giả sử model Hotel có relationship 'area'
                    ->get();
                $TimeNow = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
                $coupons = Coupon::inRandomOrder()->where('coupon_end_date', '>=', $TimeNow)->where('coupon_start_date', '<=', $TimeNow)->where('coupon_qty_code', '>', 0)->get();
                return ApiResponse::success($this->hotelService->formatHotelsData($hotels, $coupons), 'Lấy danh sách khách sạn thành công');
            } else {
                return ApiResponse::error('Lấy danh sách khách sạn thất bại', 400);
            }
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }

    /**
     * Semantic search hotels by natural language query
     * 
     * @param string $query Natural language query
     * @param int $topK Number of results
     * @param array $filters Additional filters
     * @return array
     */
    public function semanticSearchHotels(string $query, int $topK = 10, array $filters = []): array
    {
        $baseUrl = env('RECOMMENDATION_API_URL', 'http://localhost:5000');
        $url = $baseUrl . '/api/hotels/semantic-search';
        
        try {
            $response = Http::timeout(10)->post($url, [
                'query' => $query,
                'top_k' => $topK,
                'filters' => $filters
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('Semantic search response', [
                    'query' => $query,
                    'success' => $data['success'] ?? false,
                    'results_count' => count($data['data']['results'] ?? [])
                ]);
                
                if ($data['success'] ?? false) {
                    // Map Python API results to Laravel format
                    $mappedResults = $this->mapSearchResults($data['data']['results'] ?? []);
                    
                    \Log::info('Mapped results', [
                        'count' => count($mappedResults)
                    ]);
                    
                    return $mappedResults;
                }
            }

            \Log::error('Semantic search failed', [
                'query' => $query,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            \Log::error('Semantic search exception', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Map Python API results to Laravel hotel format
     */
    private function mapSearchResults(array $results): array
    {
        $mapped = [];

        foreach ($results as $result) {
            $payload = $result['payload'] ?? [];
            
            $mapped[] = [
                'hotel_id' => $payload['hotel_id'] ?? null,
                'hotel_name' => $payload['hotel_name'] ?? '',
                'hotel_rank' => $payload['hotel_rank'] ?? null,
                'hotel_price_average' => $payload['hotel_price_average'] ?? null,
                'hotel_desc' => $payload['hotel_desc'] ?? '',
                'hotel_placedetails' => $payload['hotel_placedetails'] ?? '',
                'hotel_image' => $payload['hotel_image'] ?? '',
                'area_id' => $payload['area_id'] ?? null,
                'area_name' => $payload['area_name'] ?? '',
                'brand_id' => $payload['brand_id'] ?? null,
                'brand_name' => $payload['brand_name'] ?? '',
                'hotel_tag_keyword' => $payload['hotel_tag_keyword'] ?? '',
                'relevance_score' => $result['score'] ?? 0,
                'section_type' => $result['section_type'] ?? null, // From structured chunking
            ];
        }

        return $mapped;
    }
}
