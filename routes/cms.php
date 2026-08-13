<?php

use App\Livewire\Cms\Content;
use App\Livewire\Cms\Dashboard;
use App\Livewire\Cms\Media;
use App\Livewire\Cms\PlatformMap;
use App\Livewire\Cms\Settings;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('cms')
    ->name('cms.')
    ->group(function () {
        Route::livewire('/', Dashboard::class)->name('dashboard');

        Route::livewire('content', Content\Index::class)->name('content.index');
        Route::livewire('content/{section}', Content\Edit::class)->name('content.edit');

        Route::livewire('media', Media\Index::class)->name('media.index');

        Route::livewire('platform-map', PlatformMap::class)->name('platform-map.index');

        Route::livewire('settings', Settings\Index::class)->name('settings.index');
    });
