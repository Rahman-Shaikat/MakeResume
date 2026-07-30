<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->updateOrCreate(
            ['name' => 'Super Admin', 'type' => 1],
            [
                'short_desc' => 'Full access to every Resume Studio administration feature.',
                'status' => 1,
            ],
        );
    }
}
