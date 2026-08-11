<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monthly AI insights generation
// Runs on the 1st of every month at 9:00 AM
Schedule::command('insights:monthly')->monthlyOn(1, '09:00');

// Deriv Trading Module Automation
// ensure the websocket listener is always up
Schedule::command('deriv:connect')->everyMinute()->withoutOverlapping();

// ensure daily performance snapshots and sync
Schedule::command('deriv:sync')->daily();
