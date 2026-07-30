<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminPasswordRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $adminUsers = AdminUser::query()
            ->filter(request()->only('search'))
            ->with('role')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.admin-users.index', compact('adminUsers'));
    }

    public function create(): View
    {
        return view('admin.admin-users.create', ['roles' => $this->activeRoles()]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_super'] = (int) ($validated['is_super'] ?? 2);
        $validated['status'] = 1;
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('admin-users', 'public');
        }

        $adminUser = AdminUser::query()->create($validated);

        return to_route('admin.admin-users.edit', $adminUser)
            ->with('success', 'Administrator created successfully.');
    }

    public function edit(AdminUser $adminUser): View
    {
        return view('admin.admin-users.edit', [
            'adminUser' => $adminUser,
            'roles' => $this->activeRoles($adminUser->role_id),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, AdminUser $adminUser): RedirectResponse
    {
        $validated = $request->validated();
        $validated['status'] = (int) $validated['status'];
        $validated['is_super'] = (int) $validated['is_super'];
        $oldImage = $adminUser->image_path;

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('admin-users', 'public');
        }

        $blocked = DB::transaction(function () use ($adminUser, $validated): bool {
            $lockedAdmin = AdminUser::query()->lockForUpdate()->findOrFail($adminUser->id);

            if ($this->removesLastActiveSuper($lockedAdmin, $validated['status'], $validated['is_super'])) {
                return true;
            }

            $lockedAdmin->update($validated);

            return false;
        });

        if ($blocked) {
            if (($validated['image_path'] ?? null) !== null && $validated['image_path'] !== $oldImage) {
                Storage::disk('public')->delete($validated['image_path']);
            }

            return back()->withInput()->with('error', 'The last active super administrator cannot be disabled or demoted.');
        }

        if (($validated['image_path'] ?? null) !== null && $oldImage && $validated['image_path'] !== $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return to_route('admin.admin-users.edit', $adminUser)->with('success', 'Administrator updated successfully.');
    }

    public function editPassword(AdminUser $adminUser): View
    {
        return view('admin.admin-users.password', compact('adminUser'));
    }

    public function updatePassword(
        UpdateAdminPasswordRequest $request,
        AdminUser $adminUser,
    ): RedirectResponse {
        $adminUser->update(['password' => Hash::make($request->validated('password'))]);

        return to_route('admin.admin-users.edit', $adminUser)->with('success', 'Password updated successfully.');
    }

    public function destroy(AdminUser $adminUser): RedirectResponse
    {
        $blocked = DB::transaction(function () use ($adminUser): bool {
            $lockedAdmin = AdminUser::query()->lockForUpdate()->findOrFail($adminUser->id);

            if ($this->removesLastActiveSuper($lockedAdmin, 2, $lockedAdmin->is_super)) {
                return true;
            }

            $lockedAdmin->update(['status' => 2]);

            return false;
        });

        if ($blocked) {
            return back()->with('error', 'The last active super administrator cannot be disabled.');
        }

        return to_route('admin.admin-users.index')->with('success', 'Administrator deactivated successfully.');
    }

    private function removesLastActiveSuper(AdminUser $adminUser, int $newStatus, int $newSuperStatus): bool
    {
        if ($adminUser->status !== 1 || $adminUser->is_super !== 1) {
            return false;
        }

        if ($newStatus === 1 && $newSuperStatus === 1) {
            return false;
        }

        return AdminUser::query()
            ->where('status', 1)
            ->where('is_super', 1)
            ->count() <= 1;
    }

    private function activeRoles(?int $includeRoleId = null)
    {
        return Role::query()
            ->where('type', 1)
            ->where(function ($query) use ($includeRoleId): void {
                $query->where('status', 1)
                    ->when($includeRoleId, fn ($query) => $query->orWhereKey($includeRoleId));
            })
            ->orderBy('name')
            ->get();
    }
}
