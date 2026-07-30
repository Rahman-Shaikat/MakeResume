<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HomepageTemplateShowcase;

final class HomepageTemplateShowcaseService
{
    public function active(): ?HomepageTemplateShowcase
    {
        return HomepageTemplateShowcase::query()
            ->active()
            ->with([
                'templates' => fn ($query) => $query
                    ->active()
                    ->select(['resume_templates.id', 'name', 'short_desc', 'thumbnail_path', 'accent_color', 'is_ats_friendly']),
            ])
            ->latest('updated_at')
            ->first();
    }
}
