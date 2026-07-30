<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $administratorGroup = PermissionGroup::query()
            ->where('name', 'Administrator')
            ->where('type', 1)
            ->firstOrFail();

        $modules = [
            [
                'name' => 'Roles',
                'meta_name' => 'roles',
                'short_desc' => 'View administrator roles.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'roles-create'],
                    ['name' => 'Update', 'meta_name' => 'roles-update'],
                    ['name' => 'Delete', 'meta_name' => 'roles-delete'],
                ],
            ],
            [
                'name' => 'Administrators',
                'meta_name' => 'admin-users',
                'short_desc' => 'View administrator accounts.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'admin-users-create'],
                    ['name' => 'Update', 'meta_name' => 'admin-users-update'],
                    ['name' => 'Delete', 'meta_name' => 'admin-users-delete'],
                ],
            ],
            [
                'name' => 'Permissions',
                'meta_name' => 'permissions',
                'short_desc' => 'View permission groups and permission keys.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'permissions-create'],
                    ['name' => 'Update', 'meta_name' => 'permissions-update'],
                    ['name' => 'Delete', 'meta_name' => 'permissions-delete'],
                ],
            ],
        ];

        $this->seedModules($administratorGroup, $modules);

        $templateGroup = PermissionGroup::query()
            ->where('name', 'Template Management')
            ->where('type', 1)
            ->firstOrFail();

        $this->seedModules($templateGroup, [
            [
                'name' => 'Categories',
                'meta_name' => 'categories',
                'short_desc' => 'View resume template categories and subcategories.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'categories-create'],
                    ['name' => 'Update', 'meta_name' => 'categories-update'],
                    ['name' => 'Delete', 'meta_name' => 'categories-delete'],
                ],
            ],
        ]);
    }

    /**
     * @param  array<int, array{
     *     name: string,
     *     meta_name: string,
     *     short_desc: string,
     *     children: array<int, array{name: string, meta_name: string}>
     * }>  $modules
     */
    private function seedModules(PermissionGroup $group, array $modules): void
    {
        foreach ($modules as $module) {
            $parent = Permission::query()->updateOrCreate(
                ['meta_name' => $module['meta_name']],
                [
                    'group_id' => $group->id,
                    'parent_id' => 0,
                    'name' => $module['name'],
                    'short_desc' => $module['short_desc'],
                    'type' => 1,
                    'status' => 1,
                ],
            );

            foreach ($module['children'] as $child) {
                Permission::query()->updateOrCreate(
                    ['meta_name' => $child['meta_name']],
                    [
                        'group_id' => $group->id,
                        'parent_id' => $parent->id,
                        'name' => $child['name'],
                        'short_desc' => "{$child['name']} {$module['name']}.",
                        'type' => 1,
                        'status' => 1,
                    ],
                );
            }
        }
    }
}
