<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\AdminUser;
use Illuminate\Support\Facades\Auth;

final class AdminAuthenticationService
{
    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{success: bool, message: string}
     */
    public function attempt(array $credentials): array
    {
        $admin = AdminUser::query()
            ->select(['id', 'email', 'status'])
            ->where('email', $credentials['email'])
            ->first();

        if (! $admin) {
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        if ($admin->status !== 1) {
            return [
                'success' => false,
                'message' => 'Your account has been blocked.',
            ];
        }

        if (! Auth::guard('admin')->attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ];
        }

        return [
            'success' => true,
            'message' => 'You are successfully logged in.',
        ];
    }

    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }
}
