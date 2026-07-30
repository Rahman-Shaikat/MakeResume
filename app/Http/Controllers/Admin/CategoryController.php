<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderCategoriesRequest;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryCrudService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CategoryController extends Controller
{
    public function index(Request $request, CategoryCrudService $service): View
    {
        return view(
            'admin.categories.index',
            $service->indexData($request->only(['search', 'status', 'level'])),
        );
    }

    public function create(CategoryCrudService $service): View
    {
        return view('admin.categories.create', $service->createData());
    }

    public function store(
        StoreCategoryRequest $request,
        CategoryCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated());

        return to_route('admin.categories.edit', $result['model'])
            ->with('success', $result['message']);
    }

    public function edit(Category $category, CategoryCrudService $service): View
    {
        return view('admin.categories.edit', $service->editData($category));
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category,
        CategoryCrudService $service,
    ): RedirectResponse {
        $result = $service->update($category, $request->validated());

        return to_route('admin.categories.edit', $category)
            ->with('success', $result['message']);
    }

    public function reorder(
        ReorderCategoriesRequest $request,
        CategoryCrudService $service,
    ): JsonResponse {
        $result = $service->reorder($request->validated('category_ids'));

        return response()->json(['message' => $result['message']]);
    }

    public function destroy(Category $category, CategoryCrudService $service): RedirectResponse
    {
        $result = $service->delete($category);

        return to_route('admin.categories.index')
            ->with('success', $result['message']);
    }
}
