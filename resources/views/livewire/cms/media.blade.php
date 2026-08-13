<div class="flex flex-1 flex-col overflow-hidden">
    <header class="flex shrink-0 items-center justify-between border-b border-cms-border bg-cms-bg px-8 py-5">
        <div>
            <h1 class="font-cms-serif text-lg font-semibold text-cms-text">Media Library</h1>
            <p class="mt-0.5 text-sm text-cms-text-3">
                {{ $this->files->count() }} {{ Str::plural('file', $this->files->count()) }} &middot; {{ $this->usedSpace }} used
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex overflow-hidden rounded-lg border border-cms-border">
                @foreach (['grid' => 'Grid', 'list' => 'List'] as $mode => $label)
                    <button
                        type="button"
                        wire:click="$set('view', '{{ $mode }}')"
                        @class([
                            'px-3 py-1.5 text-xs transition-colors',
                            'bg-cms-accent-soft text-cms-accent' => $view === $mode,
                            'bg-cms-surface text-cms-text-2' => $view !== $mode,
                        ])
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <label class="flex cursor-pointer items-center gap-2 rounded-lg bg-cms-accent px-4 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90">
                <flux:icon.arrow-up-tray class="size-3.5" />
                Upload
                <input type="file" wire:model="uploads" multiple accept="image/*" class="hidden">
            </label>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Drop zone --}}
            <label
                x-data="{ dragging: false }"
                x-on:dragover.prevent="dragging = true"
                x-on:dragleave.prevent="dragging = false"
                x-on:drop="dragging = false"
                :class="dragging ? 'border-cms-accent bg-cms-accent-soft' : 'border-cms-border'"
                class="mb-5 flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 border-dashed py-6 transition-colors"
            >
                <flux:icon.arrow-up-tray class="size-6 text-cms-text-3" />
                <p class="text-sm text-cms-text-3">
                    Drop files here or <span class="text-cms-accent">browse</span>
                </p>
                <input type="file" wire:model="uploads" multiple accept="image/*" class="hidden">

                <p wire:loading wire:target="uploads" class="font-cms-mono text-xs text-cms-accent">Uploading…</p>
            </label>

            @error('uploads.*')
                <p class="mb-4 rounded-lg bg-cms-red-soft px-3 py-2 text-xs text-cms-red">{{ $message }}</p>
            @enderror

            @if ($this->files->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-sm text-cms-text-3">No files yet. Upload an image to get started.</p>
                </div>
            @elseif ($view === 'grid')
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($this->files as $file)
                        <button
                            type="button"
                            wire:key="media-{{ $file->id }}"
                            wire:click="select({{ $file->id }})"
                            @class([
                                'overflow-hidden rounded-xl border bg-cms-surface text-left transition-colors',
                                'border-cms-accent outline outline-2 outline-offset-1 outline-cms-accent' => $selectedId === $file->id,
                                'border-cms-border' => $selectedId !== $file->id,
                            ])
                        >
                            <div class="aspect-video overflow-hidden bg-cms-raised">
                                <img src="{{ $file->url }}" alt="{{ $file->name }}" class="size-full object-cover">
                            </div>
                            <div class="px-3 py-2">
                                <p class="truncate text-xs font-medium text-cms-text">{{ $file->name }}</p>
                                <p class="mt-0.5 font-cms-mono text-xs text-cms-text-3">{{ $file->human_size }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="overflow-hidden rounded-xl border border-cms-border bg-cms-surface">
                    @foreach ($this->files as $file)
                        <button
                            type="button"
                            wire:key="media-row-{{ $file->id }}"
                            wire:click="select({{ $file->id }})"
                            @class([
                                'flex w-full items-center gap-4 border-b border-cms-border-subtle px-5 py-3 text-left transition-colors last:border-0',
                                'bg-cms-accent-soft' => $selectedId === $file->id,
                            ])
                        >
                            <span class="h-9 w-12 shrink-0 overflow-hidden rounded bg-cms-raised">
                                <img src="{{ $file->url }}" alt="{{ $file->name }}" class="size-full object-cover">
                            </span>
                            <span class="flex-1 truncate text-sm font-medium text-cms-text">{{ $file->name }}</span>
                            <span class="font-cms-mono text-xs text-cms-text-3">{{ $file->dimensions() ?? '—' }}</span>
                            <span class="w-16 text-right font-cms-mono text-xs text-cms-text-3">{{ $file->human_size }}</span>
                            <span class="w-12 text-right text-xs text-cms-text-3">{{ $file->created_at?->format('M j') }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Detail panel --}}
        @if ($this->selected)
            <aside class="w-60 shrink-0 overflow-y-auto border-l border-cms-border bg-cms-surface">
                <div class="space-y-4 p-4">
                    <div class="aspect-video overflow-hidden rounded-lg bg-cms-raised">
                        <img src="{{ $this->selected->url }}" alt="{{ $this->selected->name }}" class="size-full object-cover">
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-cms-text">{{ $this->selected->name }}</p>

                        <div class="mt-3 space-y-2">
                            @foreach ([
                                'Type' => $this->selected->mime_type,
                                'Size' => $this->selected->human_size,
                                'Dimensions' => $this->selected->dimensions() ?? '—',
                                'Uploaded' => $this->selected->created_at?->format('M j, Y'),
                            ] as $label => $value)
                                <div class="flex justify-between gap-2 text-xs">
                                    <span class="shrink-0 text-cms-text-3">{{ $label }}</span>
                                    <span class="truncate font-cms-mono text-cms-text-2">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2" x-data="{ copied: false }">
                        <button
                            type="button"
                            x-on:click="navigator.clipboard.writeText('{{ $this->selected->url }}'); copied = true; setTimeout(() => copied = false, 1500)"
                            class="w-full rounded-lg border border-cms-border py-1.5 text-xs font-medium text-cms-text-2 transition-colors hover:text-cms-text"
                        >
                            <span x-show="! copied">Copy URL</span>
                            <span x-show="copied" x-cloak class="text-cms-green">Copied</span>
                        </button>

                        <button
                            type="button"
                            wire:click="delete({{ $this->selected->id }})"
                            wire:confirm="Delete this file permanently?"
                            class="w-full rounded-lg py-1.5 text-xs font-medium text-cms-red transition-colors hover:bg-cms-red-soft"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </aside>
        @endif
    </div>
</div>
