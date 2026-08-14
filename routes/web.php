<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\PlatformMapController;
use App\Http\Controllers\PopulationMapController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');
Route::get('/peta', PlatformMapController::class)->name('platform-map.public');
Route::get('/peta-populasi', PopulationMapController::class)->name('population-map.public');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/cms.php';
