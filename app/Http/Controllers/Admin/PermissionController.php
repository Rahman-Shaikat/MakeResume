<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\Admin\PermissionCrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class PermissionController extends Controller
{
    public function index(PermissionCrudService $service): View
    {
        return view('admin.permissions.index', $service->indexData());
    }

    public function create(PermissionCrudService $service): View
    {
        return view('admin.permissions.create', $service->createData());
    }

    public function store(
        StorePermissionRequest $request,
        PermissionCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated());

        return to_route('admin.permissions.edit', $result['model'])
            ->with('success', $result['message']);
    }

    public function edit(Permission $permission, PermissionCrudService $service): View
    {
        return view('admin.permissions.edit', $service->editData($permission));
    }

    public function update(
        UpdatePermissionRequest $request,
        Permission $permission,
        PermissionCrudService $service,
    ): RedirectResponse {
        $result = $service->update($permission, $request->validated());

        return to_route('admin.permissions.edit', $permission)
            ->with('success', $result['message']);
    }

    public function destroy(Permission $permission, PermissionCrudService $service): RedirectResponse
    {
        $result = $service->delete($permission);

        return to_route('admin.permissions.index')
            ->with('success', $result['message']);
    }
}
