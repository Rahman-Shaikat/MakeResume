<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Models\User;
use App\Services\ResumeAllowanceResolver;
use App\Services\TemplateCatalogService;

final class DashboardService
{
    public function __construct(
        private readonly TemplateCatalogService $catalog,
        private readonly ResumeAllowanceResolver $allowances,
    ) {}

    public function indexData(User $user, ?string $categorySlug = null): array
    {
        $resumes = $user->resumes()->with('template')->latest('updated_at')->get();

        return [
            'user' => $user,
            'resumes' => $resumes,
            'resumeQuota' => $this->allowances->quotaFor($user, $resumes->count()),
            'templates' => $this->catalog->activeTemplates($categorySlug),
            'templateCategories' => $this->catalog->activeCategories(),
            'selectedCategory' => $categorySlug,
        ];
    }
}
