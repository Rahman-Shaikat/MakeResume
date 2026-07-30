<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\PermissionGroup;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->where('type', 1)
            ->where('status', 1)
            ->withCount([
                'permissions',
                'adminUsers as active_admin_users_count' => fn ($query) => $query->where('status', 1),
            ])
            ->latest()
            ->paginate(20);

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $permissionIds = collect($validated['permission'] ?? [])->map(fn ($id): int => (int) $id)->unique();
        unset($validated['permission']);

        $role = DB::transaction(function () use ($validated, $permissionIds): Role {
            $role = Role::query()->create($validated + ['status' => 1]);
            $role->permissions()->sync($permissionIds);

            return $role;
        });

        return to_route('admin.roles.edit', $role)->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        abort_unless($role->type === 1, 404);

        return view('admin.roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissions' => $role->permissions()->pluck('permissions.id')->all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        abort_unless($role->type === 1, 404);

        $validated = $request->validated();
        $permissionIds = collect($validated['permission'] ?? [])->map(fn ($id): int => (int) $id)->unique();
        unset($validated['permission']);

        DB::transaction(function () use ($role, $validated, $permissionIds): void {
            $role->update($validated);
            $role->permissions()->sync($permissionIds);
        });

        return to_route('admin.roles.edit', $role)->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_unless($role->type === 1, 404);

        if ($role->adminUsers()->where('status', 1)->exists()) {
            return back()->with('error', 'This role is assigned to active administrators and cannot be deactivated.');
        }

        $role->update(['status' => 2]);

        return to_route('admin.roles.index')->with('success', 'Role deactivated successfully.');
    }

    private function permissionGroups()
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
}
