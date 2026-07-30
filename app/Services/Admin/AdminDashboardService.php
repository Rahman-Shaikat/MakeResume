<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\AdminUser;
use App\Models\Resume;
use App\Models\Role;
use App\Models\User;

final class AdminDashboardService
{
    /** @return array{totalUsers: int, totalResumes: int, activeAdministrators: int, activeRoles: int} */
    public function dashboardData(): array
    {
        return [
            'totalUsers' => User::query()->count(),
            'totalResumes' => Resume::query()->count(),
            'activeAdministrators' => AdminUser::query()->where('status', 1)->count(),
            'activeRoles' => Role::query()->where('type', 1)->where('status', 1)->count(),
        ];
    }
}
