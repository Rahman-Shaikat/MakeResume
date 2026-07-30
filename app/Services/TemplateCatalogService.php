<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\ResumeTemplate;
use Illuminate\Database\Eloquent\Collection;

final class TemplateCatalogService
{
    /**
     * @return Collection<int, ResumeTemplate>
     */
    public function activeTemplates(?string $categorySlug = null): Collection
    {
        return ResumeTemplate::query()
            ->active()
            ->with('categories:id,name,slug')
            ->when(
                $categorySlug,
                fn ($query) => $query->whereHas(
                    'categories',
                    fn ($query) => $query->where('categories.slug', $categorySlug)->where('categories.status', 1),
                ),
            )
            ->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END')
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, ResumeTemplate>
     */
    public function homepageTemplates(int $limit = 4): Collection
    {
        return ResumeTemplate::query()
            ->active()
            ->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END')
            ->orderBy('position')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Category>
     */
    public function activeCategories(): Collection
    {
        return Category::query()
            ->where('status', 1)
            ->whereHas('resumeTemplates', fn ($query) => $query->active())
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'parent_id']);
    }

    public function findActiveBySlug(string $slug): ResumeTemplate
    {
        return ResumeTemplate::query()->active()->where('slug', $slug)->firstOrFail();
    }

    public function findBySlug(string $slug): ResumeTemplate
    {
        return ResumeTemplate::query()->where('slug', $slug)->firstOrFail();
    }
}
