<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('registration screen is available to guests', function (): void {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create your account')
        ->assertSee('assets/common/media/logo.png')
        ->assertSee('assets/common/media/favicon.png');
});

test('a guest can register and receives a verification email', function (): void {
    Notification::fake();

    $response = $this->post(route('register'), [
        'name' => 'Taylor Morgan',
        'email' => 'taylor@example.com',
        'password' => 'Resume123',
        'password_confirmation' => 'Resume123',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Taylor Morgan',
        'email' => 'taylor@example.com',
        'email_verified_at' => null,
    ]);

    Notification::assertSentTo(
        User::query()->where('email', 'taylor@example.com')->firstOrFail(),
        VerifyEmailNotification::class,
    );
});

test('registration validates account details', function (): void {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->from(route('register'))->post(route('register'), [
        'name' => '',
        'email' => 'taken@example.com',
        'password' => 'short',
        'password_confirmation' => 'different',
    ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('a registered user can sign in', function (): void {
    $user = User::factory()->create([
        'email' => 'member@example.com',
        'password' => 'Resume123',
    ]);

    $this->post(route('login'), [
        'email' => 'member@example.com',
        'password' => 'Resume123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('invalid credentials are rejected', function (): void {
    User::factory()->create([
        'email' => 'member@example.com',
        'password' => 'Resume123',
    ]);

    $this->from(route('login'))->post(route('login'), [
        'email' => 'member@example.com',
        'password' => 'not-the-password',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an authenticated user can sign out', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('authenticated users cannot access guest authentication screens', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});
