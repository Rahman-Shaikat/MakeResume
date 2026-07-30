<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Http\Resources\ResumeBuilderResource;
use App\Models\Resume;
use App\Models\User;
use App\Services\Admin\AdminNotificationService;
use App\Services\ResumeBuilderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ResumeService
{
    public function __construct(
        private readonly ResumeBuilderService $builderService,
        private readonly AdminNotificationService $adminNotifications,
    ) {}

    public function create(User $user, string $templateSlug): Resume
    {
        $resume = $user->resumes()->create([
            'template_slug' => $templateSlug,
            'content' => [
                'full_name' => $user->name,
                'email' => $user->email,
            ],
        ]);

        $templateName = config("resume_templates.catalog.{$templateSlug}.name", $templateSlug);
        $this->adminNotifications->notifyActiveAdministrators(
            title: 'New resume created',
            message: "{$user->name} started the {$templateName} template.",
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
        $payload = (new ResumeBuilderResource($resume))->resolve();
        $payload['content'] = $this->contentFor($user, $resume, $resume->template_slug);

        return [
            'user' => $user,
            'resume' => $resume,
            'template' => config("resume_templates.catalog.{$resume->template_slug}"),
            'builderPayload' => $payload,
        ];
    }

    public function templateData(User $user, string $template, bool $embedded): array
    {
        abort_unless(array_key_exists($template, config('resume_templates.catalog')), 404);

        return [
            'user' => $user,
            'resume' => null,
            'sections' => collect(),
            'data' => config("resume_templates.catalog.{$template}.sample"),
            'content' => $this->contentFor($user, null, $template),
            'embedded' => $embedded,
        ];
    }

    public function previewData(User $user, Resume $resume, bool $embedded): array
    {
        $resume = $this->builderService->load($resume);
        $template = $resume->template_slug;

        abort_unless(array_key_exists($template, config('resume_templates.catalog')), 404);

        return [
            'user' => $user,
            'resume' => $resume,
            'sections' => $resume->sections,
            'data' => config("resume_templates.catalog.{$template}.sample"),
            'content' => $this->contentFor($user, $resume, $template),
            'embedded' => $embedded,
        ];
    }

    /** @return array<string, string> */
    private function contentFor(User $user, ?Resume $resume, string $templateSlug): array
    {
        $sample = config("resume_templates.catalog.{$templateSlug}.sample");

        return array_replace([
            'full_name' => $user->name,
            'professional_title' => $sample['title'],
            'email' => $user->email,
            'phone' => $sample['phone'],
            'location' => $sample['location'],
            'website' => '',
            'linkedin' => $sample['linkedin'],
            'github' => $sample['github'],
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
