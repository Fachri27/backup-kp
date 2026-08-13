@php
    $stats = $sections->get('stats');
@endphp

@if ($stats)
    <section id="data" class="bg-tani-forest py-8">
        <div class="mx-auto max-w-7xl px-6 md:px-10">
            <div class="grid grid-cols-2 md:grid-cols-5">
                <div class="flex flex-col justify-center px-4 py-4 md:px-6">
                    <div class="text-[11px] font-black uppercase leading-tight tracking-[0.16em] text-tani-gold">
                        {{ $stats->copy('label_line_1') }}<br>{{ $stats->copy('label_line_2') }}
                    </div>
                    <div class="mt-3 h-[2px] w-8 rounded bg-tani-gold"></div>
                </div>

                @foreach ($stats->items as $stat)
                    <div class="flex items-center gap-3 border-l border-white/10 px-4 py-5 md:px-6">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/10">
                            <flux:icon :icon="$stat->get('icon', 'leaf')" class="size-[18px] text-tani-gold" />
                        </span>
                        <div>
                            <div class="text-xl font-black leading-tight text-white md:text-2xl">{{ $stat->get('value') }}</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-tani-gold">{{ $stat->get('label') }}</div>
                            <div class="mt-0.5 text-[10px] text-white/40">{{ $stat->get('sub') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
