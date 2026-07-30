<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\HomepageHero;
use App\Models\ResumeTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

final class HomepageHeroCrudService
{
    public function indexData(): array
    {
        return [
            'heroes' => HomepageHero::query()
                ->with('resumeTemplate:id,name,thumbnail_path')
                ->latest('updated_at')
                ->get(),
        ];
    }

    public function createData(): array
    {
        return [
            'resumeTemplates' => $this->resumeTemplates(),
            'statusOptions' => $this->statusOptions(),
        ];
    }

    public function editData(HomepageHero $hero): array
    {
        $hero->load('resumeTemplate:id,name,thumbnail_path');

        return [
            'homepageHero' => $hero,
            'resumeTemplates' => $this->resumeTemplates($hero),
            'statusOptions' => $this->statusOptions(),
        ];
    }

    public function store(array $data, ?UploadedFile $previewImage): array
    {
        $previewImagePath = $previewImage ? $this->storePreviewImage($previewImage) : null;

        try {
            $hero = DB::transaction(function () use ($data, $previewImagePath): HomepageHero {
                unset($data['preview_image'], $data['remove_preview_image']);

                if ((int) $data['status'] === 1) {
                    HomepageHero::query()->active()->lockForUpdate()->update(['status' => 2]);
                }

                return HomepageHero::query()->create([
                    ...$data,
                    'preview_image_path' => $previewImagePath,
                ]);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedPreviewImage($previewImagePath);
            throw $exception;
        }

        return ['success' => true, 'message' => 'Homepage hero created successfully.', 'model' => $hero];
    }

    public function update(HomepageHero $hero, array $data, ?UploadedFile $previewImage): array
    {
        $newPreviewImagePath = $previewImage ? $this->storePreviewImage($previewImage) : null;
        $oldPreviewImagePath = $hero->preview_image_path;
        $removePreviewImage = (bool) ($data['remove_preview_image'] ?? false);

        try {
            DB::transaction(function () use ($hero, $data, $newPreviewImagePath, $removePreviewImage): void {
                unset($data['preview_image'], $data['remove_preview_image']);

                if ((int) $data['status'] === 1) {
                    HomepageHero::query()
                        ->active()
                        ->whereKeyNot($hero->id)
                        ->lockForUpdate()
                        ->update(['status' => 2]);
                }

                HomepageHero::query()
                    ->lockForUpdate()
                    ->findOrFail($hero->id)
                    ->update([
                        ...$data,
                        ...($newPreviewImagePath
                            ? ['preview_image_path' => $newPreviewImagePath]
                            : ($removePreviewImage ? ['preview_image_path' => null] : [])),
                    ]);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedPreviewImage($newPreviewImagePath);
            throw $exception;
        }

        if ($newPreviewImagePath || $removePreviewImage) {
            $this->deleteManagedPreviewImage($oldPreviewImagePath);
        }

        return ['success' => true, 'message' => 'Homepage hero updated successfully.', 'model' => $hero->refresh()];
    }

    public function delete(HomepageHero $hero): array
    {
        $previewImagePath = $hero->preview_image_path;
        $hero->delete();
        $this->deleteManagedPreviewImage($previewImagePath);

        return ['success' => true, 'message' => 'Homepage hero deleted successfully.'];
    }

    /** @return Collection<int, ResumeTemplate> */
    private function resumeTemplates(?HomepageHero $hero = null): Collection
    {
        return ResumeTemplate::query()
            ->select(['id', 'name', 'thumbnail_path', 'status'])
            ->where(function ($query) use ($hero): void {
                $query->where('status', 1)
                    ->when($hero?->resume_template_id, fn ($query) => $query->orWhere('id', $hero->resume_template_id));
            })
            ->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END')
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    private function storePreviewImage(UploadedFile $file): string
    {
        $extension = match ($file->getMimeType()) {
            'image/webp' => 'webp',
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            default => throw ValidationException::withMessages([
                'preview_image' => 'Only genuine WebP, PNG, and JPEG images are allowed.',
            ]),
        };

        return $file->storeAs('homepage-heroes', Str::uuid().".{$extension}", 'public');
    }

    private function deleteManagedPreviewImage(?string $path): void
    {
        if ($path && Str::startsWith($path, 'homepage-heroes/')) {
            Storage::disk('public')->delete($path);
        }
    }

    /** @return list<array{value: int, label: string, description: string}> */
    private function statusOptions(): array
    {
        return [
            ['value' => 1, 'label' => 'Active', 'description' => 'Publish this hero on the public homepage'],
            ['value' => 2, 'label' => 'Inactive', 'description' => 'Keep this configuration as a draft'],
        ];
    }
}
