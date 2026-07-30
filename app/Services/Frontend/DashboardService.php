<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Models\User;

final class DashboardService
{
    public function indexData(User $user): array
    {
        return [
            'user' => $user,
            'resumes' => $user->resumes()->latest('updated_at')->get(),
            'templates' => config('resume_templates.catalog'),
        ];
    }
}
