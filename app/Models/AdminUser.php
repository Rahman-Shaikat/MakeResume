<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AdminUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'role_id',
    'country_id',
    'name',
    'email',
    'address',
    'gender',
    'phone',
    'image_path',
    'password',
    'status',
    'is_super',
    'email_verified_at',
])]
#[Hidden(['password', 'remember_token'])]
class AdminUser extends Authenticatable
{
    /** @use HasFactory<AdminUserFactory> */
    use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_super === 1) {
            return true;
        }

        if (! $this->relationLoaded('role')) {
            $this->load('role.permissions');
        } elseif ($this->role && ! $this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        if (! $this->role || $this->role->status !== 1 || $this->role->type !== 1) {
            return false;
        }

        return $this->role?->permissions
            ->contains(fn (Permission $item): bool => $item->status === 1 && $item->meta_name === $permission) ?? false;
    }

    /**
     * @param  Builder<AdminUser>  $query
     * @param  array{search?: string|null}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $query, string $search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });
    }

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'gender' => 'integer',
            'status' => 'integer',
            'is_super' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
