<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the protected admin dashboard template is available to an active administrator', function (): void {
    $role = Role::factory()->create();
    $admin = AdminUser::factory()->for($role)->super()->create();

    $this->actingAs($admin, 'admin');

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Resume Studio')
        ->assertSee('Administration')
        ->assertSee('Platform overview')
        ->assertSee('Resume creation overview')
        ->assertSee('Popular templates')
        ->assertSee('Recent users')
        ->assertSee('data-admin-sidebar', false)
        ->assertSee('data-admin-content', false)
        ->assertSee('/build/assets/', false);
});

test('the generic admin root does not reveal the protected admin route', function (): void {
    $this->get('/admin')->assertNotFound();
    $this->get('/admin/')->assertNotFound();
});

test('the standalone admin login template is available', function (): void {
    $this->get(route('admin.loginpage'))
        ->assertOk()
        ->assertSee('Protected area')
        ->assertSee('Sign in to administration');
});
