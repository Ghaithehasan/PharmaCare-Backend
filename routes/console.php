<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');




Schedule::command('medicines:check-stock')->everyFifteenSeconds();
Schedule::command('medicines:check-expired')->everyFifteenSeconds();
Schedule::command('app:check-confirm-date')->daily();
