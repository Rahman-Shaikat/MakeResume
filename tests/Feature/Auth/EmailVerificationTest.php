<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('unverified users are directed to the themed verification notice', function (): void {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('verification.notice'));

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertSee('Check your inbox')
        ->assertSee($user->email)
        ->assertSee('Resend verification email');
});

test('unverified users cannot use resume endpoints', function (): void {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->getJson(route('resume.builder'))
        ->assertForbidden()
        ->assertJsonPath('message', 'Your email address is not verified.');
});

test('a user can verify their email with a valid signed link', function (): void {
    Event::fake();
    $user = User::factory()->unverified()->create();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $this->actingAs($user)
        ->get($verificationUrl)
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status', 'Email verified successfully. Welcome to Resume Studio.');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(Verified::class);
});

test('verification links reject an invalid user hash', function (): void {
    $user = User::factory()->unverified()->create();
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('different@example.com')],
    );

    $this->actingAs($user)
        ->get($verificationUrl)
        ->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('an unverified user can request another verification email', function (): void {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect()
        ->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

test('verified users are not sent another verification email', function (): void {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect(route('dashboard'));

    Notification::assertNothingSent();
});

test('the verification email uses the professional resume studio template', function (): void {
    $user = User::factory()->unverified()->create([
        'name' => 'Taylor Morgan',
        'email' => 'taylor@example.com',
    ]);
    $message = (new VerifyEmailNotification)->toMail($user);
    $html = view('emails.verify-email', $message->viewData)->render();

    expect($message->subject)
        ->toBe('Verify your email address | Resume Studio')
        ->and($message->view)->toBe([
            'html' => 'emails.verify-email',
            'text' => 'emails.verify-email-text',
        ])
        ->and($html)
        ->toContain('Resume<span style="color:#66dceb;">Studio</span>')
        ->toContain('assets/common/media/logo.png')
        ->toContain('Verify email address')
        ->toContain('taylor@example.com')
        ->toContain('signature=');
});
