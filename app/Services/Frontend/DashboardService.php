<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Models\User;
use App\Services\TemplateCatalogService;

final class DashboardService
{
    public function __construct(
        private readonly TemplateCatalogService $catalog,
    ) {}

    public function indexData(User $user, ?string $categorySlug = null): array
    {
        return [
            'user' => $user,
            'resumes' => $user->resumes()->with('template')->latest('updated_at')->get(),
            'templates' => $this->catalog->activeTemplates($categorySlug),
            'templateCategories' => $this->catalog->activeCategories(),
            'selectedCategory' => $categorySlug,
        ];
    }
}
