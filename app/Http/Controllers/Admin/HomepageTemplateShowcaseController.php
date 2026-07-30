<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomepageTemplateShowcaseRequest;
use App\Http\Requests\Admin\UpdateHomepageTemplateShowcaseRequest;
use App\Models\HomepageTemplateShowcase;
use App\Services\Admin\HomepageTemplateShowcaseCrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class HomepageTemplateShowcaseController extends Controller
{
    public function index(HomepageTemplateShowcaseCrudService $service): View
    {
        return view('admin.homepage-template-showcases.index', $service->indexData());
    }

    public function create(HomepageTemplateShowcaseCrudService $service): View
    {
        return view('admin.homepage-template-showcases.create', $service->createData());
    }

    public function store(
        StoreHomepageTemplateShowcaseRequest $request,
        HomepageTemplateShowcaseCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated());

        return to_route('admin.homepage-template-showcases.edit', $result['model'])
            ->with('success', $result['message']);
    }

    public function edit(
        HomepageTemplateShowcase $homepageTemplateShowcase,
        HomepageTemplateShowcaseCrudService $service,
    ): View {
        return view(
            'admin.homepage-template-showcases.edit',
            $service->editData($homepageTemplateShowcase),
        );
    }

    public function update(
        UpdateHomepageTemplateShowcaseRequest $request,
        HomepageTemplateShowcase $homepageTemplateShowcase,
        HomepageTemplateShowcaseCrudService $service,
    ): RedirectResponse {
        $result = $service->update($homepageTemplateShowcase, $request->validated());

        return to_route('admin.homepage-template-showcases.edit', $homepageTemplateShowcase)
            ->with('success', $result['message']);
    }

    public function destroy(
        HomepageTemplateShowcase $homepageTemplateShowcase,
        HomepageTemplateShowcaseCrudService $service,
    ): RedirectResponse {
        $result = $service->delete($homepageTemplateShowcase);

        return to_route('admin.homepage-template-showcases.index')->with('success', $result['message']);
    }
}
