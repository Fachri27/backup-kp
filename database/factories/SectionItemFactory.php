<?php

namespace Database\Factories;

use App\Models\PageSection;
use App\Models\SectionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SectionItem>
 */
class SectionItemFactory extends Factory
{
    protected $model = SectionItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_section_id' => PageSection::factory(),
            'type' => 'feature',
            'sort_order' => fake()->numberBetween(0, 10),
            'is_published' => true,
            'data' => ['title' => fake()->words(2, true)],
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }
}
