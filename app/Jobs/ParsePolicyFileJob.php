<?php

namespace App\Jobs;

use App\Services\PolicyFileParseService;
use App\Services\Api\HotelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParsePolicyFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param string $fileUrl
     * @param string $fileName
     * @param string $filePath
     * @param int $companyId
     */
    public function __construct(
        public string $fileUrl,
        public string $fileName,
        public string $filePath,
        public int $companyId
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @param PolicyFileParseService $parseService
     * @return void
     */
    public function handle(PolicyFileParseService $parseService)
    {
        Log::info('ParsePolicyFileJob started', [
            'file_url' => $this->fileUrl,
            'file_name' => $this->fileName,
            'file_path' => $this->filePath,
            'company_id' => $this->companyId
        ]);

        $result = $parseService->parseFile(
            $this->fileUrl,
            $this->fileName,
            $this->filePath,
            $this->companyId
        );

        if (!$result['success']) {
            Log::error('ParsePolicyFileJob failed', [
                'file_url' => $this->fileUrl,
                'file_name' => $this->fileName,
                'error' => $result['message'] ?? 'Unknown error'
            ]);
            
            // Throw exception to mark job as failed
            throw new \Exception($result['message'] ?? 'Parse file thất bại');
        }

        Log::info('ParsePolicyFileJob completed successfully', [
            'file_url' => $this->fileUrl,
            'file_name' => $this->fileName,
            'company_id' => $this->companyId
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('ParsePolicyFileJob permanently failed', [
            'file_url' => $this->fileUrl,
            'file_name' => $this->fileName,
            'file_path' => $this->filePath,
            'company_id' => $this->companyId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Clear parsing status so user can retry
        try {
            $hotelService = app(HotelService::class);
            $hotelService->clearPolicyFileParsingStatus($this->companyId, $this->filePath);
        } catch (\Exception $e) {
            Log::error('Error clearing parsing status after job failure', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
