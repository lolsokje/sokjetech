<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CircuitController;
use App\Http\Controllers\HomeController;
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
    Route::post('store', [CircuitController::class, 'store'])->name('store');
    Route::put('{circuit}/update', [CircuitController::class, 'update'])->name('update');
});
