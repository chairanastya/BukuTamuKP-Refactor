<?php

namespace App\Jobs;

use App\Mail\KunjunganConfirmation;
use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\Tamu;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendConfirmationEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [10, 30, 60]; 

    /**
     * The karyawan instance.
     *
     * @var \App\Models\Karyawan
     */
    protected $karyawan;

    /**
     * The tamu instance.
     *
     * @var \App\Models\Tamu
     */
    protected $tamu;

    /**
     * The kunjungan instance.
     *
     * @var \App\Models\Kunjungan
     */
    protected $kunjungan;

    /**
     * The approval token.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new job instance.
     */
    public function __construct(Karyawan $karyawan, Tamu $tamu, Kunjungan $kunjungan, string $token)
    {
        $this->karyawan = $karyawan;
        $this->tamu = $tamu;
        $this->kunjungan = $kunjungan;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!$this->karyawan->email_karyawan) {
                Log::warning('Karyawan has no email, skipping confirmation email', [
                    'karyawan_id' => $this->karyawan->id_karyawan,
                    'job_id' => $this->job->getJobId(),
                ]);
                return;
            }

            Log::info('Sending confirmation email to karyawan', [
                'karyawan_id' => $this->karyawan->id_karyawan,
                'karyawan_email' => $this->karyawan->email_karyawan,
                'kunjungan_id' => $this->kunjungan->id_kunjungan,
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId(),
            ]);

            Mail::to($this->karyawan->email_karyawan)->send(
                new KunjunganConfirmation(
                    $this->karyawan,
                    $this->tamu,
                    $this->kunjungan,
                    $this->token
                )
            );

            Log::info('Confirmation email sent successfully', [
                'karyawan_email' => $this->karyawan->email_karyawan,
                'karyawan_name' => $this->karyawan->nama_karyawan,
                'job_id' => $this->job->getJobId(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email', [
                'karyawan_id' => $this->karyawan->id_karyawan,
                'karyawan_email' => $this->karyawan->email_karyawan,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Confirmation email job failed after all retries', [
            'karyawan_id' => $this->karyawan->id_karyawan,
            'karyawan_email' => $this->karyawan->email_karyawan,
            'kunjungan_id' => $this->kunjungan->id_kunjungan,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
