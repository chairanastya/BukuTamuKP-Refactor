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

    public $tries = 3;
    public $backoff = [10, 30, 60];

    protected $karyawanId;
    protected $tamu;
    protected $kunjungan;
    protected $token;

    public function __construct(int $karyawanId, Tamu $tamu, Kunjungan $kunjungan, string $token)
    {
        $this->karyawanId = $karyawanId;
        $this->tamu = $tamu;
        $this->kunjungan = $kunjungan;
        $this->token = $token;
    }

    public function handle(): void
    {
        try {
            // Re-fetch karyawan from database to ensure fresh model
            $karyawan = Karyawan::find($this->karyawanId);

            if (!$karyawan) {
                Log::error('Karyawan not found in database', [
                    'karyawan_id' => $this->karyawanId,
                    'job_id' => $this->job->getJobId(),
                ]);
                throw new \Exception("Karyawan with ID {$this->karyawanId} not found");
            }

            if (!$karyawan->email_karyawan) {
                Log::warning('Karyawan has no email, skipping confirmation email', [
                    'karyawan_id' => $karyawan->id_karyawan,
                    'job_id' => $this->job->getJobId(),
                ]);
                return;
            }

            Log::info('Sending confirmation email to karyawan', [
                'karyawan_id' => $karyawan->id_karyawan,
                'karyawan_email' => $karyawan->email_karyawan,
                'kunjungan_id' => $this->kunjungan->id_kunjungan,
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId(),
            ]);

            Mail::to($karyawan->email_karyawan)->send(
                new KunjunganConfirmation(
                    $karyawan,
                    $this->tamu,
                    $this->kunjungan,
                    $this->token
                )
            );

            Log::info('Confirmation email sent successfully', [
                'karyawan_email' => $karyawan->email_karyawan,
                'karyawan_name' => $karyawan->nama_karyawan,
                'job_id' => $this->job->getJobId(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email', [
                'karyawan_id' => $this->karyawanId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Confirmation email job failed after all retries', [
            'karyawan_id' => $this->karyawanId,
            'kunjungan_id' => $this->kunjungan->id_kunjungan,
            'error' => $exception->getMessage(),
        ]);
    }
}
