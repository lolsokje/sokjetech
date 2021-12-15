<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CircuitController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\UniverseController;
use Illuminate\Support\Facades\Route;

Route::get('', [HomeController::class, 'index'])->name('index');

Route::get('/auth/discord/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/discord/callback', [AuthController::class, 'callback'])->name('auth.callback');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::resource('circuits', CircuitController::class)->except('destroy');

Route::resource('universes', UniverseController::class)->except('destroy');

Route::group(['prefix' => 'universes/{universe}', 'as' => 'universes.'], function () {
    Route::resource('drivers', DriverController::class)->except('destroy');
    Route::resource('series', SeriesController::class)->except('destroy');
});
