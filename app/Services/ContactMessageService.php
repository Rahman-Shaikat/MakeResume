<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

final class ContactMessageService
{
    /**
     * @param  array{name: string, email: string, topic: string, message: string}  $data
     */
    public function submit(array $data, ?string $ipAddress, ?string $userAgent): ContactMessage
    {
        $contactMessage = ContactMessage::query()->create([
            ...$data,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        Mail::to(
            (string) config('contact.recipient_address'),
            (string) config('contact.recipient_name'),
        )->send(new ContactMessageReceived($contactMessage));

        $contactMessage->forceFill(['delivered_at' => now()])->save();

        return $contactMessage;
    }
}
