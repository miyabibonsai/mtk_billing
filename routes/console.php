<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('generate-billing datasim')->name('datasim_billing')->everyThreeMinutes()->withoutOverlapping();
Schedule::command('generate-billing simcard')->name('simcard_billing')->everyTenMinutes()->withoutOverlapping();

