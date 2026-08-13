@php
    $siteName = data_get($settings, 'site_name', 'Pertanian Lainnya');
    $tagline = data_get($settings, 'tagline', 'Platform Data Pertanian Indonesia');
    $markerData = collect($platforms)->map(fn (array $p): array => [
        'id' => $p['id'],
        'name' => $p['name'],
        'area' => $p['area'] ?? $p['name'],
        'lat' => $p['lat'],
        'lng' => $p['lng'],
        'description' => $p['description'],
        'status' => $p['status'],
    ])->values()->toJson();

    $statusLabel = ['active' => 'Aktif', 'maintenance' => 'Pemeliharaan', 'offline' => 'Offline'];
    $statusTone = ['active' => 'bg-tani-green', 'maintenance' => 'bg-tani-gold', 'offline' => 'bg-red-500'];
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Peta Platform - {{ $siteName }}</title>

        <meta name="description" content="Peta interaktif sebaran platform {{ $siteName }} di seluruh Indonesia.">

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

        <main class="h-screen w-full pt-16" x-data="publicMap()" x-init="init({{ $center['lat'] }}, {{ $center['lng'] }}, {{ $center['zoom'] }}, {{ $markerData }})">
            {{-- Map fills the viewport below the navbar --}}
            <div class="relative h-[calc(100vh-4rem)] w-full">
                <div x-ref="map" class="absolute inset-0 h-full w-full"></div>

                {{-- Floating title --}}
                <div class="pointer-events-none absolute left-4 top-4 z-[1000] max-w-xs rounded-2xl bg-white/90 p-4 shadow-lg backdrop-blur md:left-6">
                    <div class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-tani-gold">
                        Peta Sebaran
                    </div>
                    <h1 class="text-lg font-black leading-tight text-tani-ink">
                        Platform di Seluruh Indonesia
                    </h1>
                    <p class="mt-2 text-xs leading-relaxed text-gray-600">
                        Klik penanda atau daftar untuk melihat detail tiap titik.
                    </p>
                </div>

                {{-- Floating sidebar --}}
                <aside class="absolute right-4 top-4 z-[1000] flex max-h-[calc(100vh-7rem)] w-[300px] flex-col gap-3 md:right-6">
                    <div class="flex flex-col rounded-2xl bg-white/95 p-4 shadow-lg backdrop-blur">
                        <h2 class="mb-1 text-sm font-bold uppercase tracking-wide text-tani-ink">Daftar Platform</h2>
                        <p class="mb-3 text-xs text-gray-500">{{ count($platforms) }} titik tersebar</p>

                        <div class="space-y-2 overflow-y-auto pr-1">
                            @forelse ($platforms as $platform)
                                <button
                                    type="button"
                                    x-on:click="focus({{ $platform['lat'] }}, {{ $platform['lng'] }}, {{ $platform['id'] }})"
                                    class="flex w-full items-start gap-3 rounded-xl border border-tani-forest/10 p-3 text-left transition-colors hover:bg-tani-mint"
                                >
                                    <span class="mt-1 size-2.5 shrink-0 rounded-full {{ $statusTone[$platform['status']] ?? 'bg-gray-400' }}"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-tani-ink">{{ $platform['name'] }}</span>
                                        <span class="mt-0.5 block truncate text-xs text-gray-500">{{ $platform['area'] ?? $platform['name'] }}</span>
                                    </span>
                                    <span class="shrink-0 rounded-full bg-tani-mint px-2 py-0.5 text-[10px] font-semibold text-tani-green">
                                        {{ $statusLabel[$platform['status']] ?? ucfirst($platform['status']) }}
                                    </span>
                                </button>
                            @empty
                                <p class="text-xs text-gray-500">Belum ada platform.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/95 p-4 shadow-lg backdrop-blur">
                        <h2 class="mb-2 text-sm font-bold uppercase tracking-wide text-tani-ink">Keterangan</h2>
                        <div class="space-y-2">
                            @foreach (['active' => 'Aktif', 'maintenance' => 'Pemeliharaan', 'offline' => 'Offline'] as $key => $label)
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <span class="size-2.5 rounded-full {{ $statusTone[$key] }}"></span>
                                    {{ $label }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        @livewireScripts

        <script>
            window.publicMap = () => ({
                map: null,
                markers: {},

                init(lat, lng, zoom, platforms) {
                    this.$nextTick(() => {
                        const el = this.$refs.map;
                        if (! el || typeof L === 'undefined') return;

                        // Leaflet stamps el._leaflet_id once initialised. Livewire/Flux can
                        // re-run Alpine's x-init, so tear down any existing instance first
                        // instead of crashing with "Map container is already initialized".
                        try {
                            if (el._leaflet_id != null) {
                                L.getMap(el).remove();
                            }
                        } catch (e) {
                            el._leaflet_id = null;
                            el.innerHTML = '';
                        }

                        this.markers = {};
                        this.map = L.map(el).setView([lat, lng], zoom);

                        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; OpenStreetMap &copy; CARTO',
                            maxZoom: 19,
                        }).addTo(this.map);

                        platforms.forEach((platform) => {
                            if (typeof platform.lat !== 'number' || typeof platform.lng !== 'number') return;

                            const colour =
                                platform.status === 'active' ? '#2d6b1a' :
                                platform.status === 'maintenance' ? '#c8a84b' :
                                '#ef4444';

                            const marker = L.circleMarker([platform.lat, platform.lng], {
                                radius: 9,
                                fillColor: colour,
                                color: '#ffffff',
                                weight: 2,
                                opacity: 1,
                                fillOpacity: 0.9,
                            }).addTo(this.map);

                            marker.bindPopup(
                                `<div style="min-width:180px">` +
                                    `<p style="font-weight:700;margin:0 0 2px;color:#1a2010">${platform.name}</p>` +
                                    `<p style="font-size:11px;margin:0 0 6px;color:#666">${platform.area}</p>` +
                                    `<p style="font-size:12px;margin:0 0 8px;color:#444">${platform.description}</p>` +
                                    `<span style="display:inline-block;font-size:10px;font-weight:600;text-transform:uppercase;padding:2px 8px;border-radius:9999px;background:#e8f5e2;color:${colour}">${platform.status}</span>` +
                                `</div>`
                            );

                            this.markers[platform.id] = marker;
                        });

                        // Ensure Leaflet picks up the full-screen size once layout
                        // settles and on resize.
                        this.map.invalidateSize();
                        setTimeout(() => this.map && this.map.invalidateSize(), 200);
                        this._onResize = () => this.map && this.map.invalidateSize();
                        window.addEventListener('resize', this._onResize);
                    });
                },

                focus(lat, lng, id) {
                    if (! this.map) return;

                    this.map.flyTo([lat, lng], 10, { duration: 0.75 });

                    if (this.markers[id]) {
                        this.markers[id].openPopup();
                    }
                },
            });
        </script>
    </body>
</html>