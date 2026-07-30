<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\HomepageHeroService;
use App\Services\HomepageTemplateShowcaseService;
use App\Services\TemplateCatalogService;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(
        TemplateCatalogService $catalog,
        HomepageHeroService $heroes,
        HomepageTemplateShowcaseService $showcases,
    ): View {
        $showcase = $showcases->active();
        $showcaseTemplates = $showcase?->templates ?? collect();
        $templates = $showcaseTemplates
            ->concat(
                $catalog->homepageTemplates(4)->reject(
                    fn ($template): bool => $showcaseTemplates->contains('id', $template->id),
                ),
            )
            ->take(4)
            ->values();

        return view('pages.home', [
            'templates' => $templates,
            'hero' => $heroes->active(),
            'showcase' => $showcase,
        ]);
    }
}
