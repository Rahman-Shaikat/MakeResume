<?php

declare(strict_types=1);

namespace App\Enums;

enum ResumeLimitMode: string
{
    case Inherit = 'inherit';
    case Limited = 'limited';
    case Unlimited = 'unlimited';
}
