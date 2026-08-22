<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ResumeTemplate;
use App\Models\User;
use Illuminate\Support\Collection;

final class PublicTemplatePreviewService
{
    public function __construct(
        private readonly TemplateCatalogService $catalog,
        private readonly TemplateRendererRegistry $renderers,
    ) {}

    /**
     * @return array{
     *     user: User,
     *     resume: null,
     *     resumeTemplate: ResumeTemplate,
     *     sections: Collection<int, never>,
     *     data: array<string, mixed>,
     *     content: array<string, string>,
     *     embedded: true,
     *     view: string
     * }
     */
    public function data(string $templateSlug): array
    {
        $template = $this->catalog->findActiveBySlug($templateSlug);
        abort_unless($this->renderers->has($template->renderer_key), 404);

        $sample = $this->renderers->sample($template->renderer_key);
        $user = new User;
        $user->forceFill([
            'name' => 'Alex Morgan',
            'email' => 'alex.morgan@example.com',
        ]);

        return [
            'user' => $user,
            'resume' => null,
            'resumeTemplate' => $template,
            'sections' => collect(),
            'data' => $sample,
            'content' => [
                'full_name' => $user->name,
                'professional_title' => (string) $sample['title'],
                'email' => $user->email,
                'phone' => (string) $sample['phone'],
                'location' => (string) $sample['location'],
                'website' => '',
                'linkedin' => (string) $sample['linkedin'],
                'github' => (string) $sample['github'],
                'social_links' => [],
                'summary' => (string) $sample['summary'],
            ],
            'embedded' => true,
            'view' => $this->renderers->view($template->renderer_key),
        ];
    }
}
