<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissionGroups = PermissionGroup::query()
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
            ->get();

        return view('admin.permissions.index', compact('permissionGroups'));
    }

    public function create(): View
    {
        return view('admin.permissions.create', [
            'groups' => $this->groups(),
            'parentPermissions' => $this->parentPermissions(),
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $permission = Permission::query()->create(
            $request->validated() + ['status' => 1],
        );

        return to_route('admin.permissions.edit', $permission)
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission): View
    {
        abort_unless($permission->type === 1 && $permission->status === 1, 404);

        return view('admin.permissions.edit', [
            'permission' => $permission,
            'groups' => $this->groups(),
            'parentPermissions' => $this->parentPermissions($permission),
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        abort_unless($permission->type === 1 && $permission->status === 1, 404);

        $permission->update($request->validated());

        return to_route('admin.permissions.edit', $permission)
            ->with('success', 'Permission updated successfully. Its route key was preserved.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        abort_unless($permission->type === 1 && $permission->status === 1, 404);

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

        return to_route('admin.permissions.index')
            ->with('success', 'Permission deactivated successfully.');
    }

    /** @return Collection<int, PermissionGroup> */
    private function groups(): Collection
    {
        return PermissionGroup::query()
            ->where('type', 1)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, Permission> */
    private function parentPermissions(?Permission $exclude = null): Collection
    {
        return Permission::query()
            ->with('group')
            ->where('parent_id', 0)
            ->where('type', 1)
            ->where('status', 1)
            ->when($exclude, fn ($query) => $query->where('id', '!=', $exclude->id))
            ->orderBy('group_id')
            ->orderBy('name')
            ->get();
    }
}
