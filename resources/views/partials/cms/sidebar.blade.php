@php
    $navigation = [
        [
            'label' => 'Dashboard',
            'icon' => 'squares-2x2',
            'route' => 'cms.dashboard',
            'pattern' => 'cms.dashboard',
        ],
        [
            'label' => 'Content',
            'icon' => 'document-text',
            'route' => 'cms.content.index',
            'pattern' => 'cms.content.*',
            'badge' => \App\Models\PageSection::query()->count(),
        ],
        [
            'label' => 'Media',
            'icon' => 'photo',
            'route' => 'cms.media.index',
            'pattern' => 'cms.media.*',
        ],
        [
            'label' => 'Platform Map',
            'icon' => 'map',
            'route' => 'cms.platform-map.index',
            'pattern' => 'cms.platform-map.*',
        ],
        [
            'label' => 'Settings',
            'icon' => 'cog',
            'route' => 'cms.settings.index',
            'pattern' => 'cms.settings.*',
        ],
    ];
@endphp

<aside class="flex h-full w-56 shrink-0 flex-col border-r border-cms-border bg-cms-surface">
    <div class="border-b border-cms-border px-5 py-5">
        <a href="{{ route('cms.dashboard') }}" wire:navigate class="flex items-center gap-2.5">
            <span class="flex size-7 items-center justify-center rounded-md bg-cms-accent text-xs font-semibold text-white">
                {{ \Illuminate\Support\Str::substr($siteName, 0, 1) }}
            </span>
            <span class="text-sm font-semibold tracking-tight text-cms-text">ContentOS</span>
        </a>
    </div>

    <nav class="flex-1 space-y-0.5 px-3 py-4">
        @foreach ($navigation as $item)
            @php $isCurrent = request()->routeIs($item['pattern']); @endphp

            <a
                href="{{ route($item['route']) }}"
                wire:navigate
                @class([
                    'group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                    'bg-cms-accent-soft text-cms-accent' => $isCurrent,
                    'text-cms-text-2 hover:bg-cms-raised hover:text-cms-text' => ! $isCurrent,
                ])
                @if ($isCurrent) aria-current="page" @endif
            >
                <flux:icon :icon="$item['icon']" class="size-4" />
                <span class="flex-1">{{ $item['label'] }}</span>

                @isset($item['badge'])
                    <span class="rounded-full bg-cms-accent-soft px-1.5 py-0.5 text-xs font-semibold text-cms-accent">
                        {{ $item['badge'] }}
                    </span>
                @endisset
            </a>
        @endforeach
    </nav>

    <div class="px-3 pb-4" x-data="{ open: false }" @click.outside="open = false">
        <div class="relative">
            <div
                x-show="open"
                x-cloak
                x-transition.opacity
                class="absolute bottom-full left-0 mb-2 w-full overflow-hidden rounded-lg border border-cms-border bg-cms-raised shadow-xl"
            >
                <a href="{{ route('home') }}" target="_blank" class="block px-3 py-2 text-xs text-cms-text-2 transition-colors hover:bg-cms-surface hover:text-cms-text">
                    View site
                </a>
                <a href="{{ route('profile.edit') }}" wire:navigate class="block px-3 py-2 text-xs text-cms-text-2 transition-colors hover:bg-cms-surface hover:text-cms-text">
                    Account settings
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full px-3 py-2 text-left text-xs text-cms-red transition-colors hover:bg-cms-surface">
                        Log out
                    </button>
                </form>
            </div>

            <button
                type="button"
                x-on:click="open = ! open"
                class="flex w-full items-center gap-3 rounded-lg bg-cms-raised px-3 py-2.5 text-left transition-colors hover:bg-cms-border"
                :aria-expanded="open"
            >
                <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#6c6fff] to-[#a78bfa] text-xs font-semibold text-white">
                    {{ auth()->user()->initials() }}
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-xs font-medium text-cms-text">{{ auth()->user()->name }}</span>
                    <span class="block truncate text-xs text-cms-text-3">Admin</span>
                </span>
                <flux:icon.chevron-down class="size-3.5 text-cms-text-3" />
            </button>
        </div>
    </div>
</aside>
