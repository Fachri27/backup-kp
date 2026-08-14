<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

class PopulationMapController extends Controller
{
    /**
     * Render the public population map across the PL (Pertanian Lainnya) years.
     */
    public function __invoke(): View
    {
        $sections = PageSection::query()
            ->published()
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');

        return view('population-map', [
            'years' => ['2021', '2022', '2023', '2024', '2025'],
            'sections' => $sections,
            'settings' => [
                ...Setting::group('general'),
                ...Setting::group('social'),
            ],
        ]);
    }
}
