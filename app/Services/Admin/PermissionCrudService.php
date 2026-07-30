<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class PermissionCrudService
{
    public function indexData(): array
    {
        return [
            'permissionGroups' => PermissionGroup::query()
                ->with([
                    'parentPermissions' => fn ($query) => $query
                        ->where('type', 1)
                        ->where('status', 1)
                        ->withCount('roles')
                        ->with(['children' => fn ($query) => $query
                            ->where('type', 1)
                            ->where('status', 1)
                            ->withCount('roles')
                            ->orderBy('id')])
                        ->orderBy('id'),
                ])
                ->where('type', 1)
                ->where('status', 1)
                ->orderBy('id')
                ->get(),
        ];
    }

    public function createData(): array
    {
        return [
            'groups' => $this->groups(),
            'parentPermissions' => $this->parentPermissions(),
        ];
    }

    public function store(array $data): array
    {
        $permission = Permission::query()->create($data + ['status' => 1]);

        return [
            'success' => true,
            'message' => 'Permission created successfully.',
            'model' => $permission,
        ];
    }

    public function editData(Permission $permission): array
    {
        $this->ensureActiveAdminPermission($permission);

        return [
            'permission' => $permission,
            'groups' => $this->groups(),
            'parentPermissions' => $this->parentPermissions($permission),
        ];
    }

    public function update(Permission $permission, array $data): array
    {
        $this->ensureActiveAdminPermission($permission);
        $permission->update($data);

        return [
            'success' => true,
            'message' => 'Permission updated successfully. Its route key was preserved.',
            'model' => $permission->refresh(),
        ];
    }

    public function delete(Permission $permission): array
    {
        $this->ensureActiveAdminPermission($permission);

        DB::transaction(function () use ($permission): void {
            $permissions = Permission::query()
                ->whereKey($permission->id)
                ->when($permission->parent_id === 0, fn ($query) => $query
                    ->orWhere('parent_id', $permission->id))
                ->lockForUpdate()
                ->get();

            foreach ($permissions as $item) {
                $item->roles()->detach();
                $item->update(['status' => 2]);
            }
        });

        return [
            'success' => true,
            'message' => 'Permission deactivated successfully.',
        ];
    }

    /** @return Collection<int, PermissionGroup> */
    private function groups(): Collection
    {
        return PermissionGroup::query()
            ->select(['id', 'name'])
            ->where('type', 1)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, Permission> */
    private function parentPermissions(?Permission $exclude = null): Collection
    {
        return Permission::query()
            ->select(['id', 'group_id', 'name'])
            ->with('group:id,name')
            ->where('parent_id', 0)
            ->where('type', 1)
            ->where('status', 1)
            ->when($exclude, fn ($query) => $query->where('id', '!=', $exclude->id))
            ->orderBy('group_id')
            ->orderBy('name')
            ->get();
    }

    private function ensureActiveAdminPermission(Permission $permission): void
    {
        abort_unless($permission->type === 1 && $permission->status === 1, 404);
    }
}
