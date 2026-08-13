@php
    $fieldClasses = 'w-56 rounded-lg border border-cms-border bg-cms-raised px-3 py-1.5 text-sm text-cms-text outline-none focus:border-cms-accent';
@endphp

<div class="flex-1 overflow-y-auto">
    <header class="sticky top-0 z-10 flex items-center justify-between border-b border-cms-border bg-cms-bg px-8 py-5">
        <h1 class="font-cms-serif text-lg font-semibold text-cms-text">Settings</h1>

        @if ($tab !== 'team')
            <button
                type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                class="rounded-lg bg-cms-accent px-4 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        @endif
    </header>

    <div class="flex flex-1">
        <div class="w-44 shrink-0 space-y-0.5 border-r border-cms-border px-3 py-4">
            @foreach ($this->tabs as $option)
                <button
                    type="button"
                    wire:click="$set('tab', '{{ $option['value'] }}')"
                    @class([
                        'w-full rounded-lg px-3 py-2 text-left text-sm transition-colors',
                        'bg-cms-accent-soft font-medium text-cms-accent' => $tab === $option['value'],
                        'text-cms-text-2 hover:text-cms-text' => $tab !== $option['value'],
                    ])
                >
                    {{ $option['label'] }}
                </button>
            @endforeach
        </div>

        <div class="max-w-2xl flex-1 space-y-4 px-8 py-6">
            @if ($tab === 'general')
                <section class="rounded-xl border border-cms-border bg-cms-surface p-6">
                    <h2 class="mb-5 text-sm font-semibold text-cms-text">Site Identity</h2>

                    @foreach ([
                        'site_name' => ['Site Name', 'Appears in the browser tab and the CMS header'],
                        'tagline' => ['Tagline', 'Shown alongside the site name'],
                        'meta_description' => ['Meta Description', 'Used as the landing page description tag'],
                        'login_cta_label' => ['Login Button Label', 'The header call-to-action on the public site'],
                    ] as $key => [$label, $hint])
                        <div class="flex items-start justify-between gap-8 border-b border-cms-border-subtle py-3 last:border-0">
                            <div class="min-w-0">
                                <label for="general-{{ $key }}" class="text-sm font-medium text-cms-text">{{ $label }}</label>
                                <p class="mt-0.5 text-xs text-cms-text-3">{{ $hint }}</p>
                                @error("general.$key")
                                    <p class="mt-1 text-xs text-cms-red">{{ $message }}</p>
                                @enderror
                            </div>
                            <input id="general-{{ $key }}" type="text" wire:model="general.{{ $key }}" class="{{ $fieldClasses }} shrink-0">
                        </div>
                    @endforeach
                </section>
            @endif

            @if ($tab === 'seo')
                <section class="rounded-xl border border-cms-border bg-cms-surface p-6">
                    <h2 class="mb-5 text-sm font-semibold text-cms-text">Default SEO</h2>

                    <div class="flex items-start justify-between gap-8 py-3">
                        <div class="min-w-0">
                            <label for="seo-template" class="text-sm font-medium text-cms-text">Meta title template</label>
                            <p class="mt-0.5 text-xs text-cms-text-3">Use <code class="font-cms-mono">:title</code> and <code class="font-cms-mono">:site</code> as placeholders</p>
                            @error('seo.meta_title_template')
                                <p class="mt-1 text-xs text-cms-red">{{ $message }}</p>
                            @enderror
                        </div>
                        <input id="seo-template" type="text" wire:model="seo.meta_title_template" class="{{ $fieldClasses }} shrink-0">
                    </div>

                    <div class="flex items-start justify-between gap-8 border-t border-cms-border-subtle py-3">
                        <div class="min-w-0">
                            <span class="text-sm font-medium text-cms-text">Meta description</span>
                            <p class="mt-0.5 text-xs text-cms-text-3">Edit this under General &mdash; it is shared with the landing page</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-cms-green-soft px-2 py-0.5 text-xs text-cms-green">In use</span>
                    </div>
                </section>
            @endif

            @if ($tab === 'social')
                <section class="rounded-xl border border-cms-border bg-cms-surface p-6">
                    <h2 class="mb-5 text-sm font-semibold text-cms-text">Social Links</h2>

                    @foreach ([
                        'instagram_url' => 'Instagram',
                        'twitter_url' => 'Twitter',
                        'youtube_url' => 'YouTube',
                    ] as $key => $label)
                        <div class="flex items-start justify-between gap-8 border-b border-cms-border-subtle py-3 last:border-0">
                            <div class="min-w-0">
                                <label for="social-{{ $key }}" class="text-sm font-medium text-cms-text">{{ $label }}</label>
                                <p class="mt-0.5 text-xs text-cms-text-3">Linked from the hero rail and the footer</p>
                                @error("social.$key")
                                    <p class="mt-1 text-xs text-cms-red">{{ $message }}</p>
                                @enderror
                            </div>
                            <input id="social-{{ $key }}" type="text" wire:model="social.{{ $key }}" class="{{ $fieldClasses }} shrink-0">
                        </div>
                    @endforeach
                </section>
            @endif

            @if ($tab === 'team')
                <section class="rounded-xl border border-cms-border bg-cms-surface p-6">
                    <h2 class="mb-5 text-sm font-semibold text-cms-text">Team Members</h2>

                    <div class="space-y-1">
                        @foreach ($this->team as $member)
                            <div class="flex items-center gap-4 border-b border-cms-border-subtle py-3 last:border-0">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-cms-accent-soft text-xs font-semibold text-cms-accent">
                                    {{ $member->initials() }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-cms-text">{{ $member->name }}</p>
                                    <p class="truncate text-xs text-cms-text-3">{{ $member->email }}</p>
                                </div>
                                <span class="font-cms-mono text-xs text-cms-text-3">
                                    {{ $member->created_at?->format('M Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <p class="mt-4 text-xs text-cms-text-3">
                        Every authenticated user has full CMS access. Roles were deliberately left out &mdash;
                        ask if you want Admin/Editor/Author permissions added.
                    </p>

                    <a href="{{ route('profile.edit') }}" wire:navigate class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-cms-accent">
                        Manage your account
                    </a>
                </section>
            @endif
        </div>
    </div>
</div>
