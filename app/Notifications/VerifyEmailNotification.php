<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

final class VerifyEmailNotification extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes($this->expirationMinutes()),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );

        $viewData = [
            'user' => $notifiable,
            'verificationUrl' => $verificationUrl,
            'expirationMinutes' => $this->expirationMinutes(),
        ];

        return (new MailMessage)
            ->subject('Verify your email address | Resume Studio')
            ->view('emails.verify-email', $viewData)
            ->text('emails.verify-email-text', $viewData);
    }

    private function expirationMinutes(): int
    {
        return (int) Config::get('auth.verification.expire', 60);
    }
}
