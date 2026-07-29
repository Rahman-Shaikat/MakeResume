<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ResumeSection;
use App\Models\User;

final class ResumeSectionPolicy
{
    public function update(User $user, ResumeSection $section): bool
    {
        return $section->resume()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, ResumeSection $section): bool
    {
        return $section->is_custom && $this->update($user, $section);
    }
}
