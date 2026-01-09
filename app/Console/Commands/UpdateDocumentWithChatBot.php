<?php
namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateDocumentWithChatBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:update-with-chatbot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và tự động thực hiện index các document với ChatBot';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $data = [
            'collection_name' => 'hotel_rag',
            'timestamp'       => $now->timestamp,
        ];
        $url = 'http://localhost:5000/api/indexing/rag/hotels';
        try {
            $response = Http::timeout(300)->post($url, $data);
            \Log::info('Index Response', [
                'url'        => $url,
                'status'     => $response->status(),
                'successful' => $response->successful(),
            ]);
            if (! $response->successful()) {
                $errorBody = $response->body();
                $errorData = null;
                try {
                    $errorData = $response->json();
                } catch (\Exception $e) {
                    $errorBody = $response->body();
                }

                \Log::error('Update document failed', [
                    'status'     => $response->status(),
                    'error_body' => $errorBody,
                    'error_data' => $errorData,
                ]);
                return Command::FAILURE;
            }
            return Command::SUCCESS;
        } catch (\Exception $e) {
            \Log::error('Update document exception', [
                'message' => $e->getMessage(),
            ]);
            return Command::FAILURE;
        }
    }
}
