<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PackageResumeAllowanceProvider;
use App\Enums\ResumeLimitMode;
use App\Models\User;
use App\ValueObjects\ResumeAllowance;
use App\ValueObjects\ResumeQuota;

final class ResumeAllowanceResolver
{
    public function __construct(
        private readonly PackageResumeAllowanceProvider $packageAllowances,
    ) {}

    public function allowanceFor(User $user): ResumeAllowance
    {
        return match ($user->resume_limit_mode) {
            ResumeLimitMode::Unlimited => ResumeAllowance::unlimited('Admin override'),
            ResumeLimitMode::Limited => ResumeAllowance::limited(
                $user->resume_limit ?? 0,
                'Admin limit',
            ),
            ResumeLimitMode::Inherit => $this->packageAllowances->allowanceFor($user) ?? $this->freeAllowance(),
        };
    }

    public function quotaFor(User $user, int $used): ResumeQuota
    {
        return new ResumeQuota($this->allowanceFor($user), $used);
    }

    public function freeAllowance(): ResumeAllowance
    {
        return ResumeAllowance::limited(
            (int) config('resume_limits.free.resume_limit', 1),
            (string) config('resume_limits.free.label', 'Free'),
        );
    }
}
