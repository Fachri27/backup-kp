@php
    $hero = $sections->get('hero');

    $socials = [
        'instagram' => ['Instagram', data_get($settings, 'instagram_url', '#kontak')],
        'twitter' => ['Twitter', data_get($settings, 'twitter_url', '#kontak')],
        'youtube' => ['YouTube', data_get($settings, 'youtube_url', '#kontak')],
    ];
@endphp

@if ($hero)
    <section id="beranda" class="relative flex min-h-screen items-center overflow-hidden">
        <div class="absolute inset-0">
            <img
                src="{{ asset($hero->copy('image', 'images/landing/hero.jpg')) }}"
                alt="{{ $hero->copy('image_alt', '') }}"
                class="size-full object-cover object-center"
            >
            <div class="absolute inset-0 bg-gradient-to-br from-tani-night/90 via-tani-night/65 to-tani-night/80"></div>
        </div>

        {{-- Social rail --}}
        <div class="absolute right-5 top-1/2 z-10 hidden -translate-y-1/2 flex-col items-center gap-4 md:flex">
            @foreach ($socials as $icon => [$label, $href])
                <a
                    href="{{ $href }}"
                    class="flex size-8 items-center justify-center text-white/50 transition-colors hover:text-white"
                    aria-label="{{ $label }}"
                >
                    <flux:icon :icon="$icon" class="size-[15px]" />
                </a>
            @endforeach

            <span class="mt-1 h-12 w-px bg-white/20"></span>
            <span class="mt-2 text-[9px] tracking-[0.25em] text-white/40 [writing-mode:vertical-lr]">SCROLL</span>
        </div>

        <div class="relative z-10 mx-auto grid w-full max-w-7xl items-center gap-12 px-6 pb-16 pt-28 md:grid-cols-2 md:px-10">
            {{-- Left: headline --}}
            <div>
                <div class="mb-7 flex items-center gap-2 text-xs font-medium text-white/60">
                    @foreach ($hero->copy('eyebrow', []) as $word)
                        <span class="size-1.5 rounded-full bg-tani-gold"></span>
                        <span>{{ $word }}</span>
                    @endforeach
                </div>

                <h1
                    class="mb-7 font-black uppercase leading-[0.88] tracking-tight text-white"
                    style="font-size: clamp(3.6rem, 11vw, 8.5rem)"
                >
                    <span class="block">{{ $hero->copy('title_line_1') }}</span>
                    <span class="block text-tani-gold">{{ $hero->copy('title_line_2') }}</span>
                </h1>

                <p class="mb-9 max-w-sm text-base leading-relaxed text-white/65 md:text-lg">
                    {{ $hero->copy('description') }}
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a
                        href="{{ $hero->copy('primary_cta_href', '#') }}"
                        class="flex items-center gap-2 rounded-full bg-tani-green px-6 py-3.5 text-sm font-bold text-white transition-colors hover:bg-tani-green-hover"
                    >
                        {{ $hero->copy('primary_cta_label') }}
                        <flux:icon.arrow-right class="size-[15px]" />
                    </a>

                    <a href="{{ $hero->copy('secondary_cta_href', '#') }}" class="flex items-center gap-3 text-white/75 transition-colors hover:text-white">
                        <span class="flex size-10 items-center justify-center rounded-full border border-white/35">
                            <flux:icon.play class="ml-0.5 size-[13px] fill-current" />
                        </span>
                        <span class="text-sm font-medium">{{ $hero->copy('secondary_cta_label') }}</span>
                    </a>
                </div>

                <div class="mt-10 flex items-center gap-2.5 text-[11px] font-medium tracking-widest text-white/35">
                    <span class="flex size-5 items-center justify-center rounded-full border border-white/25">
                        <span class="h-2.5 w-1 rounded-full border border-white/35"></span>
                    </span>
                    {{ $hero->copy('scroll_label') }}
                </div>
            </div>

            {{-- Right: stat cards --}}
            <div class="flex flex-col gap-3.5 md:items-end">
                @foreach ($hero->items as $card)
                    <div class="w-full max-w-[290px] rounded-2xl bg-white/95 p-5 shadow-xl backdrop-blur">
                        <div class="flex items-start gap-3.5">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $card->get('tint', 'bg-tani-mint') }}">
                                <flux:icon :icon="$card->get('icon', 'leaf')" class="size-[19px] {{ $card->get('icon_color', 'text-tani-green') }}" />
                            </span>

                            <div class="flex-1">
                                <div class="text-[1.6rem] font-black leading-tight text-tani-ink">{{ $card->get('value') }}</div>

                                @foreach ($card->get('lines', []) as $line)
                                    <div @class([
                                        'mt-0.5',
                                        'text-[11px] text-gray-400' => data_get($line, 'style') === 'muted',
                                        'text-xs font-bold text-tani-green' => data_get($line, 'style') === 'accent',
                                    ])>
                                        {{ data_get($line, 'text') }}
                                    </div>
                                @endforeach

                                @if ($card->get('bars'))
                                    <div class="mt-2.5 flex h-7 items-end gap-[2px]">
                                        @foreach ($hero->copy('trend_bars', []) as $index => $height)
                                            <span
                                                class="flex-1 rounded-[2px] bg-tani-gold"
                                                style="height: {{ $height * 2.2 }}px; opacity: {{ 0.75 + $index * 0.02 }}"
                                            ></span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
