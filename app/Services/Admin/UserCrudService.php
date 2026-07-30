<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UserCrudService
{
    /** @param array{search?: string|null, verification?: string|null} $filters */
    public function indexData(array $filters = []): array
    {
        return [
            'users' => User::query()
                ->withCount('resumes')
                ->when($filters['search'] ?? null, function ($query, string $search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->when($filters['verification'] ?? null, function ($query, string $verification): void {
                    match ($verification) {
                        'verified' => $query->whereNotNull('email_verified_at'),
                        'pending' => $query->whereNull('email_verified_at'),
                        default => null,
                    };
                })
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'verificationFilters' => $this->verificationFilters(),
        ];
    }

    public function createData(): array
    {
        return [
            'verificationOptions' => $this->verificationOptions(),
        ];
    }

    public function store(array $data): array
    {
        $verificationStatus = (int) $data['verification_status'];
        unset($data['verification_status']);

        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = $verificationStatus === 1 ? now() : null;

        $user = User::query()->create($data);

        return [
            'success' => true,
            'message' => 'User created successfully.',
            'model' => $user,
        ];
    }

    public function showData(User $user): array
    {
        return [
            'user' => $user->loadCount('resumes'),
            'resumes' => $user->resumes()
                ->with('template')
                ->latest()
                ->paginate(10),
        ];
    }

    public function editData(User $user): array
    {
        return [
            'user' => $user,
            'verificationOptions' => $this->verificationOptions(),
        ];
    }

    public function update(User $user, array $data): array
    {
        $verificationStatus = (int) $data['verification_status'];
        unset($data['verification_status']);
        $previousEmail = $user->email;

        $data['email_verified_at'] = $verificationStatus === 1
            ? ($user->email_verified_at ?? now())
            : null;

        DB::transaction(function () use ($user, $data, $previousEmail): void {
            $user->update($data);

            if ($user->email !== $previousEmail) {
                DB::table('password_reset_tokens')->where('email', $previousEmail)->delete();
            }
        });

        return [
            'success' => true,
            'message' => 'User updated successfully.',
            'model' => $user->refresh(),
        ];
    }

    public function updatePassword(User $user, string $password): array
    {
        $user->update(['password' => Hash::make($password)]);

        return [
            'success' => true,
            'message' => 'User password updated successfully.',
            'model' => $user,
        ];
    }

    public function delete(User $user): array
    {
        DB::transaction(function () use ($user): void {
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();
            $user->delete();
        });

        return [
            'success' => true,
            'message' => 'User and associated resume data deleted successfully.',
        ];
    }

    /** @return list<array{value: int, label: string, description: string}> */
    private function verificationOptions(): array
    {
        return [
            [
                'value' => 1,
                'label' => 'Verified',
                'description' => 'Allow immediate access to verified user features.',
            ],
            [
                'value' => 2,
                'label' => 'Pending verification',
                'description' => 'Require the user to verify their email address.',
            ],
        ];
    }

    /** @return list<array{value: string, label: string}> */
    private function verificationFilters(): array
    {
        return [
            ['value' => '', 'label' => 'All verification states'],
            ['value' => 'verified', 'label' => 'Verified'],
            ['value' => 'pending', 'label' => 'Pending verification'],
        ];
    }
}
