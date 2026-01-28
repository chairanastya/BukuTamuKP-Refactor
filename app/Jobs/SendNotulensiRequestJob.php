<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Kunjungan;
use App\Mail\NotulensiRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNotulensiRequestJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = [1, 3, 5];

    public function __construct(
        public int $kunjunganId,
        public string $token
    ) {
    }

    public function handle(): void
    {
        try {
            $kunjungan = Kunjungan::with(['tamu', 'karyawan'])->findOrFail($this->kunjunganId);

            if ($kunjungan->karyawan->isEmpty()) {
                Log::warning('No karyawan associated with kunjungan', [
                    'kunjungan_id' => $this->kunjunganId,
                ]);
                return;
            }

            foreach ($kunjungan->karyawan as $karyawan) {
                if (!$karyawan->email_karyawan) {
                    Log::warning('Karyawan has no email', [
                        'karyawan_id' => $karyawan->id_karyawan,
                        'kunjungan_id' => $this->kunjunganId,
                    ]);
                    continue;
                }

                Log::info('Sending notulensi request email to karyawan', [
                    'karyawan_id' => $karyawan->id_karyawan,
                    'email' => $karyawan->email_karyawan,
                    'kunjungan_id' => $this->kunjunganId,
                ]);

                Mail::to($karyawan->email_karyawan)->send(
                    new NotulensiRequest($karyawan, $kunjungan, $this->token)
                );

                Log::info('Notulensi request email sent', [
                    'karyawan_id' => $karyawan->id_karyawan,
                    'karyawan_name' => $karyawan->nama_karyawan,
                    'kunjungan_id' => $this->kunjunganId,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in SendNotulensiRequestJob', [
                'kunjungan_id' => $this->kunjunganId,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }
}
