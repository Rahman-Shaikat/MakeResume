<?php

use App\Mail\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('stores a valid contact message and sends it to support', function (): void {
    Mail::fake();

    $this->post(route('contact.store'), [
        'name' => 'Alex Morgan',
        'email' => 'alex@example.com',
        'topic' => 'account',
        'message' => 'I need help accessing the resume that I created yesterday.',
    ])
        ->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'Alex Morgan',
        'email' => 'alex@example.com',
        'topic' => 'account',
    ]);

    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail): bool {
        return $mail->hasTo((string) config('contact.recipient_address'))
            && $mail->contactMessage->email === 'alex@example.com'
            && $mail->contactMessage->topic === 'account';
    });
});

it('validates required contact-message fields without sending mail', function (): void {
    Mail::fake();

    $this->from(route('contact'))
        ->post(route('contact.store'), [])
        ->assertRedirect(route('contact'))
        ->assertSessionHasErrors(['name', 'email', 'topic', 'message']);

    $this->assertDatabaseCount('contact_messages', 0);
    Mail::assertNothingSent();
});

it('rejects contact form submissions that fill the honeypot field', function (): void {
    Mail::fake();

    $this->from(route('contact'))
        ->post(route('contact.store'), [
            'name' => 'Alex Morgan',
            'email' => 'alex@example.com',
            'topic' => 'feedback',
            'message' => 'I would like to share some feedback about the resume builder.',
            'website' => 'https://spam.example',
        ])
        ->assertRedirect(route('contact'))
        ->assertSessionHasErrors('website');

    $this->assertDatabaseCount('contact_messages', 0);
    Mail::assertNothingSent();
});
