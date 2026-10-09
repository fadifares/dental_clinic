<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Daily Appointment Reminders (Sent every evening at 18:00 for tomorrow's appointments)
Schedule::command('clinic:send-reminders')->dailyAt('18:00');
