<?php

use App\Console\Commands\UpdatePoolStats;
use App\Console\Commands\UpdateTokenHolders;
use App\Install\Middleware\InstallMiddleware;
use App\Services\Rate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//schedule
Schedule::command(UpdatePoolStats::class)->everyTenMinutes();
Schedule::command(UpdateTokenHolders::class)->everyTenMinutes();
//commands
Artisan::command('rates:update', function () {
    Rate::update();
})->purpose('Update crypto rates in the db')->hourly();

//utility
Artisan::command('lang:strap', function () {
    Artisan::call('translatable:export', ['lang' => 'en']);
    Artisan::call('vue-i18n:generate', ['--with-vendor' => 'en']);
});

Artisan::command('skip:install', function () {
    InstallMiddleware::markAsInstalled();
});
