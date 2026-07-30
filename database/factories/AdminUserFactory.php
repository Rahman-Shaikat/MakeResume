<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<AdminUser> */
class AdminUserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => Role::factory(),
            'country_id' => 0,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->optional()->address(),
            'gender' => fake()->numberBetween(0, 3),
            'phone' => null,
            'image_path' => null,
            'password' => static::$password ??= Hash::make('password'),
            'status' => 1,
            'is_super' => 2,
            'email_verified_at' => now(),
        ];
    }

    public function super(): static
    {
        return $this->state(fn (): array => ['is_super' => 1]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => 2]);
    }
}
