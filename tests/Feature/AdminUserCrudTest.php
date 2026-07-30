<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Resume;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionGroupSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function makeFrontendUserCrudAdmin(array $attributes = []): AdminUser
{
    return AdminUser::factory()
        ->for(Role::factory())
        ->super()
        ->create($attributes);
}

function makeFrontendUserPermission(
    string $metaName,
    ?Permission $parent = null,
): Permission {
    $group = $parent?->group ?? PermissionGroup::query()->create([
        'name' => 'User Management '.fake()->unique()->numerify('####'),
        'type' => 1,
        'status' => 1,
    ]);

    return Permission::query()->create([
        'group_id' => $group->id,
        'parent_id' => $parent?->id ?? 0,
        'name' => str($metaName)->headline()->toString(),
        'meta_name' => $metaName,
        'type' => 1,
        'status' => 1,
    ]);
}

test('authorized administrator can browse search and filter website users', function (): void {
    $admin = makeFrontendUserCrudAdmin();
    User::factory()->create(['name' => 'Amina Verified', 'email' => 'amina@example.com']);
    User::factory()->unverified()->create(['name' => 'Daniel Pending', 'email' => 'daniel@example.com']);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.users.index', ['search' => 'Amina', 'verification' => 'verified']))
        ->assertOk()
        ->assertSee('User directory')
        ->assertSee('Amina Verified')
        ->assertDontSee('Daniel Pending')
        ->assertSee(route('admin.users.create'), false);
});

test('user management is visible in the sidebar with its view permission', function (): void {
    $permission = makeFrontendUserPermission('users');
    $role = Role::factory()->create();
    $role->permissions()->attach($permission);
    $admin = AdminUser::factory()->for($role)->create(['is_super' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('User management')
        ->assertSee(route('admin.users.index'), false);
});

test('administrator without user permission cannot access the user directory', function (): void {
    $admin = makeFrontendUserCrudAdmin(['is_super' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('administrator can create a verified user with a securely hashed password', function (): void {
    $admin = makeFrontendUserCrudAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.users.store'), [
            'name' => '  Nadia Rahman  ',
            'email' => 'NADIA@EXAMPLE.COM',
            'verification_status' => 1,
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])
        ->assertSessionHasNoErrors();

    $user = User::query()->where('email', 'nadia@example.com')->firstOrFail();

    expect($user->name)->toBe('Nadia Rahman')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('secure-password', $user->password))->toBeTrue()
        ->and($user->password)->not->toBe('secure-password');

    $this->assertAuthenticatedAs($admin, 'admin');
});

test('administrator can view a user and their saved resumes', function (): void {
    $admin = makeFrontendUserCrudAdmin();
    $user = User::factory()->create(['name' => 'Resume Owner']);
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'content' => [],
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.users.show', $user))
        ->assertOk()
        ->assertSee('Resume Owner')
        ->assertSee('Professional Cyan')
        ->assertSeeText('1 saved resume');
});

test('updating a user preserves their password and controls verification state', function (): void {
    $admin = makeFrontendUserCrudAdmin();
    $user = User::factory()->create([
        'password' => Hash::make('original-password'),
        'email_verified_at' => now(),
    ]);
    $originalHash = $user->password;

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.users.update', $user), [
            'name' => 'Updated Website User',
            'email' => 'updated-user@example.com',
            'verification_status' => 2,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.show', $user));

    expect($user->refresh()->name)->toBe('Updated Website User')
        ->and($user->email)->toBe('updated-user@example.com')
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->password)->toBe($originalHash);
});

test('user password is changed only through the dedicated password action', function (): void {
    $admin = makeFrontendUserCrudAdmin();
    $user = User::factory()->create();

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.users.password.update', $user), [
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.show', $user));

    expect(Hash::check('replacement-password', $user->refresh()->password))->toBeTrue();
});

test('duplicate website user email is rejected', function (): void {
    $admin = makeFrontendUserCrudAdmin();
    $existing = User::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.users.store'), [
            'name' => 'Duplicate User',
            'email' => strtoupper($existing->email),
            'verification_status' => 2,
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])
        ->assertSessionHasErrors(['email']);
});

test('deleting a user permanently removes their associated resume data', function (): void {
    $admin = makeFrontendUserCrudAdmin();
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'content' => [],
    ]);
    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => 'stale-reset-token',
        'created_at' => now(),
    ]);
    DB::table('sessions')->insert([
        'id' => 'deleted-user-session',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest',
        'payload' => 'session-data',
        'last_activity' => now()->timestamp,
    ]);

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertDatabaseMissing('resumes', ['id' => $resume->id]);
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
});

test('user management permission seeding is complete and idempotent', function (): void {
    $this->seed(PermissionGroupSeeder::class);
    $this->seed(PermissionSeeder::class);
    $this->seed(PermissionGroupSeeder::class);
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('meta_name', ['users', 'users-create', 'users-update', 'users-delete'])
        ->count())->toBe(4);
});
