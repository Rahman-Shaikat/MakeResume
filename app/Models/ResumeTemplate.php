<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ResumeTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LogicException;

#[Fillable([
    'slug',
    'renderer_key',
    'name',
    'short_desc',
    'thumbnail_path',
    'accent_color',
    'allows_profile_photo',
    'is_ats_friendly',
    'is_featured',
    'status',
    'position',
])]
final class ResumeTemplate extends Model
{
    /** @use HasFactory<ResumeTemplateFactory> */
    use HasFactory;

    protected static function newFactory(): ResumeTemplateFactory
    {
        return ResumeTemplateFactory::new();
    }

    protected static function booted(): void
    {
        self::updating(function (ResumeTemplate $template): void {
            if ($template->isDirty('slug')) {
                throw new LogicException('Resume template slugs are immutable.');
            }

            if ($template->isDirty('renderer_key') && $template->resumes()->exists()) {
                throw new LogicException('A renderer cannot change after the template is used by a resume.');
            }
        });

        self::deleting(function (ResumeTemplate $template): void {
            if ($template->resumes()->exists()) {
                throw new LogicException('A resume template in use cannot be deleted.');
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class, 'template_slug', 'slug');
    }

    /**
     * @param  Builder<ResumeTemplate>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }

    /**
     * @param  Builder<ResumeTemplate>  $query
     * @param  array{search?: string|null, status?: string|null, category?: string|null, ats?: string|null, renderer?: string|null}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(
                isset($filters['status']) && in_array((int) $filters['status'], [1, 2], true),
                fn (Builder $query) => $query->where('status', (int) $filters['status']),
            )
            ->when(
                isset($filters['ats']) && in_array((int) $filters['ats'], [1, 2], true),
                fn (Builder $query) => $query->where('is_ats_friendly', (int) $filters['ats']),
            )
            ->when(
                $filters['renderer'] ?? null,
                fn (Builder $query, string $renderer) => $query->where('renderer_key', $renderer),
            )
            ->when(
                $filters['category'] ?? null,
                fn (Builder $query, string $category) => $query->whereHas(
                    'categories',
                    fn (Builder $query) => $query->where('categories.slug', $category),
                ),
            );
    }

    public function thumbnailUrl(): ?string
    {
        if (! $this->thumbnail_path) {
            return null;
        }

        if (Str::startsWith($this->thumbnail_path, 'assets/')) {
            return asset($this->thumbnail_path);
        }

        return Storage::disk('public')->url($this->thumbnail_path);
    }

    protected function casts(): array
    {
        return [
            'allows_profile_photo' => 'integer',
            'is_ats_friendly' => 'integer',
            'is_featured' => 'integer',
            'status' => 'integer',
            'position' => 'integer',
        ];
    }
}
