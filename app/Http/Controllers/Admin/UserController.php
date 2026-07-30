<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserPasswordRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserCrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function index(Request $request, UserCrudService $service): View
    {
        return view(
            'admin.users.index',
            $service->indexData($request->only(['search', 'verification'])),
        );
    }

    public function create(UserCrudService $service): View
    {
        return view('admin.users.create', $service->createData());
    }

    public function store(StoreUserRequest $request, UserCrudService $service): RedirectResponse
    {
        $result = $service->store($request->validated());

        return to_route('admin.users.show', $result['model'])
            ->with('success', $result['message']);
    }

    public function show(User $user, UserCrudService $service): View
    {
        return view('admin.users.show', $service->showData($user));
    }

    public function edit(User $user, UserCrudService $service): View
    {
        return view('admin.users.edit', $service->editData($user));
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UserCrudService $service,
    ): RedirectResponse {
        $result = $service->update($user, $request->validated());

        return to_route('admin.users.show', $result['model'])
            ->with('success', $result['message']);
    }

    public function editPassword(User $user): View
    {
        return view('admin.users.password', compact('user'));
    }

    public function updatePassword(
        UpdateUserPasswordRequest $request,
        User $user,
        UserCrudService $service,
    ): RedirectResponse {
        $result = $service->updatePassword($user, $request->validated('password'));

        return to_route('admin.users.show', $result['model'])
            ->with('success', $result['message']);
    }

    public function destroy(User $user, UserCrudService $service): RedirectResponse
    {
        $result = $service->delete($user);

        return to_route('admin.users.index')->with('success', $result['message']);
    }
}
