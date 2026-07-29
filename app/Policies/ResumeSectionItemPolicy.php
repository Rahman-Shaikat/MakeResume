<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ResumeSectionItem;
use App\Models\User;

final class ResumeSectionItemPolicy
{
    public function update(User $user, ResumeSectionItem $item): bool
    {
        return $item->section()
            ->whereHas('resume', fn ($query) => $query->where('user_id', $user->id))
            ->exists();
    }

    public function delete(User $user, ResumeSectionItem $item): bool
    {
        return $this->update($user, $item);
    }
}
