<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen is available to guests', function (): void {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create your account');
});

test('a guest can register and is redirected to the dashboard', function (): void {
    $response = $this->post(route('register'), [
        'name' => 'Taylor Morgan',
        'email' => 'taylor@example.com',
        'password' => 'Resume123',
        'password_confirmation' => 'Resume123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Taylor Morgan',
        'email' => 'taylor@example.com',
    ]);
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
