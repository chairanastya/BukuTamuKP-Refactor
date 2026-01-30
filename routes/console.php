<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\CancelExpiredKunjunganJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule jobs
Schedule::job(new CancelExpiredKunjunganJob)
    ->everyThirtySeconds()
    ->withoutOverlapping()
    ->name('cancel-expired-kunjungan')
    ->onOneServer();
