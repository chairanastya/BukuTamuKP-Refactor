<?php

namespace App\Jobs;

use App\Models\Kunjungan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CancelExpiredKunjunganJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * Cancel kunjungan with pending status that was created more than 10 minutes ago
     * and has not been confirmed by karyawan or resepsionis
     */
    public function handle(): void
    {
        try {
            $tenMinutesAgo = Carbon::now()->subMinutes(10);

            // Find pending kunjungan created more than 10 minutes ago
            $expiredKunjungans = Kunjungan::where('status', 'pending')
                ->where('created_at', '<', $tenMinutesAgo)
                ->get();

            $count = 0;

            foreach ($expiredKunjungans as $kunjungan) {
                $kunjungan->update([
                    'status' => 'canceled',
                    'alasan_batal' => 'Otomatis dibatalkan: Tidak ada konfirmasi dalam 10 menit.'
                ]);

                Log::info('Kunjungan auto-canceled (expired >10 min)', [
                    'id_kunjungan' => $kunjungan->id_kunjungan,
                    'created_at' => $kunjungan->created_at,
                    'canceled_at' => now(),
                    'tamu' => $kunjungan->tamu->nama_tamu ?? 'Unknown'
                ]);

                $count++;
            }

            if ($count > 0) {
                Log::info("Auto-cancel job completed: {$count} kunjungan canceled");
            }
        } catch (\Exception $e) {
            Log::error('CancelExpiredKunjunganJob failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
