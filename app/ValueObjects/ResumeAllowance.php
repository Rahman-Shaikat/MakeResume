<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class ResumeAllowance
{
    public function __construct(
        public ?int $limit,
        public string $source,
    ) {}

    public static function limited(int $limit, string $source): self
    {
        return new self($limit, $source);
    }

    public static function unlimited(string $source): self
    {
        return new self(null, $source);
    }

    public function isUnlimited(): bool
    {
        return $this->limit === null;
    }

    public function allowanceLabel(): string
    {
        return $this->isUnlimited() ? 'Unlimited' : number_format($this->limit);
    }
}
