<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->jobTitle();

        return [
            'parent_id' => 0,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'short_desc' => fake()->optional()->sentence(),
            'status' => 1,
            'position' => fake()->numberBetween(0, 100),
            'is_featured' => 2,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => 1]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => 2]);
    }
}
