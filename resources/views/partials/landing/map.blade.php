@php
    $map = $sections->get('map');

    /**
     * Fills a rotated ellipse with a regular dot grid.
     *
     * Ported verbatim from the Figma Make source (`generateEllipseDots` in App.tsx) so the
     * stylised Indonesia dot map renders identically, but server-side. This is geometry
     * rather than content, so it stays in the template instead of the CMS.
     *
     * @return array<int, array{x: float, y: float}>
     */
    $generateEllipseDots = function (float $cx, float $cy, float $rx, float $ry, float $rotateDeg, float $spacing): array {
        $dots = [];
        $cos = cos(deg2rad($rotateDeg));
        $sin = sin(deg2rad($rotateDeg));

        for ($x = $cx - $rx - $spacing; $x <= $cx + $rx + $spacing; $x += $spacing) {
            for ($y = $cy - $ry - $spacing; $y <= $cy + $ry + $spacing; $y += $spacing) {
                $dx = $x - $cx;
                $dy = $y - $cy;
                $rotatedX = $dx * $cos + $dy * $sin;
                $rotatedY = -$dx * $sin + $dy * $cos;

                if (($rotatedX / $rx) ** 2 + ($rotatedY / $ry) ** 2 <= 1) {
                    $dots[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        return $dots;
    };

    /** Each entry is [centreX, centreY, radiusX, radiusY, rotationDeg, dotSpacing]. */
    $islands = [
        [96, 142, 54, 90, -32, 9],  // Sumatra
        [248, 236, 88, 19, 4, 9],   // Jawa
        [302, 132, 75, 74, 0, 9],   // Kalimantan
        [418, 92, 13, 38, 10, 9],   // Sulawesi - semenanjung utara
        [414, 163, 26, 44, 0, 9],   // Sulawesi - badan selatan
        [446, 128, 11, 26, 28, 9],  // Sulawesi - lengan timur
        [622, 157, 88, 62, 0, 9],   // Papua
        [502, 152, 18, 30, 0, 9],   // Maluku utara
        [516, 170, 11, 22, 12, 9],  // Maluku selatan
        [361, 244, 36, 11, 4, 9],   // Nusa Tenggara Barat
        [404, 249, 18, 9, 0, 9],    // Nusa Tenggara Timur
        [324, 234, 11, 9, 0, 9],    // Bali
    ];

    $dots = [];

    foreach ($islands as [$cx, $cy, $rx, $ry, $rotation, $spacing]) {
        $dots = array_merge($dots, $generateEllipseDots($cx, $cy, $rx, $ry, $rotation, $spacing));
    }

    $highlights = [[96, 98], [302, 95], [360, 244]];
@endphp

@if ($map)
    <section id="peta" class="bg-tani-cream py-20 md:py-28">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 md:grid-cols-[300px_1fr] md:px-10">
            <div>
                <div class="mb-4 text-[11px] font-bold uppercase tracking-[0.18em] text-tani-green">
                    {{ $map->copy('eyebrow') }}
                </div>

                <h2 class="mb-1 text-[2.4rem] font-black leading-[1.1] text-tani-ink">{{ $map->copy('heading_1') }}</h2>
                <h2 class="mb-6 text-[2.4rem] font-black leading-[1.1] text-tani-gold">{{ $map->copy('heading_2') }}</h2>

                <p class="mb-8 text-sm leading-relaxed text-gray-600">{{ $map->copy('body') }}</p>

                <a
                    href="{{ $map->copy('cta_href', '#') }}"
                    class="flex w-fit items-center gap-2 rounded-full bg-tani-green px-6 py-3.5 text-sm font-bold text-white transition-colors hover:bg-tani-green-hover"
                >
                    {{ $map->copy('cta_label') }}
                    <flux:icon.arrow-right class="size-[14px]" />
                </a>
            </div>

            <div class="relative">
                <div class="relative h-72 md:h-80">
                    <svg viewBox="0 0 740 290" class="size-full overflow-visible" role="img" aria-label="Peta sebaran pertanian lainnya di Indonesia">
                        @foreach ($dots as $dot)
                            <circle cx="{{ $dot['x'] }}" cy="{{ $dot['y'] }}" r="2.8" fill="#4a8c3f" opacity="0.82" />
                        @endforeach

                        @foreach ($highlights as [$hx, $hy])
                            <circle cx="{{ $hx }}" cy="{{ $hy }}" r="6" fill="#c8a84b" />
                        @endforeach
                    </svg>

                    @foreach ($map->items as $callout)
                        <div class="pointer-events-none absolute {{ $callout->get('position') }} rounded-xl bg-white p-3 text-xs shadow-lg">
                            <div class="font-bold text-tani-ink">{{ $callout->get('name') }}</div>
                            <div class="mt-0.5 font-semibold text-tani-green">{{ $callout->get('area') }}</div>
                            <div class="text-[10px] text-gray-400">{{ $callout->get('change') }}</div>
                        </div>
                    @endforeach

                    <a
                        href="{{ $map->copy('circle_href', '#') }}"
                        class="absolute right-0 top-1/2 flex size-28 -translate-y-1/2 flex-col items-center justify-center rounded-full bg-tani-clay text-center shadow-xl"
                    >
                        <span class="px-2 text-[10px] font-bold leading-tight text-white">
                            {!! implode('<br>', array_map('e', $map->copy('circle_lines', []))) !!}
                        </span>
                        <span class="mt-2 flex size-7 items-center justify-center rounded-full bg-white/25">
                            <flux:icon.arrow-right class="size-[12px] text-white" />
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif
