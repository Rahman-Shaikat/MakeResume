<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()
            ->where('name', 'Super Admin')
            ->where('type', 1)
            ->firstOrFail();

        $role->permissions()->sync(
            Permission::query()->where('type', 1)->where('status', 1)->pluck('id'),
        );
    }
}
