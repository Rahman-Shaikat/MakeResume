<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Exceptions\ResumeLimitReached;
use App\Http\Resources\ResumeBuilderResource;
use App\Models\Resume;
use App\Models\User;
use App\Services\Admin\AdminNotificationService;
use App\Services\ResumeAllowanceResolver;
use App\Services\ResumeBuilderService;
use App\Services\TemplateCatalogService;
use App\Services\TemplateRendererRegistry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ResumeService
{
    public function __construct(
        private readonly ResumeBuilderService $builderService,
        private readonly AdminNotificationService $adminNotifications,
        private readonly TemplateCatalogService $catalog,
        private readonly TemplateRendererRegistry $renderers,
        private readonly ResumeAllowanceResolver $allowances,
    ) {}

    public function create(User $user, string $templateSlug): Resume
    {
        $template = $this->catalog->findActiveBySlug($templateSlug);

        $resume = DB::transaction(function () use ($user, $template): Resume {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $quota = $this->allowances->quotaFor($lockedUser, $lockedUser->resumes()->count());

            if (! $quota->canCreate()) {
                throw new ResumeLimitReached($quota);
            }

            return $lockedUser->resumes()->create([
                'template_slug' => $template->slug,
                'content' => [
                    'full_name' => $lockedUser->name,
                    'email' => $lockedUser->email,
                ],
            ]);
        });

        $this->adminNotifications->notifyActiveAdministrators(
            title: 'New resume created',
            message: "{$user->name} started the {$template->name} template.",
            url: route('admin.dashboard'),
            icon: 'document',
            tone: 'primary',
        );

        return $resume;
    }

    public function storeProfileImage(Resume $resume, UploadedFile $file): array
    {
        $filename = Str::uuid().'.'.$file->extension();
        $path = $file->storeAs('images', $filename, 'public');

        $this->deleteManagedProfileImage($resume->profile_image);
        $resume->update(['profile_image' => $path]);

        return [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ];
    }

    public function removeProfileImage(Resume $resume): void
    {
        $this->deleteManagedProfileImage($resume->profile_image);
        $resume->update(['profile_image' => null]);
    }

    public function delete(Resume $resume): void
    {
        $profileImage = $resume->profile_image;
        $resume->delete();
        $this->deleteManagedProfileImage($profileImage);
    }

    public function builderData(User $user, Resume $resume): array
    {
        $resume = $this->builderService->load($resume);
        $template = $this->catalog->findBySlug($resume->template_slug);
        $payload = (new ResumeBuilderResource($resume))->resolve();
        $payload['content'] = $this->contentFor($user, $resume, $template->renderer_key);

        return [
            'user' => $user,
            'resume' => $resume,
            'template' => $template,
            'builderPayload' => $payload,
        ];
    }

    public function templateData(User $user, string $templateSlug, bool $embedded): array
    {
        $template = $this->catalog->findActiveBySlug($templateSlug);
        abort_unless($this->renderers->has($template->renderer_key), 404);
        $sample = $this->renderers->sample($template->renderer_key);

        return [
            'user' => $user,
            'resume' => null,
            'resumeTemplate' => $template,
            'sections' => collect(),
            'data' => $sample,
            'content' => $this->contentFor($user, null, $template->renderer_key),
            'embedded' => $embedded,
            'view' => $this->renderers->view($template->renderer_key),
        ];
    }

    public function previewData(User $user, Resume $resume, bool $embedded): array
    {
        $resume = $this->builderService->load($resume);
        $template = $this->catalog->findBySlug($resume->template_slug);
        abort_unless($this->renderers->has($template->renderer_key), 404);
        $sample = $this->renderers->sample($template->renderer_key);

        return [
            'user' => $user,
            'resume' => $resume,
            'resumeTemplate' => $template,
            'sections' => $resume->sections,
            'data' => $sample,
            'content' => $this->contentFor($user, $resume, $template->renderer_key),
            'embedded' => $embedded,
            'view' => $this->renderers->view($template->renderer_key),
        ];
    }

    public function switchTemplate(Resume $resume, string $templateSlug): Resume
    {
        $template = $this->catalog->findActiveBySlug($templateSlug);
        $resume->update(['template_slug' => $template->slug]);

        return $resume->refresh();
    }

    /** @return array<string, mixed> */
    private function contentFor(User $user, ?Resume $resume, string $templateSlug): array
    {
        $sample = $this->renderers->sample($templateSlug);

        return array_replace([
            'full_name' => $user->name,
            'professional_title' => $sample['title'],
            'email' => $user->email,
            'phone' => $sample['phone'],
            'location' => $sample['location'],
            'website' => '',
            'linkedin' => $sample['linkedin'],
            'github' => $sample['github'],
            'social_links' => [],
            'summary' => $sample['summary'],
        ], $resume?->content ?? []);
    }

    private function deleteManagedProfileImage(?string $path): void
    {
        if ($path && Str::startsWith($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
