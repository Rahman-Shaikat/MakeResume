<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PackageResumeAllowanceProvider;
use App\Models\User;
use App\ValueObjects\ResumeAllowance;

final class NullPackageResumeAllowanceProvider implements PackageResumeAllowanceProvider
{
    public function allowanceFor(User $user): ?ResumeAllowance
    {
        return null;
    }
}
