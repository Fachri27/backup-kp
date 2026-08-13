<?php

namespace Database\Factories;

use App\Enums\SectionStatus;
use App\Models\PageSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageSection>
 */
class PageSectionFactory extends Factory
{
    protected $model = PageSection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(1),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'content' => ['heading' => fake()->sentence()],
            'status' => SectionStatus::Published,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => SectionStatus::Draft]);
    }

    public function inReview(): static
    {
        return $this->state(['status' => SectionStatus::Review]);
    }
}
