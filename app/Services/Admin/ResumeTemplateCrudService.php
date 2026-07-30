<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\ResumeTemplate;
use App\Services\TemplateRendererRegistry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ResumeTemplateCrudService
{
    public function __construct(
        private readonly TemplateRendererRegistry $renderers,
    ) {}

    /** @param array{search?: string|null, status?: string|null, category?: string|null, ats?: string|null, renderer?: string|null} $filters */
    public function indexData(array $filters = []): array
    {
        $hasActiveFilters = collect($filters)->contains(fn ($value): bool => filled($value));

        return [
            'templates' => ResumeTemplate::query()
                ->filter($filters)
                ->with('categories:id,name,slug')
                ->withCount('resumes')
                ->orderBy('position')
                ->orderBy('name')
                ->get(),
            'categories' => $this->categories(),
            'rendererOptions' => $this->renderers->options(),
            'reorderEnabled' => ! $hasActiveFilters,
        ];
    }

    public function createData(): array
    {
        return [
            'categories' => $this->categories(),
            'rendererOptions' => $this->renderers->options(),
            ...$this->formOptions(),
        ];
    }

    public function editData(ResumeTemplate $template): array
    {
        $template->load('categories:id,name');

        return [
            'resumeTemplate' => $template,
            'categories' => $this->categories($template),
            'rendererOptions' => $this->renderers->options(),
            ...$this->formOptions(),
        ];
    }

    public function store(array $data, ?UploadedFile $thumbnail): array
    {
        $thumbnailPath = $thumbnail ? $this->storeThumbnail($thumbnail) : null;

        try {
            $template = DB::transaction(function () use ($data, $thumbnailPath): ResumeTemplate {
                $categoryIds = $data['category_ids'] ?? [];
                unset($data['category_ids'], $data['thumbnail']);

                $lastPosition = ResumeTemplate::query()->lockForUpdate()->max('position');
                $template = ResumeTemplate::query()->create([
                    ...$data,
                    'thumbnail_path' => $thumbnailPath,
                    'position' => is_null($lastPosition) ? 0 : ((int) $lastPosition + 1),
                ]);
                $template->categories()->sync($categoryIds);

                return $template;
            });
        } catch (Throwable $exception) {
            $this->deleteManagedThumbnail($thumbnailPath);
            throw $exception;
        }

        return ['success' => true, 'message' => 'Resume template created successfully.', 'model' => $template];
    }

    public function update(ResumeTemplate $template, array $data, ?UploadedFile $thumbnail): array
    {
        $newThumbnailPath = $thumbnail ? $this->storeThumbnail($thumbnail) : null;
        $oldThumbnailPath = $template->thumbnail_path;

        try {
            DB::transaction(function () use ($template, $data, $newThumbnailPath): void {
                $categoryIds = $data['category_ids'] ?? [];
                unset($data['category_ids'], $data['thumbnail']);

                $lockedTemplate = ResumeTemplate::query()->lockForUpdate()->findOrFail($template->id);
                $lockedTemplate->update([
                    ...$data,
                    ...($newThumbnailPath ? ['thumbnail_path' => $newThumbnailPath] : []),
                ]);
                $lockedTemplate->categories()->sync($categoryIds);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedThumbnail($newThumbnailPath);
            throw $exception;
        }

        if ($newThumbnailPath) {
            $this->deleteManagedThumbnail($oldThumbnailPath);
        }

        return ['success' => true, 'message' => 'Resume template updated successfully.', 'model' => $template->refresh()];
    }

    /** @param array<int, int|string> $templateIds */
    public function reorder(array $templateIds): array
    {
        $templateIds = collect($templateIds)->map(fn ($id): int => (int) $id)->values()->all();

        DB::transaction(function () use ($templateIds): void {
            $templates = ResumeTemplate::query()->lockForUpdate()->get()->keyBy('id');
            $existing = $templates->keys()->map(fn ($id): int => (int) $id)->sort()->values()->all();
            $submitted = collect($templateIds)->sort()->values()->all();

            if ($existing !== $submitted) {
                throw ValidationException::withMessages([
                    'template_ids' => 'The template list changed. Refresh the page and try again.',
                ]);
            }

            foreach ($templateIds as $position => $templateId) {
                $templates->get($templateId)?->update(['position' => $position]);
            }
        });

        return ['success' => true, 'message' => 'Template order updated successfully.'];
    }

    public function delete(ResumeTemplate $template): array
    {
        if ($template->resumes()->exists()) {
            return [
                'success' => false,
                'message' => 'This template is used by one or more resumes. Make it inactive instead.',
            ];
        }

        $thumbnailPath = $template->thumbnail_path;
        $template->delete();
        $this->deleteManagedThumbnail($thumbnailPath);

        return ['success' => true, 'message' => 'Resume template deleted successfully.'];
    }

    /**
     * @return Collection<int, Category>
     */
    private function categories(?ResumeTemplate $template = null): Collection
    {
        $assignedIds = $template?->categories->pluck('id')->all() ?? [];

        return Category::query()
            ->where(function ($query) use ($assignedIds): void {
                $query->where('status', 1)
                    ->when($assignedIds, fn ($query) => $query->orWhereIn('id', $assignedIds));
            })
            ->orderBy('parent_id')
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'status']);
    }

    private function storeThumbnail(UploadedFile $file): string
    {
        $extension = match ($file->getMimeType()) {
            'image/webp' => 'webp',
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            default => throw ValidationException::withMessages([
                'thumbnail' => 'Only genuine WebP, PNG, and JPEG images are allowed.',
            ]),
        };

        return $file->storeAs('resume-templates', Str::uuid().".{$extension}", 'public');
    }

    private function deleteManagedThumbnail(?string $path): void
    {
        if ($path && Str::startsWith($path, 'resume-templates/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function formOptions(): array
    {
        return [
            'statusOptions' => [
                ['value' => 1, 'label' => 'Active', 'description' => 'Available in the template gallery'],
                ['value' => 2, 'label' => 'Inactive', 'description' => 'Hidden from new selection and switching'],
            ],
            'yesNoOptions' => [
                ['value' => 1, 'label' => 'Yes', 'description' => 'Enabled'],
                ['value' => 2, 'label' => 'No', 'description' => 'Disabled'],
            ],
        ];
    }
}
