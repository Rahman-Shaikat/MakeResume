<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'parent_id',
    'name',
    'slug',
    'short_desc',
    'status',
    'position',
    'is_featured',
])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->orderBy('name');
    }

    public function resumeTemplates(): BelongsToMany
    {
        return $this->belongsToMany(ResumeTemplate::class);
    }

    /**
     * @param  Builder<Category>  $query
     * @param  array{search?: string|null, status?: string|null, level?: string|null}  $filters
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
                ($filters['level'] ?? null) === 'parent',
                fn (Builder $query) => $query->where('parent_id', 0),
            )
            ->when(
                ($filters['level'] ?? null) === 'subcategory',
                fn (Builder $query) => $query->where('parent_id', '>', 0),
            );
    }

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'status' => 'integer',
            'position' => 'integer',
            'is_featured' => 'integer',
        ];
    }
}
