<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HomepageHero;

final class HomepageHeroService
{
    public function active(): ?HomepageHero
    {
        return HomepageHero::query()
            ->active()
            ->with('resumeTemplate:id,name,thumbnail_path')
            ->latest('updated_at')
            ->first();
    }
}
