<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\HomepageTemplateShowcaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'eyebrow', 'headline', 'cta_label', 'status'])]
final class HomepageTemplateShowcase extends Model
{
    /** @use HasFactory<HomepageTemplateShowcaseFactory> */
    use HasFactory;

    protected static function newFactory(): HomepageTemplateShowcaseFactory
    {
        return HomepageTemplateShowcaseFactory::new();
    }

    public function templates(): BelongsToMany
    {
        return $this->belongsToMany(ResumeTemplate::class)
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * @param  Builder<HomepageTemplateShowcase>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 1);
    }

    protected function casts(): array
    {
        return ['status' => 'integer'];
    }
}
