<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

class PlatformMapController extends Controller
{
    /**
     * Render the public, interactive platform map.
     */
    public function __invoke(): View
    {
        $sections = PageSection::query()
            ->published()
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');

        return view('platform-map', [
            'center' => config('platforms.center'),
            'platforms' => config('platforms.items'),
            'sections' => $sections,
            'settings' => [
                ...Setting::group('general'),
                ...Setting::group('social'),
            ],
        ]);
    }
}
