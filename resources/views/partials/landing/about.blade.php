@php
    $about = $sections->get('about');
@endphp

@if ($about)
    <section id="tentang" class="bg-tani-cream py-20 md:py-28">
        <div class="mx-auto grid max-w-7xl items-start gap-12 px-6 md:grid-cols-[320px_1fr] md:px-10">
            <div>
                <div class="mb-4 text-[11px] font-bold uppercase tracking-[0.18em] text-tani-green">
                    {{ $about->copy('eyebrow') }}
                </div>

                <h2 class="mb-5 text-[2.6rem] font-black leading-[1.1] text-tani-ink">
                    {{ $about->copy('heading_line_1') }}<br>{{ $about->copy('heading_line_2') }}
                </h2>

                <div class="mb-5 h-[3px] w-10 rounded bg-tani-gold"></div>

                <p class="mb-8 text-sm leading-relaxed text-gray-600">{{ $about->copy('body') }}</p>

                <a
                    href="{{ $about->copy('cta_href', '#') }}"
                    class="flex w-fit items-center gap-2 rounded-full bg-tani-gold px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-tani-gold-hover"
                >
                    {{ $about->copy('cta_label') }}
                    <flux:icon.arrow-right class="size-[14px]" />
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($about->items as $feature)
                    <article class="group flex cursor-pointer flex-col overflow-hidden rounded-2xl bg-white shadow-sm">
                        <div class="relative h-40 overflow-hidden">
                            <img
                                src="{{ asset($feature->get('image', 'images/landing/feature-1.jpg')) }}"
                                alt="{{ $feature->get('title') }}"
                                class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                            >
                            <div class="absolute inset-0 bg-black/25"></div>
                            <span class="absolute bottom-3 left-3 flex size-8 items-center justify-center rounded-full {{ $feature->get('accent', 'bg-tani-moss') }}">
                                <flux:icon :icon="$feature->get('icon', 'leaf')" class="size-[14px] text-white" />
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="mb-1 text-sm font-bold text-tani-ink">{{ $feature->get('title') }}</h3>
                            <p class="flex-1 text-[11px] leading-relaxed text-gray-400">{{ $feature->get('description') }}</p>
                            <div class="mt-3 font-mono text-[11px] font-bold text-gray-200">{{ $feature->get('number') }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
