<?php

namespace App\Jobs;

use App\Mail\NotulensiAvailable;
use App\Models\Kunjungan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNotulensiAvailableJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = [1, 3, 5];

    public function __construct(
        public int $kunjunganId,
        public string $token
    ) {}

    public function handle(): void
    {
        try {
            $kunjungan = Kunjungan::with(['tamu', 'karyawan'])->findOrFail($this->kunjunganId);

            // Send email to tamu (visitor)
            if ($kunjungan->tamu && $kunjungan->tamu->email_tamu) {
                Mail::to($kunjungan->tamu->email_tamu)->send(
                    new NotulensiAvailable(
                        $kunjungan->tamu->nama_tamu,
                        $kunjungan,
                        $this->token
                    )
                );
                
                Log::info('Notulensi available email sent to visitor', [
                    'kunjungan_id' => $this->kunjunganId,
                    'visitor_email' => $kunjungan->tamu->email_tamu
                ]);
            }

            // Send email to each karyawan (employee)
            if ($kunjungan->karyawan->isNotEmpty()) {
                foreach ($kunjungan->karyawan as $karyawan) {
                    if ($karyawan->email_karyawan) {
                        Mail::to($karyawan->email_karyawan)->send(
                            new NotulensiAvailable(
                                $karyawan->nama_karyawan,
                                $kunjungan,
                                $this->token
                            )
                        );
                        
                        Log::info('Notulensi available email sent to employee', [
                            'kunjungan_id' => $this->kunjunganId,
                            'karyawan_id' => $karyawan->id_karyawan,
                            'employee_email' => $karyawan->email_karyawan
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notulensi available emails', [
                'kunjungan_id' => $this->kunjunganId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
