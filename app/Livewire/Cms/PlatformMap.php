<?php

namespace App\Livewire\Cms;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Livewire exposes #[Computed] methods as properties at runtime.
 *
 * @property-read array<int, array{id: int, name: string, lat: float, lng: float, description: string, status: string}> $platforms
 * @property-read array{lat: float, lng: float, zoom: int} $center
 */
#[Layout('layouts::cms')]
#[Title('Platform Map')]
class PlatformMap extends Component
{
    /**
     * Default map center and zoom level.
     *
     * @return array{lat: float, lng: float, zoom: int}
     */
    #[Computed]
    public function center(): array
    {
        return config('platforms.center');
    }

    /**
     * Platform locations rendered as markers on the map.
     *
     * @return array<int, array{id: int, name: string, lat: float, lng: float, description: string, status: string}>
     */
    #[Computed]
    public function platforms(): array
    {
        return config('platforms.items');
    }

    /**
     * Count of platforms grouped by status, for the summary cards.
     *
     * @return array<int, array{label: string, value: int, tone: string}>
     */
    #[Computed]
    public function statusSummary(): array
    {
        $platforms = $this->platforms;

        return [
            ['label' => 'Active', 'value' => count(array_filter($platforms, fn (array $p): bool => $p['status'] === 'active')), 'tone' => 'green'],
            ['label' => 'Maintenance', 'value' => count(array_filter($platforms, fn (array $p): bool => $p['status'] === 'maintenance')), 'tone' => 'amber'],
            ['label' => 'Offline', 'value' => count(array_filter($platforms, fn (array $p): bool => $p['status'] === 'offline')), 'tone' => 'red'],
            ['label' => 'Total', 'value' => count($platforms), 'tone' => 'accent'],
        ];
    }
}
