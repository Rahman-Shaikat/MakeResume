<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'short_desc', 'type', 'status'])]
class PermissionGroup extends Model
{
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'group_id');
    }

    public function parentPermissions(): HasMany
    {
        return $this->permissions()->where('parent_id', 0);
    }

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'status' => 'integer',
        ];
    }
}
