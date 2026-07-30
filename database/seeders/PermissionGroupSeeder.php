<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'Administrator' => 'Manage administrator accounts, roles, and access.',
            'User Management' => 'Manage website user accounts and their access state.',
            'Template Management' => 'Manage resume template categories and related catalog data.',
        ];

        foreach ($groups as $name => $description) {
            PermissionGroup::query()->updateOrCreate(
                ['name' => $name, 'type' => 1],
                [
                    'short_desc' => $description,
                    'status' => 1,
                ],
            );
        }
    }
}
