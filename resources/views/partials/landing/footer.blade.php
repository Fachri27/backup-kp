@php
    $footer = $sections->get('footer');

    $socials = [
        'instagram' => ['Instagram', data_get($settings, 'instagram_url', '#kontak')],
        'twitter' => ['Twitter', data_get($settings, 'twitter_url', '#kontak')],
        'youtube' => ['YouTube', data_get($settings, 'youtube_url', '#kontak')],
    ];
@endphp

<footer id="kontak" class="bg-tani-night px-6 py-12 md:px-10">
    <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 md:flex-row">
        <div>
            <div class="mb-4 flex items-center gap-2.5">
                <span class="flex size-9 items-center justify-center rounded-full border-2 border-tani-gold">
                    <flux:icon.leaf class="size-[15px] text-tani-gold" />
                </span>
                <span class="text-[11px] font-extrabold uppercase leading-tight tracking-wider text-white">
                    @foreach (str($siteName)->explode(' ')->take(2) as $word)
                        <span class="block">{{ $word }}</span>
                    @endforeach
                </span>
            </div>

            <p class="max-w-xs text-xs leading-relaxed text-white/40">{{ $footer?->copy('blurb') }}</p>
        </div>

        <div class="flex flex-col gap-4 md:items-end">
            <div class="flex gap-4">
                @foreach ($socials as $icon => [$label, $href])
                    <a
                        href="{{ $href }}"
                        class="flex size-9 items-center justify-center rounded-full border border-white/15 text-white/40 transition-colors hover:border-white/40 hover:text-white"
                        aria-label="{{ $label }}"
                    >
                        <flux:icon :icon="$icon" class="size-[15px]" />
                    </a>
                @endforeach
            </div>

            <div class="text-xs text-white/25">
                &copy; {{ now()->year }} {{ $footer?->copy('copyright') }}
            </div>
        </div>
    </div>
</footer>
