<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Services\Admin\RoleCrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class RoleController extends Controller
{
    public function index(RoleCrudService $service): View
    {
        return view('admin.roles.index', $service->indexData());
    }

    public function create(RoleCrudService $service): View
    {
        return view('admin.roles.create', $service->createData());
    }

    public function store(
        StoreRoleRequest $request,
        RoleCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated());

        return to_route('admin.roles.edit', $result['model'])
            ->with('success', $result['message']);
    }

    public function edit(Role $role, RoleCrudService $service): View
    {
        return view('admin.roles.edit', $service->editData($role));
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role,
        RoleCrudService $service,
    ): RedirectResponse {
        $result = $service->update($role, $request->validated());

        return to_route('admin.roles.edit', $role)
            ->with('success', $result['message']);
    }

    public function destroy(Role $role, RoleCrudService $service): RedirectResponse
    {
        $result = $service->delete($role);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return to_route('admin.roles.index')->with('success', $result['message']);
    }
}
