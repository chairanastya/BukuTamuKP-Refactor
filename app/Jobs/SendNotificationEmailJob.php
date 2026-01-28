<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Kunjungan;
use App\Mail\KunjunganNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNotificationEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = [1, 3, 5];

    public function __construct(
        public int $kunjunganId,
        public string $status
    ) {
    }

    public function handle(): void
    {
        try {
            $kunjungan = Kunjungan::with(['tamu', 'karyawan'])->findOrFail($this->kunjunganId);

            $tamu = $kunjungan->tamu;
            $karyawan = $kunjungan->karyawan()->first();

            if (!$karyawan) {
                Log::warning('No employee associated with kunjungan', [
                    'kunjungan_id' => $this->kunjunganId,
                ]);
                return;
            }

            if (!$tamu->email_tamu) {
                Log::warning('Guest has no email', [
                    'kunjungan_id' => $this->kunjunganId,
                    'tamu_id' => $tamu->id_tamu,
                ]);
                return;
            }

            Log::info('Sending notification email to guest', [
                'kunjungan_id' => $this->kunjunganId,
                'email' => $tamu->email_tamu,
                'status' => $this->status,
            ]);

            Mail::to($tamu->email_tamu)->send(
                new KunjunganNotification($tamu, $karyawan, $kunjungan, $this->status)
            );

            Log::info('Notification email successfully sent to guest', [
                'kunjungan_id' => $this->kunjunganId,
                'tamu_name' => $tamu->nama_tamu,
                'status' => $this->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in SendNotificationEmailJob', [
                'kunjungan_id' => $this->kunjunganId,
                'status' => $this->status,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }
}
