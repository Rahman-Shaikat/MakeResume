<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->filter(request()->only(['search', 'status', 'level']))
            ->with('parent')
            ->withCount(['children' => fn ($query) => $query->where('status', 1)])
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'parentCategories' => $this->parentCategories(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::query()->create($request->validated());

        return to_route('admin.categories.edit', $category)
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parentCategories' => $this->parentCategories($category, $category->parent_id ?: null),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($category, $validated): void {
            $lockedCategory = Category::query()->lockForUpdate()->findOrFail($category->id);
            $lockedCategory->update($validated);

            if ($lockedCategory->parent_id === 0 && (int) $validated['status'] === 2) {
                $lockedCategory->children()->where('status', 1)->update(['status' => 2]);
            }
        });

        return to_route('admin.categories.edit', $category)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        DB::transaction(function () use ($category): void {
            $lockedCategory = Category::query()->lockForUpdate()->findOrFail($category->id);
            $lockedCategory->update(['status' => 2]);

            if ($lockedCategory->parent_id === 0) {
                $lockedCategory->children()->where('status', 1)->update(['status' => 2]);
            }
        });

        return to_route('admin.categories.index')
            ->with('success', 'Category deactivated successfully.');
    }

    /** @return Collection<int, Category> */
    private function parentCategories(?Category $exclude = null, ?int $includeParentId = null): Collection
    {
        return Category::query()
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
}
