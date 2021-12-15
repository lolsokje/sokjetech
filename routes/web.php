<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CircuitController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\UniverseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('', [HomeController::class, 'index'])->name('index');

Route::get('/auth/discord/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/discord/callback', [AuthController::class, 'callback'])->name('auth.callback');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::group(['prefix' => 'circuits', 'as' => 'circuits.'], function () {
    Route::get('', [CircuitController::class, 'index'])->name('index');
    Route::get('create', [CircuitController::class, 'create'])->name('create');
    Route::get('{circuit}/edit', [CircuitController::class, 'edit'])->name('edit');
    Route::post('store', [CircuitController::class, 'store'])->name('store');
    Route::put('{circuit}/update', [CircuitController::class, 'update'])->name('update');
});

Route::group(['prefix' => 'universes', 'as' => 'universes.'], function () {
    Route::get('', [UniverseController::class, 'index'])->name('index');
    Route::get('create', [UniverseController::class, 'create'])->name('create');
    Route::post('store', [UniverseController::class, 'store'])->name('store');
    Route::get('{universe}', [UniverseController::class, 'show'])->name('show');
    Route::get('{universe}/edit', [UniverseController::class, 'edit'])->name('edit');
    Route::put('{universe}', [UniverseController::class, 'update'])->name('update');

    Route::group(['prefix' => '{universe}/drivers', 'as' => 'drivers.'], function () {
        Route::get('', [DriverController::class, 'index'])->name('index');
        Route::get('create', [DriverController::class, 'create'])->name('create');
        Route::post('store', [DriverController::class, 'store'])->name('store');
        Route::get('{driver}/edit', [DriverController::class, 'edit'])->name('edit');
        Route::put('{driver}', [DriverController::class, 'update'])->name('update');
    });

    Route::group(['prefix' => '{universe}'], function () {
        Route::resource('series', SeriesController::class)->except('destroy');
    });
});
