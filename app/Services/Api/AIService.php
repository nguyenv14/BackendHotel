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
        $url     = $baseUrl . '/api/hotels/semantic-search';

        try {
            $response = Http::timeout(10)->post($url, [
                'query'   => $query,
                'top_k'   => $topK,
                'filters' => $filters,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                \Log::info('Semantic search response', [
                    'query'         => $query,
                    'success'       => $data['success'] ?? false,
                    'results_count' => count($data['data']['results'] ?? [])
                ]);

                if ($data['success'] ?? false) {
                    // Map Python API results to Laravel format
                    $mappedResults = $this->mapSearchResults($data['data']['results'] ?? []);

                    \Log::info('Mapped results', [
                        'count' => count($mappedResults),
                    ]);

                    return $mappedResults;
                }
            }

            \Log::error('Semantic search failed', [
                'query'    => $query,
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            \Log::error('Semantic search exception', [
                'query' => $query,
                'error' => $e->getMessage(),
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
                'hotel_id'            => $payload['hotel_id'] ?? null,
                'hotel_name'          => $payload['hotel_name'] ?? '',
                'hotel_rank'          => $payload['hotel_rank'] ?? null,
                'hotel_price_average' => $payload['hotel_price_average'] ?? null,
                'hotel_desc'          => $payload['hotel_desc'] ?? '',
                'hotel_placedetails'  => $payload['hotel_placedetails'] ?? '',
                'hotel_image'         => $payload['hotel_image'] ?? '',
                'area_id'             => $payload['area_id'] ?? null,
                'area_name'           => $payload['area_name'] ?? '',
                'brand_id'            => $payload['brand_id'] ?? null,
                'brand_name'          => $payload['brand_name'] ?? '',
                'hotel_tag_keyword'   => $payload['hotel_tag_keyword'] ?? '',
                'relevance_score'     => $result['score'] ?? 0,
                'section_type'        => $result['section_type'] ?? null, // From structured chunking
            ];
        }

        return $mapped;
    }

    /**
     * Chatbot hotel endpoint - Gửi câu hỏi và nhận câu trả lời từ RAG system
     * 
     * Request format:
     * {
     *   "question": "Câu hỏi của người dùng" (required),
     *   "top_k": 5 (optional, mặc định: 5),
     *   "filters": {} (optional, ví dụ: {"area_id": 1, "max_price": 2000000})
     * }
     * 
     * @param array $data Request data với các tham số:
     *   - question (required): Câu hỏi của người dùng
     *   - top_k (optional): Số lượng documents để retrieve (mặc định: 5)
     *   - filters (optional): Mảng các filters (ví dụ: area_id, max_price, etc.)
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function chatbotHotel($data)
    {
        // Validate dữ liệu đầu vào
        if (!is_array($data)) {
            return ApiResponse::error('Dữ liệu không hợp lệ', 400);
        }
        
        // Kiểm tra question là bắt buộc
        $question = isset($data['question']) ? trim($data['question']) : '';
        if (empty($question)) {
            return ApiResponse::error('Câu hỏi (question) là bắt buộc', 400);
        }
        
        // Chuẩn hóa dữ liệu gửi đến Flask API
        $requestData = [
            'question' => $question,
        ];
        
        // Thêm top_k nếu có (optional)
        if (isset($data['top_k']) && is_numeric($data['top_k'])) {
            $requestData['top_k'] = (int) $data['top_k'];
        }
        
        // Thêm filters nếu có (optional)
        if (isset($data['filters']) && is_array($data['filters'])) {
            $requestData['filters'] = $data['filters'];
        }
        
        $baseUrl = env('AI_SERVICE_URL', 'http://localhost:5001');
        $url     = $baseUrl . 'chat';
        
        try {
            // Gọi Flask API và lấy kết quả đầy đủ
            $response = Http::timeout(300)->post($url, $requestData);
            
            // Log response để debug
            \Log::info('Chatbot API Response', [
                'url' => $url,
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);
            
            if (!$response->successful()) {
                // Lấy error message từ response nếu có
                $errorBody = $response->body();
                $errorData = null;
                try {
                    $errorData = $response->json();
                } catch (\Exception $e) {
                    // Nếu không parse được JSON, dùng body text
                    $errorBody = $response->body();
                }
                
                \Log::error('Chatbot API Error', [
                    'status' => $response->status(),
                    'error_body' => $errorBody,
                    'error_data' => $errorData,
                ]);
                
                $errorMessage = 'Chatbot hotel thất bại';
                if ($errorData && isset($errorData['message'])) {
                    $errorMessage = $errorData['message'];
                } elseif ($response->status() === 404) {
                    $errorMessage = 'Không tìm thấy API chatbot. Vui lòng kiểm tra cấu hình AI_SERVICE_URL.';
                } elseif ($response->status() === 500) {
                    $errorMessage = 'Lỗi server chatbot. Vui lòng thử lại sau.';
                }
                
                return ApiResponse::error($errorMessage, $response->status());
            }
            
            $jsonData = $response->json();
            
            if (!isset($jsonData['success']) || !$jsonData['success']) {
                \Log::warning('Chatbot API returned unsuccessful response', [
                    'response' => $jsonData,
                ]);
                
                return ApiResponse::error(
                    $jsonData['message'] ?? ($jsonData['error'] ?? 'Chatbot hotel thất bại'),
                    400
                );
            }
            
            // Sau khi có kết quả, stream về frontend
            return response()->stream(function () use ($jsonData) {
                // Gửi signal bắt đầu
                echo "data: " . json_encode(['type' => 'start', 'message' => 'Đang xử lý...']) . "\n\n";
                ob_flush();
                flush();
                
                // Stream answer về frontend theo từng chunk
                if (isset($jsonData['data']['answer'])) {
                    $answer = $jsonData['data']['answer'];
                    
                    // Chia answer thành các câu để stream
                    // Tách theo dấu câu (., !, ?) để stream từng câu một
                    $sentences = preg_split('/([.!?]+[\s]+)/', $answer, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $currentChunk = '';
                    
                    foreach ($sentences as $part) {
                        $currentChunk .= $part;
                        
                        // Gửi chunk khi gặp dấu câu hoặc đủ độ dài
                        if (preg_match('/[.!?]$/', trim($part)) || strlen($currentChunk) >= 50) {
                            if (!empty(trim($currentChunk))) {
                                echo "data: " . json_encode([
                                    'type' => 'chunk',
                                    'content' => $currentChunk,
                                ]) . "\n\n";
                                ob_flush();
                                flush();
                                
                                // Delay nhỏ để tạo hiệu ứng streaming
                                usleep(30000); // 30ms
                            }
                            $currentChunk = '';
                        }
                    }
                    
                    // Gửi phần còn lại
                    if (!empty(trim($currentChunk))) {
                        echo "data: " . json_encode([
                            'type' => 'chunk',
                            'content' => trim($currentChunk),
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                }
                
                // Gửi toàn bộ dữ liệu hoàn chỉnh (bao gồm sources, metadata)
                echo "data: " . json_encode([
                    'type' => 'complete',
                    'data' => $jsonData['data'],
                ]) . "\n\n";
                ob_flush();
                flush();
                
                // Gửi signal kết thúc
                echo "data: " . json_encode(['type' => 'end']) . "\n\n";
                ob_flush();
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Tắt buffering trong nginx
            ]);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Lỗi kết nối (Flask API không chạy hoặc không thể kết nối)
            \Log::error('Chatbot API Connection Error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return ApiResponse::error('Không thể kết nối đến chatbot service. Vui lòng kiểm tra AI_SERVICE_URL và đảm bảo Flask API đang chạy.', 503);
        } catch (\Exception $e) {
            // Fallback: trả về error không streaming nếu có lỗi
            \Log::error('Chatbot API Exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ApiResponse::error('Lỗi: ' . $e->getMessage(), 500);
        }
    }
}
