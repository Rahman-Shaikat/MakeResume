<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\AdminUser;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AdminTopbarComposer
{
    public function __construct(
        private readonly AdminNotificationService $service,
    ) {}

    public function compose(View $view): void
    {
        /** @var AdminUser|null $admin */
        $admin = Auth::guard('admin')->user();

        $view->with(
            $admin
                ? $this->service->topbarData($admin)
                : [
                    'adminNotifications' => collect(),
                    'adminUnreadNotificationCount' => 0,
                ],
        );
    }
}
