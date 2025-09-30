<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('email:all-employees')
    ->everyFiveMinutes()
    ->appendOutputTo(storage_path('logs/schedule.log'))
    ->emailOutputOnFailure(env('ADMIN_EMAIL'));
   

    // php artisan schedule:run //for manual testing
    //  * * * * * cd /Users/athiranayas/Desktop/Projects/EMS-EmployeeManagement && /opt/homebrew/bin/php artisan schedule:run >> /dev/null 2>&1