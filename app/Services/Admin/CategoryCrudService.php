<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CategoryCrudService
{
    /** @param array{search?: string|null, status?: string|null, level?: string|null} $filters */
    public function indexData(array $filters = []): array
    {
        $hasActiveFilters = filled($filters['search'] ?? null)
            || filled($filters['status'] ?? null)
            || filled($filters['level'] ?? null);

        return [
            'categories' => Category::query()
                ->filter($filters)
                ->with('parent')
                ->withCount(['children' => fn ($query) => $query->where('status', 1)])
                ->orderBy('position')
                ->orderBy('name')
                ->get(),
            'reorderEnabled' => ! $hasActiveFilters,
        ];
    }

    public function createData(): array
    {
        return [
            'parentCategories' => $this->parentCategories(),
            'statusOptions' => $this->statusOptions(),
            'featuredOptions' => $this->featuredOptions(),
        ];
    }

    public function store(array $data): array
    {
        $category = DB::transaction(function () use ($data): Category {
            $lastCategory = Category::query()
                ->orderByDesc('position')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            return Category::query()->create([
                ...$data,
                'position' => $lastCategory ? $lastCategory->position + 1 : 0,
            ]);
        });

        return [
            'success' => true,
            'message' => 'Category created successfully.',
            'model' => $category,
        ];
    }

    public function editData(Category $category): array
    {
        return [
            'category' => $category,
            'parentCategories' => $this->parentCategories($category, $category->parent_id ?: null),
            'statusOptions' => $this->statusOptions(),
            'featuredOptions' => $this->featuredOptions(),
        ];
    }

    public function update(Category $category, array $data): array
    {
        DB::transaction(function () use ($category, $data): void {
            $lockedCategory = Category::query()->lockForUpdate()->findOrFail($category->id);
            $lockedCategory->update($data);

            if ($lockedCategory->parent_id === 0 && (int) $data['status'] === 2) {
                $lockedCategory->children()->where('status', 1)->update(['status' => 2]);
            }
        });

        return [
            'success' => true,
            'message' => 'Category updated successfully.',
            'model' => $category->refresh(),
        ];
    }

    /** @param array<int, int|string> $categoryIds */
    public function reorder(array $categoryIds): array
    {
        /** @var list<int> $categoryIds */
        $categoryIds = collect($categoryIds)
            ->map(fn ($categoryId): int => (int) $categoryId)
            ->values()
            ->all();

        DB::transaction(function () use ($categoryIds): void {
            $categories = Category::query()
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $existingIds = $categories->keys()->map(fn ($categoryId): int => (int) $categoryId)->sort()->values()->all();
            $submittedIds = collect($categoryIds)->sort()->values()->all();

            if ($existingIds !== $submittedIds) {
                throw ValidationException::withMessages([
                    'category_ids' => 'The category list changed. Refresh the page and try again.',
                ]);
            }

            foreach ($categoryIds as $position => $categoryId) {
                $categories->get($categoryId)?->update(['position' => $position]);
            }
        });

        return [
            'success' => true,
            'message' => 'Category order updated successfully.',
        ];
    }

    public function delete(Category $category): array
    {
        DB::transaction(function () use ($category): void {
            $lockedCategory = Category::query()->lockForUpdate()->findOrFail($category->id);
            $lockedCategory->update(['status' => 2]);

            if ($lockedCategory->parent_id === 0) {
                $lockedCategory->children()->where('status', 1)->update(['status' => 2]);
            }
        });

        return [
            'success' => true,
            'message' => 'Category deactivated successfully.',
        ];
    }

    /** @return Collection<int, Category> */
    private function parentCategories(?Category $exclude = null, ?int $includeParentId = null): Collection
    {
        return Category::query()
            ->select(['id', 'name', 'status', 'position'])
            ->where('parent_id', 0)
            ->where(function ($query) use ($includeParentId): void {
                $query->where('status', 1)
                    ->when($includeParentId, fn ($query) => $query->orWhereKey($includeParentId));
            })
            ->when($exclude, fn ($query) => $query->where('id', '!=', $exclude->id))
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    /** @return list<array{value: int, label: string, description: string}> */
    private function statusOptions(): array
    {
        return [
            ['value' => 1, 'label' => 'Active', 'description' => 'Available for template assignment'],
            ['value' => 2, 'label' => 'Inactive', 'description' => 'Hidden from active use'],
        ];
    }

    /** @return list<array{value: int, label: string, description: string}> */
    private function featuredOptions(): array
    {
        return [
            ['value' => 2, 'label' => 'No', 'description' => 'Use standard placement'],
            ['value' => 1, 'label' => 'Yes', 'description' => 'Highlight this category'],
        ];
    }
}
