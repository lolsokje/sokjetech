<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CircuitController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EngineController;
use App\Http\Controllers\EntrantController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LineupController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\TeamController;
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
    Route::resource('teams', TeamController::class)->except('destroy');
});

Route::group(['prefix' => 'series/{series}', 'as' => 'series.'], function () {
    Route::resource('engines', EngineController::class)->except('destroy', 'show');
    Route::resource('seasons', SeasonController::class)->except('destroy');
});

Route::group(['prefix' => 'seasons/{season}', 'as' => 'seasons.'], function () {
    Route::get('races/reorder', [RaceController::class, 'reorder'])->name('races.reorder');
    Route::put('races/order', [RaceController::class, 'order'])->name('races.order');
    Route::resource('races', RaceController::class)->except('destroy');

    Route::resource('entrants', EntrantController::class)->except('destroy', 'show');
    Route::resource('lineups', LineupController::class)->except('destroy', 'store');
    Route::post('/{entrant}/lineups', [LineupController::class, 'store'])->name('lineups.store');
});
