<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule investment accrual processing
Schedule::command('investments:process-accruals')
    ->daily()
    ->at('01:00')
    ->withoutOverlapping()
    ->runInBackground();

// Schedule deposit accrual processing
Schedule::command('deposits:process-accruals')
    ->daily()
    ->at('01:30')
    ->withoutOverlapping()
    ->runInBackground();
