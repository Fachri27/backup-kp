<?php

namespace App\Livewire\Cms\Content;

use App\Enums\SectionStatus;
use App\Models\PageSection;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Livewire exposes #[Computed] methods as properties at runtime.
 *
 * @property-read Collection<int, PageSection> $sections
 * @property-read int $totalSections
 * @property-read array<int, array{value: string, label: string}> $filters
 */
#[Layout('layouts::cms')]
#[Title('Content')]
class Index extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'all')]
    public string $filter = 'all';

    /** @var array<int, int> */
    public array $selected = [];

    /**
     * @return Collection<int, PageSection>
     */
    #[Computed]
    public function sections(): Collection
    {
        return PageSection::query()
            ->withCount('items')
            ->when($this->search !== '', fn ($query) => $query->where(
                fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhere('key', 'like', '%'.$this->search.'%'),
            ))
            ->when($this->filter !== 'all', fn ($query) => $query->where('status', $this->filter))
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function totalSections(): int
    {
        return PageSection::query()->count();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    #[Computed]
    public function filters(): array
    {
        return [
            ['value' => 'all', 'label' => 'All'],
            ...array_map(
                fn (SectionStatus $status): array => ['value' => $status->value, 'label' => $status->label()],
                SectionStatus::cases(),
            ),
        ];
    }

    public function updatedSearch(): void
    {
        $this->selected = [];
    }

    public function updatedFilter(): void
    {
        $this->selected = [];
    }

    public function toggleAll(): void
    {
        $ids = $this->sections->pluck('id')->all();

        $this->selected = count($this->selected) === count($ids) ? [] : $ids;
    }

    /**
     * Bulk-apply a status to the selected sections.
     *
     * Sections are fixed bands of the landing page, so the bulk action changes
     * visibility rather than deleting rows.
     */
    public function applyStatus(string $status): void
    {
        $status = SectionStatus::from($status);

        PageSection::query()
            ->whereIn('id', $this->selected)
            ->update(['status' => $status->value]);

        $count = count($this->selected);
        $this->selected = [];

        unset($this->sections);

        Flux::toast(variant: 'success', text: __(':count section(s) set to :status.', [
            'count' => $count,
            'status' => $status->label(),
        ]));
    }
}
