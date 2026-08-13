<div class="flex-1 overflow-y-auto">
    <header class="sticky top-0 z-10 flex items-center justify-between border-b border-cms-border bg-cms-bg px-8 py-5">
        <div>
            <h1 class="font-cms-serif text-lg font-semibold text-cms-text">Content</h1>
            <p class="mt-0.5 text-sm text-cms-text-3">{{ $this->totalSections }} sections on the landing page</p>
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

    <div class="px-8 py-5">
        {{-- Toolbar --}}
        <div class="mb-5 flex flex-wrap items-center gap-3">
            <div class="relative max-w-xs flex-1">
                <flux:icon.magnifying-glass class="absolute left-3 top-1/2 size-3.5 -translate-y-1/2 text-cms-text-3" />
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search sections…"
                    aria-label="Search sections"
                    class="w-full rounded-lg border border-cms-border bg-cms-surface py-2 pl-9 pr-4 text-sm text-cms-text outline-none placeholder:text-cms-text-3 focus:border-cms-accent"
                >
            </div>

            @foreach ($this->filters as $option)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $option['value'] }}')"
                    @class([
                        'rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors',
                        'border-cms-accent bg-cms-accent-soft text-cms-accent' => $filter === $option['value'],
                        'border-cms-border bg-cms-surface text-cms-text-2 hover:text-cms-text' => $filter !== $option['value'],
                    ])
                >
                    {{ $option['label'] }}
                </button>
            @endforeach

            @if (count($selected) > 0)
                <span class="ml-auto flex items-center gap-3 text-xs text-cms-text-3">
                    {{ count($selected) }} selected
                    <button type="button" wire:click="applyStatus('published')" class="font-medium text-cms-green">Publish</button>
                    <button type="button" wire:click="applyStatus('draft')" class="font-medium text-cms-amber">Unpublish</button>
                </span>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-cms-border bg-cms-surface">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-cms-border">
                        <th class="w-10 px-5 py-3">
                            <input
                                type="checkbox"
                                wire:click="toggleAll"
                                @checked(count($selected) > 0 && count($selected) === $this->sections->count())
                                aria-label="Select all sections"
                                class="rounded accent-cms-accent"
                            >
                        </th>
                        @foreach (['Section', 'Status', 'Items', 'Updated', ''] as $heading)
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-widest text-cms-text-3">
                                {{ $heading }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->sections as $section)
                        <tr wire:key="section-{{ $section->id }}" class="border-b border-cms-border-subtle transition-colors last:border-0 hover:bg-cms-raised">
                            <td class="px-5 py-3">
                                <input
                                    type="checkbox"
                                    value="{{ $section->id }}"
                                    wire:model.live="selected"
                                    aria-label="Select {{ $section->name }}"
                                    class="rounded accent-cms-accent"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('cms.content.edit', $section) }}" wire:navigate class="text-sm font-medium text-cms-text hover:underline">
                                    {{ $section->name }}
                                </a>
                                <p class="mt-0.5 text-xs text-cms-text-3">{{ $section->description }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $section->status->badgeClasses() }}">
                                    {{ $section->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-cms-mono text-xs text-cms-text-2">{{ $section->items_count ?: '—' }}</td>
                            <td class="px-4 py-3 font-cms-mono text-xs text-cms-text-2">{{ $section->updated_at?->format('M j, Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('cms.content.edit', $section) }}" wire:navigate class="rounded px-2 py-1 text-xs font-medium text-cms-accent">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($this->sections->isEmpty())
                <div class="py-16 text-center text-cms-text-3">
                    <p class="text-sm">No sections found</p>
                </div>
            @endif
        </div>
    </div>
</div>
