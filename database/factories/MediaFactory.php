<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->slug(2).'.jpg';

        return [
            'name' => $name,
            'path' => 'media/'.$name,
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(20_000, 900_000),
            'width' => 1920,
            'height' => 1080,
            'user_id' => User::factory(),
        ];
    }
}
