<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\AdminUser;
use App\Notifications\AdminActivityNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

final class AdminNotificationService
{
    public function topbarData(AdminUser $admin, int $limit = 5): array
    {
        return [
            'adminNotifications' => $admin->notifications()
                ->latest()
                ->limit($limit)
                ->get(),
            'adminUnreadNotificationCount' => $admin->unreadNotifications()->count(),
        ];
    }

    public function notifyActiveAdministrators(
        string $title,
        string $message,
        string $url,
        string $icon = 'bell',
        string $tone = 'primary',
    ): void {
        AdminUser::query()
            ->select(['id'])
            ->where('status', 1)
            ->chunkById(100, function ($administrators) use ($title, $message, $url, $icon, $tone): void {
                Notification::send(
                    $administrators,
                    new AdminActivityNotification($title, $message, $url, $icon, $tone),
                );
            });
    }

    public function markAllRead(AdminUser $admin): int
    {
        return $admin->unreadNotifications()->update(['read_at' => now()]);
    }

    public function readAndResolveTarget(AdminUser $admin, string $notificationId): string
    {
        /** @var DatabaseNotification $notification */
        $notification = $admin->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        $target = data_get($notification->data, 'url');
        $applicationUrl = rtrim(url('/'), '/');

        if (
            ! is_string($target)
            || ($target !== $applicationUrl && ! str_starts_with($target, $applicationUrl.'/'))
        ) {
            return route('admin.dashboard');
        }

        return $target;
    }
}
