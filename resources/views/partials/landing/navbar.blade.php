@php
    $navigation = $sections->get('navigation');
    $navLinks = $navigation?->items ?? collect();
    $loginLabel = data_get($settings, 'login_cta_label', 'Masuk Platform');
@endphp

<div x-data="{ menuOpen: false }" @keydown.escape.window="menuOpen = false">
    <nav class="fixed inset-x-0 top-0 z-50 flex items-center justify-between bg-tani-night/95 px-6 py-4 backdrop-blur-md md:px-10">
        <a href="#beranda" class="flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-full border-2 border-tani-gold">
                <flux:icon.leaf class="size-[15px] text-tani-gold" />
            </span>
            <span class="text-[11px] font-extrabold uppercase leading-tight tracking-wider text-white">
                @foreach (str($siteName)->explode(' ')->take(2) as $word)
                    <span class="block">{{ $word }}</span>
                @endforeach
            </span>
        </a>

        <div class="hidden items-center gap-7 text-sm md:flex">
            @foreach ($navLinks as $link)
                <a
                    href="{{ $link->get('href', '#') }}"
                    @class([
                        'transition-colors',
                        'border-b border-white pb-0.5 text-white' => $loop->first,
                        'text-white/65 hover:text-white' => ! $loop->first,
                    ])
                >
                    {{ $link->get('label') }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            @if (Route::has('platform-map.public'))
                <a
                    href="{{ route('platform-map.public') }}"
                    class="hidden items-center gap-2 rounded-full border border-white/20 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:border-white/50 md:flex"
                >
                    <flux:icon.map class="size-[15px]" />
                    Peta
                </a>
            @endif

            @if (Route::has('login'))
                <a
                    href="{{ route('login') }}"
                    class="hidden items-center gap-2 rounded-full bg-tani-green px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-tani-green-hover md:flex"
                >
                    {{ $loginLabel }}
                    <flux:icon.arrow-right class="size-[14px]" />
                </a>
            @endif

            <button
                type="button"
                class="text-white md:hidden"
                x-on:click="menuOpen = ! menuOpen"
                :aria-expanded="menuOpen"
                aria-controls="mobile-menu"
                aria-label="Buka menu navigasi"
            >
                <flux:icon.menu class="size-[22px]" x-show="! menuOpen" />
                <flux:icon.x class="size-[22px]" x-show="menuOpen" x-cloak />
            </button>
        </div>
    </nav>

    <div
        id="mobile-menu"
        x-show="menuOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-40 flex flex-col bg-tani-night px-6 pt-20 md:hidden"
    >
        @foreach ($navLinks as $link)
            <a
                href="{{ $link->get('href', '#') }}"
                x-on:click="menuOpen = false"
                class="border-b border-white/10 py-4 text-lg font-semibold text-white"
            >
                {{ $link->get('label') }}
            </a>
        @endforeach

        @if (Route::has('platform-map.public'))
            <a
                href="{{ route('platform-map.public') }}"
                x-on:click="menuOpen = false"
                class="mt-6 flex w-fit items-center gap-2 rounded-full border border-white/20 px-5 py-3 font-semibold text-white"
            >
                <flux:icon.map class="size-[15px]" />
                Peta
            </a>
        @endif

        @if (Route::has('login'))
            <a
                href="{{ route('login') }}"
                class="mt-6 flex w-fit items-center gap-2 rounded-full bg-tani-green px-5 py-3 font-semibold text-white"
            >
                {{ $loginLabel }}
                <flux:icon.arrow-right class="size-[14px]" />
            </a>
        @endif
    </div>
</div>
