<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\PermissionGroup;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class RoleCrudService
{
    public function indexData(): array
    {
        return [
            'roles' => Role::query()
                ->where('type', 1)
                ->where('status', 1)
                ->withCount([
                    'permissions',
                    'adminUsers as active_admin_users_count' => fn ($query) => $query->where('status', 1),
                ])
                ->latest()
                ->paginate(20),
        ];
    }

    public function createData(): array
    {
        return [
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissions' => [],
        ];
    }

    public function store(array $data): array
    {
        $permissionIds = $this->permissionIds($data);
        unset($data['permission']);

        $role = DB::transaction(function () use ($data, $permissionIds): Role {
            $role = Role::query()->create($data + ['status' => 1]);
            $role->permissions()->sync($permissionIds);

            return $role;
        });

        return [
            'success' => true,
            'message' => 'Role created successfully.',
            'model' => $role,
        ];
    }

    public function editData(Role $role): array
    {
        $this->ensureAdminRole($role);

        return [
            'role' => $role,
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissions' => $role->permissions()->pluck('permissions.id')->all(),
        ];
    }

    public function update(Role $role, array $data): array
    {
        $this->ensureAdminRole($role);
        $permissionIds = $this->permissionIds($data);
        unset($data['permission']);

        DB::transaction(function () use ($role, $data, $permissionIds): void {
            $role->update($data);
            $role->permissions()->sync($permissionIds);
        });

        return [
            'success' => true,
            'message' => 'Role updated successfully.',
            'model' => $role->refresh(),
        ];
    }

    public function delete(Role $role): array
    {
        $this->ensureAdminRole($role);

        if ($role->adminUsers()->where('status', 1)->exists()) {
            return [
                'success' => false,
                'message' => 'This role is assigned to active administrators and cannot be deactivated.',
            ];
        }

        $role->update(['status' => 2]);

        return [
            'success' => true,
            'message' => 'Role deactivated successfully.',
        ];
    }

    /** @return Collection<int, PermissionGroup> */
    private function permissionGroups(): Collection
    {
        return PermissionGroup::query()
            ->with([
                'parentPermissions' => fn ($query) => $query
                    ->where('type', 1)
                    ->where('status', 1)
                    ->with(['children' => fn ($query) => $query
                        ->where('type', 1)
                        ->where('status', 1)
                        ->orderBy('id')])
                    ->orderBy('id'),
            ])
            ->where('type', 1)
            ->where('status', 1)
            ->orderBy('id')
            ->get();
    }

    /** @return list<int> */
    private function permissionIds(array $data): array
    {
        return collect($data['permission'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function ensureAdminRole(Role $role): void
    {
        abort_unless($role->type === 1, 404);
    }
}
