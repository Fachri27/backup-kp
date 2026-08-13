@php
    $dashboard = $sections->get('dashboard');

    /*
     * The sidebar, filter panel and map controls below are chrome for the product
     * mockup rather than site content, so they stay in the template. The editable
     * parts - header copy, the province detail card and the figures strip - come
     * from the CMS.
     */
    $sidebarItems = [
        ['icon' => 'house', 'label' => 'Beranda'],
        ['icon' => 'map', 'label' => 'Peta Interaktif'],
        ['icon' => 'chart-column-increasing', 'label' => 'Statistik'],
        ['icon' => 'trending-up', 'label' => 'Perbandingan'],
        ['icon' => 'download', 'label' => 'Unduh Data'],
        ['icon' => 'info', 'label' => 'Tentang Data'],
    ];

    $mapTypes = ['Peta', 'Satelit', 'Terrain'];

    $mapLayers = ['Pertanian Lainnya', 'Batas Administrasi', 'Citra Satelit', 'Topografi'];
@endphp

@if ($dashboard)
    <section id="fitur" class="bg-tani-forest py-16">
        <div class="mx-auto max-w-7xl px-6 md:px-10">
            <div
                x-data="{
                    activeNav: @js($dashboard->copy('title')),
                    mapType: 'Peta',
                    year: 2024,
                    layers: {
                        'Pertanian Lainnya': true,
                        'Batas Administrasi': true,
                        'Citra Satelit': false,
                        'Topografi': false,
                    },
                    get yearOffset() {
                        return ((this.year - 2021) / 4) * 100;
                    },
                }"
                class="flex min-h-[500px] overflow-hidden rounded-3xl bg-white shadow-2xl"
            >
                {{-- Sidebar --}}
                <div class="hidden w-48 shrink-0 flex-col bg-tani-forest lg:flex">
                    <div class="flex items-center gap-2 border-b border-white/10 px-4 py-5">
                        <span class="flex size-7 items-center justify-center rounded-full border border-tani-gold">
                            <flux:icon.leaf class="size-[11px] text-tani-gold" />
                        </span>
                        <span class="text-[9px] font-extrabold uppercase leading-tight tracking-wider text-white">
                            @foreach (str($siteName)->explode(' ')->take(2) as $word)
                                <span class="block">{{ $word }}</span>
                            @endforeach
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col gap-0.5 p-3">
                        @foreach ($sidebarItems as $item)
                            <button
                                type="button"
                                x-on:click="activeNav = @js($item['label'])"
                                :class="activeNav === @js($item['label'])
                                    ? 'bg-tani-green text-white'
                                    : 'text-white/50 hover:bg-white/10 hover:text-white'"
                                class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-[11px] font-semibold transition-colors"
                            >
                                <flux:icon :icon="$item['icon']" class="size-[13px]" />
                                {{ $item['label'] }}
                            </button>
                        @endforeach
                    </div>

                    <div class="p-3">
                        <div class="rounded-xl bg-tani-night p-3 text-center">
                            <div class="text-[10px] font-medium text-white/50">{{ $dashboard->copy('help_title') }}</div>
                            <div class="mt-0.5 text-[10px] font-bold text-tani-gold">{{ $dashboard->copy('help_link') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Main --}}
                <div class="flex min-w-0 flex-1 flex-col">
                    <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-3.5">
                        <div>
                            <div class="text-sm font-bold text-tani-ink" x-text="activeNav">{{ $dashboard->copy('title') }}</div>
                            <div class="text-[11px] text-gray-400">{{ $dashboard->copy('subtitle') }}</div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <div class="flex items-center gap-2 rounded-full bg-gray-100 px-3.5 py-1.5 text-[11px] text-gray-400">
                                <flux:icon.search class="size-[11px]" />
                                <span class="hidden sm:inline">{{ $dashboard->copy('search_placeholder') }}</span>
                            </div>
                            <span class="flex size-8 items-center justify-center rounded-full bg-gray-100">
                                <flux:icon.bell class="size-[13px] text-gray-500" />
                            </span>
                            <span class="size-8 rounded-full bg-tani-green"></span>
                        </div>
                    </div>

                    <div class="flex min-h-0 flex-1">
                        {{-- Filter panel --}}
                        <div class="hidden w-44 shrink-0 overflow-y-auto border-r border-gray-100 p-4 md:block">
                            <div class="mb-3 text-[10px] font-black uppercase tracking-[0.12em] text-tani-ink">
                                Filter Wilayah
                            </div>

                            <span class="mb-1 block text-[10px] text-gray-400">Provinsi</span>
                            <div class="mb-3 flex items-center justify-between rounded-lg bg-gray-100 px-2.5 py-2 text-[11px] text-tani-ink">
                                {{ $dashboard->copy('detail.name') }}
                                <flux:icon.lucide-chevron-down class="size-[10px]" />
                            </div>

                            <span class="mb-1 block text-[10px] text-gray-400">Kabupaten/Kota</span>
                            <div class="mb-3 flex items-center justify-between rounded-lg bg-gray-100 px-2.5 py-2 text-[11px] text-gray-400">
                                Semua Kabupaten
                                <flux:icon.lucide-chevron-down class="size-[10px]" />
                            </div>

                            <span class="mb-1 block text-[10px] text-gray-400">Tahun</span>
                            <div class="mb-1 flex justify-between text-[9px] text-gray-400">
                                <span>2021</span>
                                <span x-text="year">2024</span>
                            </div>
                            <div class="relative mb-4 h-3">
                                <div class="absolute top-1/2 h-1.5 w-full -translate-y-1/2 rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-tani-green" :style="`width: ${yearOffset}%`"></div>
                                </div>
                                <div
                                    class="pointer-events-none absolute top-1/2 size-3 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white bg-tani-green shadow"
                                    :style="`left: ${yearOffset}%`"
                                ></div>
                                <input
                                    type="range"
                                    min="2021"
                                    max="2024"
                                    step="1"
                                    x-model.number="year"
                                    class="absolute inset-0 w-full cursor-pointer opacity-0"
                                    aria-label="Pilih tahun"
                                >
                            </div>

                            <span class="mb-2 block text-[10px] text-gray-400">Kategori</span>
                            <div class="mb-4 flex items-center gap-1.5 text-[11px] text-tani-ink">
                                <span class="size-2.5 rounded-full bg-tani-green"></span>
                                {{ $dashboard->copy('detail.category') }}
                            </div>

                            <div class="mb-2.5 text-[10px] font-black uppercase tracking-[0.12em] text-tani-ink">
                                Layer Peta
                            </div>

                            @foreach ($mapLayers as $layer)
                                <button
                                    type="button"
                                    x-on:click="layers[@js($layer)] = ! layers[@js($layer)]"
                                    class="mb-2 flex w-full items-center gap-1.5 text-left text-[11px] text-gray-600"
                                >
                                    <span
                                        class="flex size-3 shrink-0 items-center justify-center rounded border"
                                        :class="layers[@js($layer)] ? 'border-tani-green bg-tani-green' : 'border-gray-300'"
                                    >
                                        <span
                                            x-show="layers[@js($layer)]"
                                            x-cloak
                                            class="h-1 w-1.5 -translate-y-px rotate-45 border-b-[1.5px] border-r-[1.5px] border-white"
                                        ></span>
                                    </span>
                                    {{ $layer }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Map view --}}
                        <div class="relative min-w-0 flex-1 bg-tani-steel">
                            <img
                                src="{{ asset($dashboard->copy('map_image', 'images/landing/map-bg.jpg')) }}"
                                alt="Citra satelit wilayah Indonesia"
                                class="absolute inset-0 size-full object-cover opacity-45"
                            >

                            <div class="absolute right-3 top-3 z-10 flex gap-1">
                                @foreach ($mapTypes as $type)
                                    <button
                                        type="button"
                                        x-on:click="mapType = @js($type)"
                                        :class="mapType === @js($type)
                                            ? 'bg-white font-bold text-tani-green'
                                            : 'bg-white/20 font-medium text-white'"
                                        class="rounded-lg px-3 py-1.5 text-[11px] transition-colors"
                                    >
                                        {{ $type }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="absolute bottom-20 right-3 z-10 flex flex-col gap-1">
                                <span class="flex size-7 items-center justify-center rounded bg-white text-sm font-bold text-gray-700 shadow">+</span>
                                <span class="flex size-7 items-center justify-center rounded bg-white text-sm font-bold text-gray-700 shadow">&minus;</span>
                            </div>

                            <div class="absolute left-1/4 top-1/4 z-10 w-44 rounded-xl bg-white p-4 shadow-xl">
                                <div class="text-sm font-bold text-tani-ink">{{ $dashboard->copy('detail.name') }}</div>
                                <div class="mb-2 text-[10px] text-gray-400">{{ $dashboard->copy('detail.category') }}</div>
                                <div class="text-2xl font-black leading-tight text-tani-ink">{{ $dashboard->copy('detail.value') }}</div>
                                <div class="mb-1 mt-1 text-[10px] text-gray-400">{{ $dashboard->copy('detail.change_label') }}</div>
                                <div class="text-sm font-bold text-tani-green">{{ $dashboard->copy('detail.change') }}</div>

                                <div class="mt-2.5 flex h-6 items-end gap-[2px]">
                                    @foreach ($dashboard->copy('detail.bars', []) as $index => $height)
                                        <span
                                            class="flex-1 rounded-[2px] bg-tani-gold"
                                            style="height: {{ $height * 2 }}px; opacity: {{ 0.7 + $index * 0.03 }}"
                                        ></span>
                                    @endforeach
                                </div>

                                <span class="mt-2.5 flex items-center gap-1 text-[11px] font-bold text-tani-green">
                                    {{ $dashboard->copy('detail.cta_label') }}
                                    <flux:icon.arrow-right class="size-[10px]" />
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid shrink-0 grid-cols-2 gap-3 border-t border-gray-100 px-5 py-3 md:grid-cols-5">
                        @foreach ($dashboard->items as $stat)
                            <div>
                                <div class="mb-0.5 text-[9px] font-semibold uppercase tracking-wide text-gray-400">
                                    {{ $stat->get('label') }}
                                </div>
                                <div class="text-sm font-black leading-tight text-tani-ink">{{ $stat->get('value') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $stat->get('sub') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
