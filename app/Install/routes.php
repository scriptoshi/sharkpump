<?php

use App\Install\Controllers\InstallController;
use App\Install\Middleware\InstallMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(InstallController::class)
    ->middleware(InstallMiddleware::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/requirements', 'requirements')->name('requirements');
        Route::get('/permissions', 'permissions')->name('permissions');
        Route::get('/environment', 'environment')->name('environment');
        Route::post('/environment/save', 'saveEnvironment')->name('environment.save');
        // should pass for now
        Route::get('/final', 'final')->name('final')->withoutMiddleware(InstallMiddleware::class);
    });
