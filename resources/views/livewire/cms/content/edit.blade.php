<div class="flex flex-1 flex-col overflow-hidden">
    {{-- Top bar --}}
    <header class="flex shrink-0 items-center gap-4 border-b border-cms-border bg-cms-bg px-6 py-3.5">
        <a href="{{ route('cms.content.index') }}" wire:navigate class="flex items-center gap-1.5 text-sm text-cms-text-3 transition-colors hover:text-cms-text">
            <flux:icon.chevron-left class="size-3.5" />
            Content
        </a>

        <span class="h-4 w-px bg-cms-border"></span>

        <span class="flex-1 truncate text-sm font-medium text-cms-text-2">{{ $section->name }}</span>

        <div class="ml-auto flex items-center gap-2">
            <span class="font-cms-mono text-xs text-cms-text-3">{{ count($items) }} rows</span>

            <select
                wire:model="status"
                aria-label="Section status"
                class="rounded-lg border border-cms-border bg-cms-surface px-2 py-1.5 text-xs text-cms-text-2 outline-none"
            >
                @foreach ($this->statuses as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>

            <button
                type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                class="rounded-lg bg-cms-accent px-4 py-1.5 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>

            <button
                type="button"
                wire:click="publish"
                class="rounded-lg border border-transparent bg-cms-green-soft px-4 py-1.5 text-sm font-medium text-cms-green transition-opacity hover:opacity-90"
            >
                Publish
            </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        {{-- Editor --}}
        <div class="flex flex-1 flex-col overflow-y-auto">
            <div class="mx-auto w-full max-w-3xl px-12 py-8">
                <h1 class="mb-1 font-cms-serif text-[32px] leading-tight text-cms-text">{{ $section->name }}</h1>
                <p class="mb-8 text-sm text-cms-text-3">{{ $section->description }}</p>

                {{-- Section copy --}}
                @if (count($fields) > 0)
                    <div class="mb-8 space-y-4">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-cms-text-3">Section copy</h2>

                        @foreach ($fields as $index => $field)
                            <div wire:key="field-{{ $field['path'] }}">
                                <label for="field-{{ $index }}" class="mb-1.5 block text-xs font-medium text-cms-text-2">
                                    {{ $field['label'] }}
                                </label>

                                @if ($field['type'] === 'textarea')
                                    <textarea
                                        id="field-{{ $index }}"
                                        wire:model="fields.{{ $index }}.value"
                                        rows="3"
                                        class="w-full resize-y rounded-lg border border-cms-border bg-cms-surface px-3 py-2 text-sm leading-relaxed text-cms-text outline-none focus:border-cms-accent"
                                    ></textarea>
                                @elseif ($field['type'] === 'bool')
                                    <label class="flex items-center gap-2 text-sm text-cms-text-2">
                                        <input
                                            id="field-{{ $index }}"
                                            type="checkbox"
                                            wire:model="fields.{{ $index }}.value"
                                            class="rounded accent-cms-accent"
                                        >
                                        Enabled
                                    </label>
                                @else
                                    <input
                                        id="field-{{ $index }}"
                                        type="text"
                                        wire:model="fields.{{ $index }}.value"
                                        class="w-full rounded-lg border border-cms-border bg-cms-surface px-3 py-2 text-sm text-cms-text outline-none focus:border-cms-accent"
                                    >
                                    @if (in_array($field['type'], ['list', 'numlist'], true))
                                        <p class="mt-1 text-xs text-cms-text-3">Separate values with commas.</p>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Repeatable rows --}}
                @if (count($items) > 0)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xs font-semibold uppercase tracking-widest text-cms-text-3">
                                {{ $this->itemLabel }}
                            </h2>
                            <button type="button" wire:click="addItem" class="flex items-center gap-1.5 text-xs font-medium text-cms-accent">
                                <flux:icon.plus class="size-3" />
                                Add row
                            </button>
                        </div>

                        @foreach ($items as $itemIndex => $item)
                            <div wire:key="item-{{ $item['id'] ?? $itemIndex }}" class="rounded-xl border border-cms-border bg-cms-surface p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <span class="font-cms-mono text-xs text-cms-text-3">
                                        {{ str_pad((string) ($itemIndex + 1), 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-1.5 text-xs text-cms-text-3">
                                            <input type="checkbox" wire:model="items.{{ $itemIndex }}.is_published" class="rounded accent-cms-accent">
                                            Visible
                                        </label>

                                        <button type="button" wire:click="moveItem({{ $itemIndex }}, -1)" @disabled($loop->first) class="text-xs text-cms-text-3 disabled:opacity-30" aria-label="Move up">&uarr;</button>
                                        <button type="button" wire:click="moveItem({{ $itemIndex }}, 1)" @disabled($loop->last) class="text-xs text-cms-text-3 disabled:opacity-30" aria-label="Move down">&darr;</button>

                                        <button
                                            type="button"
                                            wire:click="removeItem({{ $itemIndex }})"
                                            wire:confirm="Remove this row from the page?"
                                            class="text-xs font-medium text-cms-red"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach ($item['fields'] as $fieldIndex => $field)
                                        <div @class(['sm:col-span-2' => $field['type'] === 'textarea'])>
                                            <label for="item-{{ $itemIndex }}-{{ $fieldIndex }}" class="mb-1 block text-xs font-medium text-cms-text-3">
                                                {{ $field['label'] }}
                                            </label>

                                            @if ($field['type'] === 'textarea')
                                                <textarea
                                                    id="item-{{ $itemIndex }}-{{ $fieldIndex }}"
                                                    wire:model="items.{{ $itemIndex }}.fields.{{ $fieldIndex }}.value"
                                                    rows="2"
                                                    class="w-full resize-y rounded-lg border border-cms-border bg-cms-raised px-2.5 py-1.5 text-xs leading-relaxed text-cms-text outline-none focus:border-cms-accent"
                                                ></textarea>
                                            @elseif ($field['type'] === 'bool')
                                                <label class="flex items-center gap-2 py-1.5 text-xs text-cms-text-2">
                                                    <input
                                                        id="item-{{ $itemIndex }}-{{ $fieldIndex }}"
                                                        type="checkbox"
                                                        wire:model="items.{{ $itemIndex }}.fields.{{ $fieldIndex }}.value"
                                                        class="rounded accent-cms-accent"
                                                    >
                                                    Enabled
                                                </label>
                                            @else
                                                <input
                                                    id="item-{{ $itemIndex }}-{{ $fieldIndex }}"
                                                    type="text"
                                                    wire:model="items.{{ $itemIndex }}.fields.{{ $fieldIndex }}.value"
                                                    class="w-full rounded-lg border border-cms-border bg-cms-raised px-2.5 py-1.5 text-xs text-cms-text outline-none focus:border-cms-accent"
                                                >
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="w-64 shrink-0 overflow-y-auto border-l border-cms-border bg-cms-surface">
            <div class="space-y-5 p-5">
                <div>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-widest text-cms-text-3">Status</span>
                    <div class="flex gap-2">
                        @foreach ($this->statuses as $option)
                            <button
                                type="button"
                                wire:click="$set('status', '{{ $option['value'] }}')"
                                @class([
                                    'flex-1 rounded-lg border py-1.5 text-xs font-medium transition-colors',
                                    'border-cms-accent bg-cms-accent-soft text-cms-accent' => $status === $option['value'],
                                    'border-transparent bg-cms-raised text-cms-text-2' => $status !== $option['value'],
                                ])
                            >
                                {{ $option['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="h-px bg-cms-border"></div>

                <div>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-widest text-cms-text-3">Section</span>
                    <div class="space-y-2">
                        @foreach ([
                            'Key' => $section->key,
                            'Copy fields' => (string) count($fields),
                            'Rows' => (string) count($items),
                            'Updated' => $section->updated_at?->diffForHumans() ?? '—',
                        ] as $label => $value)
                            <div class="flex justify-between text-xs">
                                <span class="text-cms-text-3">{{ $label }}</span>
                                <span class="font-cms-mono text-cms-text-2">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="h-px bg-cms-border"></div>

                <div>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-widest text-cms-text-3">Preview</span>
                    <a
                        href="{{ route('home') }}#{{ $section->key }}"
                        target="_blank"
                        class="flex items-center justify-center gap-1.5 rounded-lg border border-cms-border bg-cms-raised py-2 text-xs font-medium text-cms-text-2 transition-colors hover:text-cms-text"
                    >
                        <flux:icon.arrow-up-right class="size-3" />
                        Open on site
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
