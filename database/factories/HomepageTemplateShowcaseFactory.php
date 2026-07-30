<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HomepageTemplateShowcase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomepageTemplateShowcase>
 */
final class HomepageTemplateShowcaseFactory extends Factory
{
    protected $model = HomepageTemplateShowcase::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'eyebrow' => 'Designed to be read',
            'headline' => fake()->sentence(8),
            'cta_label' => 'Explore your workspace',
            'status' => 2,
        ];
    }
}
