<?php

use Illuminate\Support\Facades\Route;

Route::get('version', fn () => config('app.version'))->name('version');
