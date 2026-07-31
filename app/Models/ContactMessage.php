<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'topic',
    'message',
    'ip_address',
    'user_agent',
    'delivered_at',
])]
final class ContactMessage extends Model
{
    public function topicLabel(): string
    {
        return match ($this->topic) {
            'account' => 'Account or workspace support',
            'feedback' => 'Feedback or feature idea',
            'partnership' => 'Partnership opportunity',
            default => 'Other',
        };
    }

    protected function casts(): array
    {
        return [
            'delivered_at' => 'immutable_datetime',
        ];
    }
}
