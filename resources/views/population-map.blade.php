@php
    $siteName = data_get($settings, 'site_name', 'Pertanian Lainnya');
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Peta Populasi PL - {{ $siteName }}</title>

        <meta name="description" content="Peta interaktif sebaran populasi PL (Pertanian Lainnya) 2021-2025.">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-screen overflow-hidden bg-tani-cream font-display text-tani-ink antialiased">
        @include('partials.landing.navbar')

        <main class="h-screen w-full pt-16" x-data="populationMap()" x-init="init()">
            <div class="relative h-[calc(100vh-4rem)] w-full">
                <div x-ref="map" class="absolute inset-0 h-full w-full"></div>

                {{-- Floating title + year selector --}}
                <div class="pointer-events-auto absolute left-4 top-4 z-[1000] max-w-xs rounded-2xl bg-white/90 p-4 shadow-lg backdrop-blur md:left-6">
                    <div class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-tani-gold">
                        Peta Populasi
                    </div>
                    <h1 class="text-lg font-black leading-tight text-tani-ink">
                        PL Tahun <span x-text="currentYear">@json($years[count($years) - 1])</span>
                    </h1>
                    <p class="mt-2 text-xs leading-relaxed text-gray-600">
                        Klik polygon untuk melihat jumlah populasi tiap blok.
                    </p>

                    <div class="mt-3 flex flex-wrap gap-1.5" x-show="years.length">
                        <template x-for="year in years" :key="year">
                            <button
                                type="button"
                                x-on:click="selectYear(year)"
                                x-text="year"
                                class="rounded-full px-3 py-1 text-xs font-bold transition-colors"
                                :class="year === activeYear ? 'bg-tani-forest text-white' : 'bg-tani-mint text-tani-forest hover:bg-tani-green/20'"
                            ></button>
                        </template>
                    </div>
                </div>

                {{-- Loading indicator --}}
                <div
                    x-show="loading"
                    x-transition.opacity
                    class="absolute bottom-4 left-1/2 z-[1000] -translate-x-1/2 rounded-full bg-tani-night/90 px-5 py-2.5 text-xs font-semibold text-white shadow-lg"
                >
                    Memuat data tahun <span x-text="activeYear"></span>…
                </div>

                {{-- Legend --}}
                <aside class="pointer-events-none absolute right-4 top-4 z-[1000] w-[190px] rounded-2xl bg-white/90 p-4 shadow-lg backdrop-blur md:right-6">
                    <h2 class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-tani-ink">
                        Jumlah Populasi
                    </h2>
                    <div class="space-y-1.5" x-show="loaded">
                        <template x-for="bucket in legend" :key="bucket.label">
                            <div class="flex items-center gap-2 text-[11px] text-gray-700">
                                <span class="h-3 w-5 shrink-0 rounded-sm" :style="`background:${bucket.color}`"></span>
                                <span x-text="bucket.label"></span>
                            </div>
                        </template>
                    </div>
                    <p class="mt-3 text-[10px] leading-relaxed text-gray-400">
                        Sumber: data PL. Klik polygon untuk detail.
                    </p>
                </aside>

                {{-- Popup fallback (Leaflet bindPopup) --}}
            </div>
        </main>

        @livewireScripts

        <script>
            window.populationMap = () => ({
                map: null,
                layer: null,
                years: @json($years),
                activeYear: @json($years[count($years) - 1]),
                loading: false,
                loaded: false,
                legend: [],
                currentYear: @json($years[count($years) - 1]),

                init() {
                    this.$nextTick(() => {
                        const el = this.$refs.map;
                        if (! el || typeof L === 'undefined') return;

                        // Teardown any previously initialised map (Alpine/Livewire may re-run x-init).
                        try {
                            if (el._leaflet_id != null) {
                                L.getMap(el).remove();
                            }
                        } catch (e) {
                            el._leaflet_id = null;
                            el.innerHTML = '';
                        }

                        // Extent of the PL dataset.
                        const bounds = L.latLngBounds(
                            [-5.516, 101.017],
                            [-2.277, 103.764]
                        );

                        this.map = L.map(el, {
                            preferCanvas: true,
                            zoomControl: true,
                        });

                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; OpenStreetMap &copy; CARTO',
                            maxZoom: 19,
                        }).addTo(this.map);

                        this.map.fitBounds(bounds, { padding: [20, 20] });
                        this.map.invalidateSize();
                        setTimeout(() => this.map && this.map.invalidateSize(), 200);
                        this._onResize = () => this.map && this.map.invalidateSize();
                        window.addEventListener('resize', this._onResize);

                        this.loadYear(this.activeYear);
                    });
                },

                async selectYear(year) {
                    if (year === this.activeYear && this.loaded) return;

                    this.activeYear = year;
                    this.currentYear = year;
                    await this.loadYear(year);
                },

                async loadYear(year) {
                    this.loading = true;
                    this.loaded = false;

                    if (this.layer) {
                        this.map.removeLayer(this.layer);
                        this.layer = null;
                    }

                    try {
                        const response = await fetch(`/maps/pl_${year}.geojson`);
                        if (! response.ok) throw new Error(`HTTP ${response.status}`);
                        const data = await response.json();

                        const counts = data.features.map(f => f.properties.count);
                        let min = counts[0];
                        let max = counts[0];
                        for (let i = 1; i < counts.length; i++) {
                            if (counts[i] < min) min = counts[i];
                            if (counts[i] > max) max = counts[i];
                        }
                        this.legend = this.buildLegend(min, max);

                        this.layer = L.geoJSON(data, {
                            style: (feature) => this.styleFeature(feature, min, max),
                            onEachFeature: (feature, layer) => {
                                const c = feature.properties.count.toLocaleString('id-ID');
                                layer.bindPopup(
                                    `<div style="min-width:150px">` +
                                        `<p style="font-weight:700;margin:0 0 2px;color:#1a2010">Blok Sensus</p>` +
                                        `<p style="font-size:12px;margin:0 0 8px;color:#444">Populasi: <strong>${c}</strong></p>` +
                                    `</div>`
                                );
                            },
                        }).addTo(this.map);

                        this.loaded = true;
                    } catch (e) {
                        console.error('Gagal memuat data', year, e);
                    } finally {
                        this.loading = false;
                    }
                },

                styleFeature(feature, min, max) {
                    const c = feature.properties.count;
                    const q = c > 0 ? Math.min(1, Math.log(c) / Math.log(max)) : 0;
                    return {
                        color: '#2d6b1a',
                        weight: 0.4,
                        opacity: 0.6,
                        fillColor: this.colorFor(q),
                        fillOpacity: 0.6,
                    };
                },

                // Diverging green scale: light -> deep green.
                colorFor(q) {
                    const stops = [
                        [0.00, '#e8f5e2'],
                        [0.25, '#bfe3b0'],
                        [0.50, '#8cc976'],
                        [0.75, '#54a83f'],
                        [1.00, '#2d6b1a'],
                    ];
                    for (let i = 0; i < stops.length - 1; i++) {
                        const [t0, c0] = stops[i];
                        const [t1, c1] = stops[i + 1];
                        if (q >= t0 && q <= t1) {
                            const f = t1 === t0 ? 0 : (q - t0) / (t1 - t0);
                            return this.lerpColor(c0, c1, f);
                        }
                    }
                    return stops[stops.length - 1][1];
                },

                lerpColor(a, b, f) {
                    const pa = [parseInt(a.slice(1, 3), 16), parseInt(a.slice(3, 5), 16), parseInt(a.slice(5, 7), 16)];
                    const pb = [parseInt(b.slice(1, 3), 16), parseInt(b.slice(3, 5), 16), parseInt(b.slice(5, 7), 16)];
                    const c = pa.map((v, i) => Math.round(v + (pb[i] - v) * f));
                    return `rgb(${c.join(',')})`;
                },

                buildLegend(min, max) {
                    const buckets = [min, ...this.quantiles(min, max, 4).slice(0, 3), max + 1];
                    const labels = [];
                    for (let i = 0; i < buckets.length - 1; i++) {
                        const lo = Math.max(min, buckets[i]);
                        const hi = buckets[i + 1] - 1;
                        const q = buckets[i + 1] > max ? 1 : this.colorRatio(buckets[i], buckets[i + 1], min, max);
                        labels.push({
                            color: this.colorFor(q),
                            label: `${this.shorten(lo)} – ${this.shorten(hi)}`,
                        });
                    }
                    return labels;
                },

                quantiles(min, max, n) {
                    const out = [];
                    for (let i = 1; i < n; i++) {
                        out.push(min + (max - min) * (i / n));
                    }
                    return out;
                },

                colorRatio(lo, hi, min, max) {
                    return Math.min(1, Math.log(hi) / Math.log(max));
                },

                shorten(v) {
                    if (v >= 1_000_000) return `${(v / 1_000_000).toFixed(1)} jt`;
                    if (v >= 1_000) return `${(v / 1_000).toFixed(0)} rb`;
                    return `${v}`;
                },
            });
        </script>
    </body>
</html>
