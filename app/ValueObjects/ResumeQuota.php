<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class ResumeQuota
{
    public function __construct(
        public ResumeAllowance $allowance,
        public int $used,
    ) {}

    public function canCreate(): bool
    {
        return $this->allowance->isUnlimited() || $this->used < $this->allowance->limit;
    }

    public function usageLabel(): string
    {
        return number_format($this->used).' / '.$this->allowance->allowanceLabel();
    }

    public function sourceLabel(): string
    {
        return $this->allowance->source;
    }

    public function limitReachedMessage(): string
    {
        if ($this->allowance->isUnlimited()) {
            return '';
        }

        if ($this->allowance->limit === 0) {
            return 'New resume creation is currently disabled for your account.';
        }

        if ($this->used > $this->allowance->limit) {
            return "Your workspace has {$this->used} saved resumes and is over its {$this->allowance->limit}-resume allowance. Delete a saved resume to create another one.";
        }

        return "You have reached your {$this->allowance->limit}-resume allowance. Delete a saved resume to create another one.";
    }
}
