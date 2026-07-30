<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

final class AdminUserCrudService
{
    /** @param array{search?: string|null} $filters */
    public function indexData(array $filters = []): array
    {
        return [
            'adminUsers' => AdminUser::query()
                ->filter($filters)
                ->with('role')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ];
    }

    public function createData(): array
    {
        return [
            'roles' => $this->activeRoles(),
            'genderOptions' => $this->genderOptions(),
            'statusOptions' => $this->statusOptions(),
            'accessOptions' => $this->accessOptions(),
        ];
    }

    public function store(array $data): array
    {
        $data['is_super'] = (int) ($data['is_super'] ?? 2);
        $data['status'] = 1;
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();
        $data = $this->storeUploadedImage($data);

        $adminUser = AdminUser::query()->create($data);

        return [
            'success' => true,
            'message' => 'Administrator created successfully.',
            'model' => $adminUser,
        ];
    }

    public function editData(AdminUser $adminUser): array
    {
        return [
            'adminUser' => $adminUser,
            'roles' => $this->activeRoles($adminUser->role_id),
            'genderOptions' => $this->genderOptions(),
            'statusOptions' => $this->statusOptions(),
            'accessOptions' => $this->accessOptions(),
        ];
    }

    public function update(AdminUser $adminUser, array $data): array
    {
        $data['status'] = (int) $data['status'];
        $data['is_super'] = (int) $data['is_super'];
        $oldImage = $adminUser->image_path;
        $data = $this->storeUploadedImage($data);

        $blocked = DB::transaction(function () use ($adminUser, $data): bool {
            $lockedAdmin = AdminUser::query()->lockForUpdate()->findOrFail($adminUser->id);

            if ($this->removesLastActiveSuper($lockedAdmin, $data['status'], $data['is_super'])) {
                return true;
            }

            $lockedAdmin->update($data);

            return false;
        });

        if ($blocked) {
            $this->deleteReplacementImage($data['image_path'] ?? null, $oldImage);

            return [
                'success' => false,
                'message' => 'The last active super administrator cannot be disabled or demoted.',
                'model' => $adminUser,
            ];
        }

        if (($data['image_path'] ?? null) !== null && $oldImage && $data['image_path'] !== $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return [
            'success' => true,
            'message' => 'Administrator updated successfully.',
            'model' => $adminUser->refresh(),
        ];
    }

    public function updatePassword(AdminUser $adminUser, string $password): array
    {
        $adminUser->update(['password' => Hash::make($password)]);

        return [
            'success' => true,
            'message' => 'Password updated successfully.',
            'model' => $adminUser,
        ];
    }

    public function delete(AdminUser $adminUser): array
    {
        $blocked = DB::transaction(function () use ($adminUser): bool {
            $lockedAdmin = AdminUser::query()->lockForUpdate()->findOrFail($adminUser->id);

            if ($this->removesLastActiveSuper($lockedAdmin, 2, $lockedAdmin->is_super)) {
                return true;
            }

            $lockedAdmin->update(['status' => 2]);

            return false;
        });

        return [
            'success' => ! $blocked,
            'message' => $blocked
                ? 'The last active super administrator cannot be disabled.'
                : 'Administrator deactivated successfully.',
        ];
    }

    /** @return list<array{id: int, name: string}> */
    private function activeRoles(?int $includeRoleId = null): array
    {
        return Role::query()
            ->select(['id', 'name', 'status'])
            ->where('type', 1)
            ->where(function ($query) use ($includeRoleId): void {
                $query->where('status', 1)
                    ->when($includeRoleId, fn ($query) => $query->orWhereKey($includeRoleId));
            })
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name.($role->status !== 1 ? ' (inactive)' : ''),
            ])
            ->values()
            ->all();
    }

    /** @return list<array{id: int, name: string}> */
    private function genderOptions(): array
    {
        return [
            ['id' => 0, 'name' => 'Prefer not to say'],
            ['id' => 1, 'name' => 'Male'],
            ['id' => 2, 'name' => 'Female'],
            ['id' => 3, 'name' => 'Other'],
        ];
    }

    /** @return list<array{id: int, name: string}> */
    private function statusOptions(): array
    {
        return [
            ['id' => 1, 'name' => 'Active'],
            ['id' => 2, 'name' => 'Inactive'],
        ];
    }

    /** @return list<array{id: int, name: string}> */
    private function accessOptions(): array
    {
        return [
            ['id' => 2, 'name' => 'Use assigned role'],
            ['id' => 1, 'name' => 'Super administrator'],
        ];
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

    private function storeUploadedImage(array $data): array
    {
        $image = $data['image_path'] ?? null;

        if ($image instanceof UploadedFile) {
            $data['image_path'] = $image->store('admin-users', 'public');
        } else {
            unset($data['image_path']);
        }

        return $data;
    }

    private function deleteReplacementImage(?string $newImage, ?string $oldImage): void
    {
        if ($newImage && $newImage !== $oldImage) {
            Storage::disk('public')->delete($newImage);
        }
    }
}
