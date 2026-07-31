<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\ValueObjects\ResumeQuota;
use RuntimeException;

final class ResumeLimitReached extends RuntimeException
{
    public function __construct(public readonly ResumeQuota $quota)
    {
        parent::__construct($quota->limitReachedMessage());
    }
}
