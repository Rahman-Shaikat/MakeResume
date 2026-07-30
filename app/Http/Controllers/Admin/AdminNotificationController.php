<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminNotificationController extends Controller
{
    public function open(
        Request $request,
        string $notification,
        AdminNotificationService $service,
    ): RedirectResponse {
        /** @var AdminUser $admin */
        $admin = $request->user('admin');

        return redirect()->to($service->readAndResolveTarget($admin, $notification));
    }

    public function markAllRead(
        Request $request,
        AdminNotificationService $service,
    ): RedirectResponse {
        /** @var AdminUser $admin */
        $admin = $request->user('admin');
        $updated = $service->markAllRead($admin);

        return back()->with(
            'success',
            $updated > 0 ? 'All notifications marked as read.' : 'No unread notifications.',
        );
    }
}
