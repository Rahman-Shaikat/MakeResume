<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminPasswordRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\AdminUser;
use App\Services\Admin\AdminUserCrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AdminUserController extends Controller
{
    public function index(Request $request, AdminUserCrudService $service): View
    {
        return view('admin.admin-users.index', $service->indexData($request->only('search')));
    }

    public function create(AdminUserCrudService $service): View
    {
        return view('admin.admin-users.create', $service->createData());
    }

    public function store(
        StoreAdminUserRequest $request,
        AdminUserCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated());

        return to_route('admin.admin-users.edit', $result['model'])
            ->with('success', $result['message']);
    }

    public function edit(AdminUser $adminUser, AdminUserCrudService $service): View
    {
        return view('admin.admin-users.edit', $service->editData($adminUser));
    }

    public function update(
        UpdateAdminUserRequest $request,
        AdminUser $adminUser,
        AdminUserCrudService $service,
    ): RedirectResponse {
        $result = $service->update($adminUser, $request->validated());

        if (! $result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return to_route('admin.admin-users.edit', $adminUser)->with('success', $result['message']);
    }

    public function editPassword(AdminUser $adminUser): View
    {
        return view('admin.admin-users.password', compact('adminUser'));
    }

    public function updatePassword(
        UpdateAdminPasswordRequest $request,
        AdminUser $adminUser,
        AdminUserCrudService $service,
    ): RedirectResponse {
        $result = $service->updatePassword($adminUser, $request->validated('password'));

        return to_route('admin.admin-users.edit', $adminUser)->with('success', $result['message']);
    }

    public function destroy(AdminUser $adminUser, AdminUserCrudService $service): RedirectResponse
    {
        $result = $service->delete($adminUser);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return to_route('admin.admin-users.index')->with('success', $result['message']);
    }
}
