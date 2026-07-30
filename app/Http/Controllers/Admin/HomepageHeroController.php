<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomepageHeroRequest;
use App\Http\Requests\Admin\UpdateHomepageHeroRequest;
use App\Models\HomepageHero;
use App\Services\Admin\HomepageHeroCrudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class HomepageHeroController extends Controller
{
    public function index(HomepageHeroCrudService $service): View
    {
        return view('admin.homepage-heroes.index', $service->indexData());
    }

    public function create(HomepageHeroCrudService $service): View
    {
        return view('admin.homepage-heroes.create', $service->createData());
    }

    public function store(
        StoreHomepageHeroRequest $request,
        HomepageHeroCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated(), $request->file('preview_image'));

        return to_route('admin.homepage-heroes.edit', $result['model'])
            ->with('success', $result['message']);
    }

    public function edit(HomepageHero $homepageHero, HomepageHeroCrudService $service): View
    {
        return view('admin.homepage-heroes.edit', $service->editData($homepageHero));
    }

    public function update(
        UpdateHomepageHeroRequest $request,
        HomepageHero $homepageHero,
        HomepageHeroCrudService $service,
    ): RedirectResponse {
        $result = $service->update($homepageHero, $request->validated(), $request->file('preview_image'));

        return to_route('admin.homepage-heroes.edit', $homepageHero)
            ->with('success', $result['message']);
    }

    public function destroy(HomepageHero $homepageHero, HomepageHeroCrudService $service): RedirectResponse
    {
        $result = $service->delete($homepageHero);

        return to_route('admin.homepage-heroes.index')->with('success', $result['message']);
    }
}
