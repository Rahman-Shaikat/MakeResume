<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are directed to login from the home page', function (): void {
    $this->get('/')
        ->assertRedirect(route('login'));
});

test('authenticated users are directed to their dashboard from the home page', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertRedirect(route('dashboard'));
});
