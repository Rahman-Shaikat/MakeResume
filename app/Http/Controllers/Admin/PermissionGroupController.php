<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionGroupRequest;
use App\Http\Requests\Admin\UpdatePermissionGroupRequest;
use App\Models\PermissionGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionGroupController extends Controller
{
    public function create(): View
    {
        return view('admin.permissions.groups.create');
    }

    public function store(StorePermissionGroupRequest $request): RedirectResponse
    {
        $group = PermissionGroup::query()->create(
            $request->validated() + ['status' => 1],
        );

        return to_route('admin.permission-groups.edit', $group)
            ->with('success', 'Permission group created successfully.');
    }

    public function edit(PermissionGroup $permissionGroup): View
    {
        abort_unless($permissionGroup->type === 1 && $permissionGroup->status === 1, 404);

        return view('admin.permissions.groups.edit', compact('permissionGroup'));
    }

    public function update(
        UpdatePermissionGroupRequest $request,
        PermissionGroup $permissionGroup,
    ): RedirectResponse {
        abort_unless($permissionGroup->type === 1 && $permissionGroup->status === 1, 404);

        $permissionGroup->update($request->validated());

        return to_route('admin.permission-groups.edit', $permissionGroup)
            ->with('success', 'Permission group updated successfully.');
    }

    public function destroy(PermissionGroup $permissionGroup): RedirectResponse
    {
        abort_unless($permissionGroup->type === 1 && $permissionGroup->status === 1, 404);

        if ($permissionGroup->permissions()->where('status', 1)->exists()) {
            return back()->with('error', 'Move or deactivate this group’s active permissions first.');
        }

        $permissionGroup->update(['status' => 2]);

        return to_route('admin.permissions.index')
            ->with('success', 'Permission group deactivated successfully.');
    }
}
