<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Models\User;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

final class UserRegistrationService
{
    public function __construct(
        private readonly AdminNotificationService $adminNotifications,
    ) {}

    public function register(array $data): User
    {
        $user = User::query()->create($data);

        event(new Registered($user));
        Auth::login($user);
        $this->adminNotifications->notifyActiveAdministrators(
            title: 'New user registered',
            message: "{$user->name} created a Resume Engineer account.",
            url: route('admin.dashboard'),
            icon: 'user-plus',
            tone: 'success',
        );

        return $user;
    }
}
