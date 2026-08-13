<?php

namespace App\Livewire\Cms;

use App\Models\Media;
use App\Models\PageSection;
use App\Models\SectionItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Livewire exposes #[Computed] methods as properties at runtime.
 *
 * @property-read array<int, array{label: string, value: string, note: string}> $stats
 * @property-read Collection<int, PageSection> $sections
 * @property-read int $maxItems
 * @property-read Collection<int, array{event: string, at: Carbon}> $activity
 * @property-read string $greeting
 */
#[Layout('layouts::cms')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * Headline counts across the managed content.
     *
     * @return array<int, array{label: string, value: string, note: string}>
     */
    #[Computed]
    public function stats(): array
    {
        $sections = PageSection::query()->count();
        $published = PageSection::query()->published()->count();
        $items = SectionItem::query()->count();
        $media = Media::query()->count();

        return [
            ['label' => 'Sections', 'value' => (string) $sections, 'note' => 'on the landing page'],
            ['label' => 'Published', 'value' => (string) $published, 'note' => 'live right now'],
            ['label' => 'Content Items', 'value' => (string) $items, 'note' => 'cards, stats and links'],
            ['label' => 'Media Files', 'value' => (string) $media, 'note' => 'in the library'],
        ];
    }

    /**
     * Sections with their item counts - drives both the chart and the table.
     *
     * @return Collection<int, PageSection>
     */
    #[Computed]
    public function sections(): Collection
    {
        return PageSection::query()
            ->withCount('items')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * The tallest bar in the chart, used to scale the rest.
     */
    #[Computed]
    public function maxItems(): int
    {
        return max(1, (int) $this->sections->max('items_count'));
    }

    /**
     * A merged feed of recent content and media changes.
     *
     * @return Collection<int, array{event: string, at: Carbon}>
     */
    #[Computed]
    public function activity(): Collection
    {
        $sections = PageSection::query()
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (PageSection $section): array => [
                'event' => $section->name.' section updated',
                'at' => $section->updated_at,
            ]);

        $media = Media::query()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Media $item): array => [
                'event' => $item->name.' uploaded to media',
                'at' => $item->created_at,
            ]);

        return $sections
            ->concat($media)
            ->sortByDesc('at')
            ->take(5)
            ->values();
    }

    /**
     * Time-of-day greeting for the header, matching the design's "Good morning".
     */
    #[Computed]
    public function greeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };
    }
}
