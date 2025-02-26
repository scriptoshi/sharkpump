<?php

use App\Console\Commands\UpdatePoolStats;
use App\Console\Commands\UpdateTokenHolders;
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

use Illuminate\Support\Facades\Storage;

Artisan::command('s3:rename-folder', function () {
    $disk = Storage::disk('do');

    // Get all files in the source folder
    $files = $disk->allFiles('memex');

    // Move each file to the new location while preserving visibility
    foreach ($files as $file) {
        $newPath = str_replace('memex/', 'scriptoshi/', $file);
        // Move the file
        $disk->move($file, $newPath);
        // Set the same visibility on the new path
        $disk->setVisibility($newPath, 'public');
    }

    $this->info('Folder renamed successfully from /memex to /scriptoshi');
})->purpose('Rename S3 folder from /memex to /scriptoshi while preserving file visibility');
