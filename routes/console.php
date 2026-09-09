<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lock daily entries older than 2 days every night, so only Admins can edit
// historical records once billing/reconciliation windows have likely closed.
Schedule::command('daily-entries:lock-past')->dailyAt('23:55');
