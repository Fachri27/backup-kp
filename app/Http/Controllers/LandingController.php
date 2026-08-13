<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    /**
     * Render the public landing page from CMS-managed content.
     *
     * Only published sections and published rows are loaded, so hiding a section in
     * the CMS removes it from the page without touching a Blade file.
     */
    public function __invoke(): View
    {
        $sections = PageSection::query()
            ->published()
            ->with(['items' => fn ($query) => $query->where('is_published', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');

        return view('welcome', [
            'sections' => $sections,
            'settings' => [
                ...Setting::group('general'),
                ...Setting::group('social'),
            ],
        ]);
    }
}
