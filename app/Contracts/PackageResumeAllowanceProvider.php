<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use App\ValueObjects\ResumeAllowance;

interface PackageResumeAllowanceProvider
{
    /**
     * Return null when the user has no active package entitlement.
     */
    public function allowanceFor(User $user): ?ResumeAllowance;
}
