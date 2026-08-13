<div class="flex-1 overflow-y-auto">
    <header class="sticky top-0 z-10 flex items-center justify-between border-b border-cms-border bg-cms-bg px-8 py-5">
        <div>
            <h1 class="font-cms-serif text-lg font-semibold text-cms-text">
                {{ $this->greeting }}, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}
            </h1>
            <p class="mt-0.5 text-sm text-cms-text-3">{{ now()->format('l, F j, Y') }}</p>
        </div>

        <a
            href="{{ route('home') }}"
            target="_blank"
            class="flex items-center gap-2 rounded-lg bg-cms-accent px-4 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90"
        >
            <flux:icon.arrow-up-right class="size-3.5" />
            View site
        </a>
    </header>

    <div class="space-y-6 px-8 py-6">
        {{-- Stat cards --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($this->stats as $stat)
                <div class="rounded-xl border border-cms-border bg-cms-surface p-5">
                    <p class="text-xs font-medium uppercase tracking-widest text-cms-text-3">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-cms-serif text-3xl font-light tracking-tight text-cms-text">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-xs text-cms-text-3">{{ $stat['note'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Chart + activity --}}
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-cms-border bg-cms-surface p-5 lg:col-span-2">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-cms-text">Content Items</h2>
                        <p class="mt-0.5 text-xs text-cms-text-3">Editable rows per section</p>
                    </div>
                    <span class="rounded-md bg-cms-green-soft px-2 py-1 text-xs font-medium text-cms-green">
                        {{ $this->sections->sum('items_count') }} total
                    </span>
                </div>

                <div class="flex h-32 items-end gap-2" x-data="{ hovered: null }">
                    @foreach ($this->sections as $section)
                        <div
                            class="flex flex-1 cursor-default flex-col items-center gap-1"
                            x-on:mouseenter="hovered = {{ $section->id }}"
                            x-on:mouseleave="hovered = null"
                        >
                            <div class="relative flex h-[104px] w-full items-end">
                                <div
                                    x-show="hovered === {{ $section->id }}"
                                    x-cloak
                                    class="absolute -top-7 left-1/2 -translate-x-1/2 whitespace-nowrap rounded border border-cms-border bg-cms-raised px-1.5 py-0.5 text-xs text-cms-text"
                                >
                                    {{ $section->items_count }}
                                </div>
                                <div
                                    class="w-full rounded-t-sm border-t-2 transition-colors"
                                    :class="hovered === {{ $section->id }}
                                        ? 'bg-cms-accent border-cms-accent'
                                        : 'bg-cms-raised border-cms-border'"
                                    style="height: {{ max(4, ($section->items_count / $this->maxItems) * 100) }}%"
                                ></div>
                            </div>
                            <span class="font-cms-mono text-[10px] text-cms-text-3">{{ \Illuminate\Support\Str::limit($section->name, 6, '') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-cms-border bg-cms-surface p-5">
                <h2 class="mb-4 text-sm font-semibold text-cms-text">Recent Activity</h2>

                <div class="space-y-3">
                    @forelse ($this->activity as $entry)
                        <div class="flex gap-3">
                            <span class="mt-1 size-1.5 shrink-0 rounded-full bg-cms-accent"></span>
                            <div>
                                <p class="text-xs leading-snug text-cms-text-2">{{ $entry['event'] }}</p>
                                <p class="mt-0.5 font-cms-mono text-xs text-cms-text-3">{{ $entry['at']?->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-cms-text-3">Nothing has been edited yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sections table --}}
        <div class="rounded-xl border border-cms-border bg-cms-surface">
            <div class="flex items-center justify-between border-b border-cms-border px-5 py-4">
                <h2 class="text-sm font-semibold text-cms-text">Page Sections</h2>
                <a href="{{ route('cms.content.index') }}" wire:navigate class="text-xs font-medium text-cms-accent">
                    View all &rarr;
                </a>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b border-cms-border-subtle">
                        @foreach (['Section', 'Status', 'Items', 'Updated'] as $heading)
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-widest text-cms-text-3">
                                {{ $heading }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->sections as $section)
                        <tr class="border-b border-cms-border-subtle transition-colors last:border-0 hover:bg-cms-raised">
                            <td class="px-5 py-3">
                                <a href="{{ route('cms.content.edit', $section) }}" wire:navigate class="text-sm font-medium text-cms-text hover:underline">
                                    {{ $section->name }}
                                </a>
                                <p class="mt-0.5 text-xs text-cms-text-3">{{ $section->description }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $section->status->badgeClasses() }}">
                                    {{ $section->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-cms-mono text-xs text-cms-text-2">{{ $section->items_count }}</td>
                            <td class="px-5 py-3 font-cms-mono text-xs text-cms-text-2">{{ $section->updated_at?->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
