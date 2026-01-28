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

    protected $karyawanIds;
    protected $kunjunganId;
    protected $tamuId;
    protected $token;

    public function __construct(array $karyawanIds, int $kunjunganId, int $tamuId, string $token)
    {
        $this->karyawanIds = $karyawanIds;
        $this->kunjunganId = $kunjunganId;
        $this->tamuId = $tamuId;
        $this->token = $token;
    }

    public function handle(): void
    {
        try {
            $kunjungan = Kunjungan::findOrFail($this->kunjunganId);
            $tamu = Tamu::findOrFail($this->tamuId);

            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($this->karyawanIds as $karyawanId) {
                try {
                    $karyawan = Karyawan::find($karyawanId);

                    if (!$karyawan) {
                        Log::warning('Karyawan not found', [
                            'karyawan_id' => $karyawanId,
                            'job_id' => $this->job->getJobId(),
                        ]);
                        $emailsFailed++;
                        continue;
                    }

                    if (!$karyawan->email_karyawan) {
                        Log::warning('Karyawan has no email', [
                            'karyawan_id' => $karyawanId,
                            'job_id' => $this->job->getJobId(),
                        ]);
                        $emailsFailed++;
                        continue;
                    }

                    Log::info('Sending confirmation email to karyawan', [
                        'karyawan_id' => $karyawan->id_karyawan,
                        'karyawan_email' => $karyawan->email_karyawan,
                        'kunjungan_id' => $kunjungan->id_kunjungan,
                        'attempt' => $this->attempts(),
                        'job_id' => $this->job->getJobId(),
                    ]);

                    Mail::to($karyawan->email_karyawan)->send(
                        new KunjunganConfirmation($karyawan, $tamu, $kunjungan, $this->token)
                    );

                    $emailsSent++;

                    Log::info('Confirmation email sent successfully', [
                        'karyawan_email' => $karyawan->email_karyawan,
                        'karyawan_name' => $karyawan->nama_karyawan,
                        'job_id' => $this->job->getJobId(),
                    ]);

                } catch (\Exception $mailError) {
                    Log::error('Failed to send email to individual karyawan', [
                        'karyawan_id' => $karyawanId,
                        'error' => $mailError->getMessage(),
                        'job_id' => $this->job->getJobId(),
                    ]);
                    $emailsFailed++;
                }
            }

            Log::info('Email batch completed', [
                'sent' => $emailsSent,
                'failed' => $emailsFailed,
                'total' => count($this->karyawanIds),
                'job_id' => $this->job->getJobId(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process confirmation email job', [
                'kunjungan_id' => $this->kunjunganId,
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
            'kunjungan_id' => $this->kunjunganId,
            'karyawan_ids' => $this->karyawanIds,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
