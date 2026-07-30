<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\PermissionGroup;

final class PermissionGroupCrudService
{
    public function createData(): array
    {
        return [];
    }

    public function store(array $data): array
    {
        $group = PermissionGroup::query()->create($data + ['status' => 1]);

        return [
            'success' => true,
            'message' => 'Permission group created successfully.',
            'model' => $group,
        ];
    }

    public function editData(PermissionGroup $permissionGroup): array
    {
        $this->ensureActiveAdminGroup($permissionGroup);

        return [
            'permissionGroup' => $permissionGroup,
        ];
    }

    public function update(PermissionGroup $permissionGroup, array $data): array
    {
        $this->ensureActiveAdminGroup($permissionGroup);
        $permissionGroup->update($data);

        return [
            'success' => true,
            'message' => 'Permission group updated successfully.',
            'model' => $permissionGroup->refresh(),
        ];
    }

    public function delete(PermissionGroup $permissionGroup): array
    {
        $this->ensureActiveAdminGroup($permissionGroup);

        if ($permissionGroup->permissions()->where('status', 1)->exists()) {
            return [
                'success' => false,
                'message' => 'Move or deactivate this group’s active permissions first.',
            ];
        }

        $permissionGroup->update(['status' => 2]);

        return [
            'success' => true,
            'message' => 'Permission group deactivated successfully.',
        ];
    }

    private function ensureActiveAdminGroup(PermissionGroup $permissionGroup): void
    {
        abort_unless($permissionGroup->type === 1 && $permissionGroup->status === 1, 404);
    }
}
