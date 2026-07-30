<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\HomepageHeroFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'resume_template_id',
    'preview_image_path',
    'eyebrow',
    'headline',
    'description',
    'top_badge',
    'editor_title',
    'editor_description',
    'bottom_status_title',
    'bottom_status_text',
    'status',
])]
final class HomepageHero extends Model
{
    /** @use HasFactory<HomepageHeroFactory> */
    use HasFactory;

    protected static function newFactory(): HomepageHeroFactory
    {
        return HomepageHeroFactory::new();
    }

    public function resumeTemplate(): BelongsTo
    {
        return $this->belongsTo(ResumeTemplate::class);
    }

    /**
     * @param  Builder<HomepageHero>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }

    public function previewUrl(): ?string
    {
        if (! $this->preview_image_path) {
            return null;
        }

        if (Str::startsWith($this->preview_image_path, 'assets/')) {
            return asset($this->preview_image_path);
        }

        return Storage::disk('public')->url($this->preview_image_path);
    }

    protected function casts(): array
    {
        return [
            'resume_template_id' => 'integer',
            'status' => 'integer',
        ];
    }
}
