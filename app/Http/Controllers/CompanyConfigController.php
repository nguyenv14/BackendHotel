<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyConfigRepository\CompanyConfigRepositoryInterface;
use App\Services\Api\HotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\Models\ManipulationActivity;
use App\Models\CompanyConfig;
session_start();

class CompanyConfigController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $comRepo;
    protected $hotelService;
    
    public function __construct(CompanyConfigRepositoryInterface $comRepo, HotelService $hotelService)
    {
        $this->comRepo = $comRepo;
        $this->hotelService = $hotelService;
    }
    public function show_company_config(){
         $info = $this->comRepo->getCompany();
         
         // Lấy danh sách policy files nếu có company_id
         $policyFiles = [];
         if ($info && $info->company_id) {
             try {
                 // Tìm ConfigWeb với company_id tương ứng
                 $configWeb = \App\Models\ConfigWeb::where('company_id', $info->company_id)->first();
                 if ($configWeb) {
                     $policyFiles = $this->hotelService->getAllPolicyFiles($info->company_id);
                 }
             } catch (\Exception $e) {
                 // Nếu không tìm thấy, để mảng rỗng
                 $policyFiles = [];
             }
         }
         
        return view('admin.ConfigWeb.companyconfig')->with(compact('info', 'policyFiles'));
    }

    public function edit_content_footer(Request $request){
        $result = $this->comRepo->InsertorUpdate($request->all());        
    }

    public function upload_policy_file(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('file');
            
            // Lấy company_id từ CompanyConfig
            
            // Upload file lên MinIO với company_id
            $path = $this->hotelService->putFileMinio($file);
            
            return response()->json([
                'success' => true,
                'message' => 'Upload file thành công',
                'path' => $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get_policy_files(Request $request)
    {
        try {
            $info = $this->comRepo->getCompany();
            if (!$info || !$info->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin công ty',
                    'data' => []
                ], 400);
            }
            
            $files = $this->hotelService->getAllPolicyFiles($info->company_id);
            
            return response()->json([
                'success' => true,
                'data' => $files
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy danh sách file: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function parse_policy_file(Request $request)
    {
        $request->validate([
            'file_url' => 'required|url',
            'file_name' => 'required|string',
            'file_path' => 'required|string',
        ]);

        try {
            $fileUrl = $request->input('file_url');
            $fileName = $request->input('file_name');
            $filePath = $request->input('file_path');
            
            // Get company_id
            $info = $this->comRepo->getCompany();
            if (!$info || !$info->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin công ty'
                ], 400);
            }
            
            // Get recommendation API URL from env
            $recommendationApiUrl = env('RECOMMENDATION_API_URL', 'http://localhost:5000');
            $parseUrl = $recommendationApiUrl . '/api/documents/parse-file';
            
            // Call recommendation API to parse file
            $response = \Illuminate\Support\Facades\Http::timeout(300)->post($parseUrl, [
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
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 500);
            }
            
            $result = $response->json();
            
            if ($result['success'] ?? false) {
                // Mark file as parsed in database
                $this->hotelService->markPolicyFileAsParsed($info->company_id, $filePath);
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Parse file thành công',
                    'data' => $result['data'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Parse file thất bại'
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Parse policy file error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi parse file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }
}

