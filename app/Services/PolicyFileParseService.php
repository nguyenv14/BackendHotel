<?php

namespace App\Services;

use App\Services\Api\HotelService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PolicyFileParseService
{
    protected $hotelService;

    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    /**
     * Parse policy file by calling recommendation API
     *
     * @param string $fileUrl
     * @param string $fileName
     * @param string $filePath
     * @param int $companyId
     * @return array
     */
    public function parseFile(string $fileUrl, string $fileName, string $filePath, int $companyId): array
    {
        try {
            // Mark file as parsing
            $this->hotelService->markPolicyFileAsParsing($companyId, $filePath);
            
            // Get recommendation API URL from env
            $recommendationApiUrl = env('RECOMMENDATION_API_URL', 'http://localhost:5000');
            $parseUrl = $recommendationApiUrl . '/api/documents/parse-file';
            
            Log::info('Starting to parse policy file', [
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'company_id' => $companyId
            ]);
            
            // Call recommendation API to parse file
            $response = Http::timeout(300)->post($parseUrl, [
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'collection_name' => 'policy_documents',
                'chunk_size' => 1000,
                'chunk_overlap' => 200
            ]);
            
            if (!$response->successful()) {
                $errorMsg = 'Parse file thất bại';
                try {
                    $errorData = $response->json();
                    if (isset($errorData['message'])) {
                        $errorMsg = $errorData['message'];
                    }
                } catch (\Exception $e) {
                    $errorMsg = 'Lỗi khi gọi API recommendation: ' . $response->status();
                }
                
                Log::error('Parse policy file failed', [
                    'file_url' => $fileUrl,
                    'file_name' => $fileName,
                    'error' => $errorMsg,
                    'status' => $response->status()
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMsg
                ];
            }
            
            $result = $response->json();
            
            if ($result['success'] ?? false) {
                // Mark file as parsed in database
                $this->hotelService->markPolicyFileAsParsed($companyId, $filePath);
                
                Log::info('Parse policy file successful', [
                    'file_url' => $fileUrl,
                    'file_name' => $fileName,
                    'company_id' => $companyId
                ]);
                
                return [
                    'success' => true,
                    'message' => $result['message'] ?? 'Parse file thành công',
                    'data' => $result['data'] ?? []
                ];
            } else {
                $errorMsg = $result['message'] ?? 'Parse file thất bại';
                
                Log::error('Parse policy file failed', [
                    'file_url' => $fileUrl,
                    'file_name' => $fileName,
                    'error' => $errorMsg
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMsg
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Parse policy file error', [
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Lỗi parse file: ' . $e->getMessage()
            ];
        }
    }
}

