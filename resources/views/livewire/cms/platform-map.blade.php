@php
    $center = $this->center;
    $platforms = $this->platforms;
    $markerData = collect($platforms)->map(fn (array $p): array => [
        'id' => $p['id'],
        'name' => $p['name'],
        'lat' => $p['lat'],
        'lng' => $p['lng'],
        'description' => $p['description'],
        'status' => $p['status'],
    ])->values()->toJson();
@endphp

<div class="flex h-full flex-col overflow-hidden" x-data="platformMap()" x-init="init({{ $center['lat'] }}, {{ $center['lng'] }}, {{ $center['zoom'] }}, {{ $markerData }})">
    <header class="flex shrink-0 items-center justify-between border-b border-cms-border bg-cms-bg px-8 py-4">
        <div>
            <h1 class="font-cms-serif text-lg font-semibold text-cms-text">Platform Map</h1>
            <p class="mt-0.5 text-sm text-cms-text-3">Distribution of platforms across the region</p>
        </div>

        <div class="hidden gap-3 sm:flex">
            @foreach ($this->statusSummary as $summary)
                <div class="rounded-lg border border-cms-border bg-cms-surface px-4 py-2">
                    <p class="text-[10px] font-medium uppercase tracking-widest text-cms-text-3">{{ $summary['label'] }}</p>
                    <p class="mt-0.5 font-cms-serif text-xl font-light tracking-tight text-cms-text">{{ $summary['value'] }}</p>
                </div>
            @endforeach
        </div>
    </header>

    {{-- Map + sidebar list, fills remaining height --}}
    <div class="grid min-h-0 flex-1 gap-4 p-4 lg:grid-cols-3">
        <div class="min-h-0 overflow-hidden rounded-xl border border-cms-border bg-cms-surface lg:col-span-2">
            <div x-ref="map" class="h-full w-full"></div>
        </div>

        <div class="flex min-h-0 flex-col rounded-xl border border-cms-border bg-cms-surface p-5">
            <h2 class="mb-4 shrink-0 text-sm font-semibold text-cms-text">Platforms</h2>

            <div class="min-h-0 flex-1 space-y-2 overflow-y-auto">
                @forelse ($platforms as $platform)
                    <button
                        type="button"
                        x-on:click="focus({{ $platform['lat'] }}, {{ $platform['lng'] }}, {{ $platform['id'] }})"
                        class="flex w-full items-start gap-3 rounded-lg border border-cms-border-subtle p-3 text-left transition-colors hover:bg-cms-raised"
                    >
                        <span class="mt-0.5 size-2 shrink-0 rounded-full
                            {{ $platform['status'] === 'active' ? 'bg-cms-green' : '' }}
                            {{ $platform['status'] === 'maintenance' ? 'bg-cms-amber' : '' }}
                            {{ $platform['status'] === 'offline' ? 'bg-cms-red' : '' }}
                        "></span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-cms-text">{{ $platform['name'] }}</span>
                            <span class="mt-0.5 block truncate text-xs text-cms-text-3">{{ $platform['description'] }}</span>
                        </span>
                        <flux:icon.map-pin class="size-4 shrink-0 text-cms-text-3" />
                    </button>
                @empty
                    <p class="text-xs text-cms-text-3">No platforms to display.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        window.platformMap = () => ({
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

                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; OpenStreetMap &copy; CARTO',
                        maxZoom: 19,
                    }).addTo(this.map);

                    platforms.forEach((platform) => {
                        if (typeof platform.lat !== 'number' || typeof platform.lng !== 'number') return;

                        const colour =
                            platform.status === 'active' ? '#22c55e' :
                            platform.status === 'maintenance' ? '#f59e0b' :
                            '#ef4444';

                        const marker = L.circleMarker([platform.lat, platform.lng], {
                            radius: 8,
                            fillColor: colour,
                            color: '#ffffff',
                            weight: 1.5,
                            opacity: 1,
                            fillOpacity: 0.9,
                        }).addTo(this.map);

                        marker.bindPopup(
                            `<div class="space-y-1">` +
                                `<p class="font-semibold">${platform.name}</p>` +
                                `<p class="text-xs opacity-70">${platform.description}</p>` +
                                `<p class="text-xs uppercase tracking-wide" style="color:${colour}">${platform.status}</p>` +
                            `</div>`
                        );

                        this.markers[platform.id] = marker;
                    });

                    // Container sits inside a flex column; Leaflet needs a nudge to
                    // pick up the resolved size once layout settles and on resize.
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
@endpush