<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FactoriesController;
use App\Http\Controllers\Admin\HoldersController;
use App\Http\Controllers\Admin\LaunchpadsController;
use App\Http\Controllers\Admin\MsgsController;
use App\Http\Controllers\Admin\NftsController;
use App\Http\Controllers\Admin\PromosController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TradesController;
use App\Http\Middleware\DemoMode;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
#users
Route::name('users.')->controller(DashboardController::class)->group(function () {
    Route::get('/users', 'index')->name('index');
    Route::put('/users/toggle/{user}', 'toggle')->name('toggle');
    Route::put('/users/banned/{user}', 'banned')->name('banned');
});
#users


#factories
Route::name('factories.')->controller(FactoriesController::class)->group(function () {
    Route::get('/factories', 'index')->name('index');
    Route::get('/factories/create', 'create')->name('create');
    Route::post('/factories/store', 'store')->name('store');
    Route::get('/factories/{factory}/show', 'show')->name('show');
    Route::get('/factories/{factory}/edit', 'edit')->name('edit');
    Route::put('/factories/{factory}', 'update')->name('update');
    Route::put('/factories/toggle/{factory}', 'toggle')->middleware(DemoMode::class)->name('toggle');
    Route::delete('/factories/{factory}', 'destroy')->middleware(DemoMode::class)->name('destroy');
});
#factories


#launchpads
Route::name('launchpads.')->controller(LaunchpadsController::class)->group(function () {
    Route::get('/launchpads', 'index')->name('index');
    Route::put('/launchpads/toggle/{launchpad}', 'toggle')->middleware(DemoMode::class)->name('toggle');
    Route::put('/launchpads/kingofthehill/{launchpad}', 'kingofthehill')->name('kingofthehill');
    Route::put('/launchpads/featured/{launchpad}', 'featured')->name('featured');
    Route::delete('/launchpads/{launchpad}', 'destroy')->middleware(DemoMode::class)->name('destroy');
});
#launchpads



#holders
Route::name('holders.')->controller(HoldersController::class)->group(function () {
    Route::get('/holders', 'index')->name('index');
});
#holders



#msgs
Route::name('msgs.')->controller(MsgsController::class)->group(function () {
    Route::get('/msgs', 'index')->name('index');
    Route::put('/msgs/status/{msg}/{status}', 'status')->name('status');
    Route::delete('/msgs/{msg}', 'destroy')->middleware(DemoMode::class)->name('destroy');
});
#msgs



#trades
Route::name('trades.')->controller(TradesController::class)->group(function () {
    Route::get('/trades', 'index')->name('index');
    Route::delete('/trades/{trade}', 'destroy')->middleware(DemoMode::class)->name('destroy');
});
#trades
#settings
Route::name('settings.')->controller(SettingsController::class)->group(function () {
    Route::put('/settings', 'update')->middleware(DemoMode::class)->name('update');
    Route::put('/mail/{mailer}', 'saveMailSettings')->middleware(DemoMode::class)->name('mail');
});
#settings


#promos
Route::name('promos.')->controller(PromosController::class)->group(function () {
    Route::get('/promos', 'index')->name('index');
    Route::get('/promos/create', 'create')->name('create');
    Route::post('/promos/store', 'store')->middleware(DemoMode::class)->name('store');
    Route::get('/promos/{promo}/edit', 'edit')->name('edit');
    Route::put('/promos/{promo}', 'update')->middleware(DemoMode::class)->name('update');
    Route::put('/promos/toggle/{promo}', 'toggle')->middleware(DemoMode::class)->name('toggle');
    Route::delete('/promos/{promo}', 'destroy')->middleware(DemoMode::class)->name('destroy');
});
#promos


#nfts
Route::name('nfts.')->controller(NftsController::class)->group(function () {
    Route::get('/nfts', 'index')->name('index');
    Route::get('/nfts/create', 'create')->name('create');
    Route::post('/nfts/store', 'store')->name('store');
    Route::get('/nft/{nft:contract}', 'edit')->name('edit');
    Route::get('/metadata/{nft:contract}', 'metadata')->name('metadata');
    Route::put('/metadata/{nft}/update', 'updateMetadata')->name('metadata.update');
    Route::put('/nfts/{nft}', 'update')->name('update');
    Route::put('/nfts/toggle/{nft}', 'toggle')->name('toggle');
    Route::delete('/nfts/{nft}', 'destroy')->name('destroy');
});
#nft