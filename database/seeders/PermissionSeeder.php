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

        $userGroup = PermissionGroup::query()
            ->where('name', 'User Management')
            ->where('type', 1)
            ->firstOrFail();

        $this->seedModules($userGroup, [
            [
                'name' => 'Users',
                'meta_name' => 'users',
                'short_desc' => 'View website user accounts and resume activity.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'users-create'],
                    ['name' => 'Update', 'meta_name' => 'users-update'],
                    ['name' => 'Delete', 'meta_name' => 'users-delete'],
                ],
            ],
        ]);

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
            [
                'name' => 'Templates',
                'meta_name' => 'templates',
                'short_desc' => 'View and preview resume template catalog entries.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'templates-create'],
                    ['name' => 'Update', 'meta_name' => 'templates-update'],
                    ['name' => 'Delete', 'meta_name' => 'templates-delete'],
                ],
            ],
            [
                'name' => 'Homepage Hero',
                'meta_name' => 'homepage-heroes',
                'short_desc' => 'View and manage the public homepage hero content.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'homepage-heroes-create'],
                    ['name' => 'Update', 'meta_name' => 'homepage-heroes-update'],
                    ['name' => 'Delete', 'meta_name' => 'homepage-heroes-delete'],
                ],
            ],
            [
                'name' => 'Homepage Template Showcase',
                'meta_name' => 'homepage-template-showcases',
                'short_desc' => 'View and manage the homepage template showcase.',
                'children' => [
                    ['name' => 'Create', 'meta_name' => 'homepage-template-showcases-create'],
                    ['name' => 'Update', 'meta_name' => 'homepage-template-showcases-update'],
                    ['name' => 'Delete', 'meta_name' => 'homepage-template-showcases-delete'],
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
