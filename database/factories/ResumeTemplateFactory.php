<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ResumeTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ResumeTemplate>
 */
final class ResumeTemplateFactory extends Factory
{
    protected $model = ResumeTemplate::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 99999),
            'renderer_key' => 'template-one',
            'name' => Str::title($name),
            'short_desc' => fake()->sentence(),
            'thumbnail_path' => null,
            'accent_color' => '#00B6CE',
            'allows_profile_photo' => 1,
            'is_ats_friendly' => 1,
            'is_featured' => 2,
            'status' => 2,
            'position' => 0,
        ];
    }
}
