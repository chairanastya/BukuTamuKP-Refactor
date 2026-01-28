<?php

namespace App\Jobs;

use App\Models\Tamu;
use Cloudinary\Cloudinary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UploadPhotoToCloudinaryJob implements ShouldQueue
{
    /**
     * Create a new job instance.
     */
    use Queueable, InteractsWithQueue, SerializesModels;
    public $tries = 3;
    public $backoff = [10, 30, 60];
    protected $base64Image;
    protected $tamuId;
    public function __construct(string $base64Image, int $tamuId)
    {
    /**
     * Execute the job.
     */
        $this->base64Image = $base64Image;
        $this->tamuId = $tamuId;
    }
   
    public function handle(): void
    {
        try {
            $sizeInMB = (strlen($this->base64Image) * 0.75) / (1024 * 1024);
            
            Log::info('Starting Cloudinary upload', [
                'tamu_id' => $this->tamuId,
                'size_mb' => number_format($sizeInMB, 2),
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId(),
            ]);

            if ($sizeInMB > 5) {
                throw new \Exception('File size exceeds 5MB limit: ' . number_format($sizeInMB, 2) . ' MB');
            }

            $cloudinaryConfig = [
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ]
            ];

            if (config('app.env') === 'local') {
                putenv('CURLOPT_SSL_VERIFYPEER=0');
            }

            $cloudinary = new Cloudinary($cloudinaryConfig);

            $uploadResult = $cloudinary->uploadApi()->upload($this->base64Image, [
                'folder' => 'tamu-ktp',
                'resource_type' => 'image',
                'type' => 'upload',
                'transformation' => [
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
            ]);

            $tamu = Tamu::findOrFail($this->tamuId);
            $tamu->ktp_public_id = $uploadResult['public_id'];
            $tamu->save();

            Log::info('Cloudinary upload successful', [
                'tamu_id' => $this->tamuId,
                'public_id' => $uploadResult['public_id'],
                'job_id' => $this->job->getJobId(),
            ]);

        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'tamu_id' => $this->tamuId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Cloudinary upload job failed after all retries', [
            'tamu_id' => $this->tamuId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
