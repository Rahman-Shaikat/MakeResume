<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HomepageHero;
use App\Models\ResumeTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomepageHero>
 */
final class HomepageHeroFactory extends Factory
{
    protected $model = HomepageHero::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'resume_template_id' => ResumeTemplate::factory(),
            'preview_image_path' => null,
            'eyebrow' => 'Your next role starts here',
            'headline' => fake()->sentence(8),
            'description' => fake()->paragraph(),
            'top_badge' => 'Designed around you',
            'editor_title' => 'Professional summary',
            'editor_description' => 'Clear, focused, and ready for the role you want next.',
            'bottom_status_title' => 'Saved automatically',
            'bottom_status_text' => 'Your progress is safe',
            'status' => 2,
        ];
    }
}
