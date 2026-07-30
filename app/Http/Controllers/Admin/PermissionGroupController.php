<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionGroupRequest;
use App\Http\Requests\Admin\UpdatePermissionGroupRequest;
use App\Models\PermissionGroup;
use App\Services\Admin\PermissionGroupCrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class PermissionGroupController extends Controller
{
    public function create(PermissionGroupCrudService $service): View
    {
        return view('admin.permissions.groups.create', $service->createData());
    }

    public function store(
        StorePermissionGroupRequest $request,
        PermissionGroupCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated());

        return to_route('admin.permission-groups.edit', $result['model'])
            ->with('success', $result['message']);
    }

    public function edit(
        PermissionGroup $permissionGroup,
        PermissionGroupCrudService $service,
    ): View {
        return view('admin.permissions.groups.edit', $service->editData($permissionGroup));
    }

    public function update(
        UpdatePermissionGroupRequest $request,
        PermissionGroup $permissionGroup,
        PermissionGroupCrudService $service,
    ): RedirectResponse {
        $result = $service->update($permissionGroup, $request->validated());

        return to_route('admin.permission-groups.edit', $permissionGroup)
            ->with('success', $result['message']);
    }

    public function destroy(
        PermissionGroup $permissionGroup,
        PermissionGroupCrudService $service,
    ): RedirectResponse {
        $result = $service->delete($permissionGroup);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return to_route('admin.permissions.index')
            ->with('success', $result['message']);
    }
}
