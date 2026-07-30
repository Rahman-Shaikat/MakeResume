<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createAdminPermission(string $metaName, string $name = 'Permission', ?Permission $parent = null): Permission
{
    $group = PermissionGroup::query()->firstOrCreate(
        ['name' => 'Administrator', 'type' => 1],
        ['status' => 1],
    );

    return Permission::query()->create([
        'group_id' => $group->id,
        'parent_id' => $parent?->id ?? 0,
        'name' => $name,
        'meta_name' => $metaName,
        'type' => 1,
        'status' => 1,
    ]);
}

function createAdministrator(array $attributes = [], array $permissions = []): AdminUser
{
    $role = Role::factory()->create();

    if ($permissions !== []) {
        $role->permissions()->attach($permissions);
    }

    return AdminUser::factory()->for($role)->create($attributes);
}

test('admin login succeeds with valid active credentials', function (): void {
    $admin = createAdministrator(['password' => Hash::make('secret-pass')]);

    $this->post(route('admin.login'), [
        'email' => $admin->email,
        'password' => 'secret-pass',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin, 'admin');
});

test('authenticated admin is redirected away from the admin login action', function (): void {
    $admin = createAdministrator();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.login'))
        ->assertRedirect(route('admin.dashboard'));
});

test('inactive admin login is blocked', function (): void {
    $admin = createAdministrator([
        'password' => Hash::make('secret-pass'),
        'status' => 2,
    ]);

    $this->post(route('admin.login'), [
        'email' => $admin->email,
        'password' => 'secret-pass',
    ])->assertSessionHasErrors(['email']);

    $this->assertGuest('admin');
});

test('admin can log out', function (): void {
    $admin = createAdministrator();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.loginpage'));

    $this->assertGuest('admin');
});

test('unauthenticated admin route redirects to admin login', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.loginpage'));
});

test('active administrator can access the dashboard', function (): void {
    $admin = createAdministrator();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('normal administrator without permission receives forbidden', function (): void {
    $admin = createAdministrator();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index'))
        ->assertForbidden();
});

test('normal administrator with exact permission can access a protected route', function (): void {
    $permission = createAdminPermission('roles', 'Roles');
    $admin = createAdministrator([], [$permission->id]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index'))
        ->assertOk();
});

test('super administrator bypasses permission checks', function (): void {
    $admin = createAdministrator(['is_super' => 1]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.roles.index'))
        ->assertOk();
});

test('creating a role stores unique permission assignments', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $parent = createAdminPermission('roles', 'Roles');
    $child = createAdminPermission('roles-create', 'Create', $parent);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.roles.store'), [
            'name' => 'Content Administrator',
            'short_desc' => 'Manages selected administration tools.',
            'type' => 1,
            'permission' => [$parent->id, $child->id, $child->id],
        ])
        ->assertSessionHasNoErrors();

    $role = Role::query()->where('name', 'Content Administrator')->firstOrFail();

    expect($role->permissions()->pluck('permissions.id')->sort()->values()->all())
        ->toBe([$parent->id, $child->id]);
});

test('updating a role adds and removes permissions accurately', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $parent = createAdminPermission('roles', 'Roles');
    $create = createAdminPermission('roles-create', 'Create', $parent);
    $update = createAdminPermission('roles-update', 'Update', $parent);
    $role = Role::factory()->create(['name' => 'Editor']);
    $role->permissions()->attach([$parent->id, $create->id]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.roles.update', $role), [
            'name' => 'Editor',
            'short_desc' => 'Updated',
            'type' => 1,
            'permission' => [$parent->id, $update->id],
        ])
        ->assertSessionHasNoErrors();

    expect($role->permissions()->pluck('permissions.id')->sort()->values()->all())
        ->toBe([$parent->id, $update->id]);
});

test('deleting a role sets status to inactive', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $role = Role::factory()->create();

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.roles.destroy', $role))
        ->assertRedirect(route('admin.roles.index'));

    expect($role->refresh()->status)->toBe(2);
});

test('a role assigned to an active administrator cannot be deactivated', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $assignedRole = Role::factory()->create();
    AdminUser::factory()->for($assignedRole)->create();

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.roles.destroy', $assignedRole))
        ->assertSessionHas('error');

    expect($assignedRole->refresh()->status)->toBe(1);
});

test('creating an administrator hashes the password', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $role = Role::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.admin-users.store'), [
            'role_id' => $role->id,
            'country_id' => 0,
            'name' => 'New Administrator',
            'email' => 'new-admin@example.com',
            'gender' => 0,
            'phone' => null,
            'address' => null,
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'is_super' => 2,
        ])
        ->assertSessionHasNoErrors();

    $created = AdminUser::query()->where('email', 'new-admin@example.com')->firstOrFail();

    expect(Hash::check('secure-password', $created->password))->toBeTrue()
        ->and($created->password)->not->toBe('secure-password');
});

test('updating an administrator preserves the password', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $target = createAdministrator(['password' => Hash::make('original-password')]);
    $originalHash = $target->password;

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.admin-users.update', $target), [
            'role_id' => $target->role_id,
            'country_id' => 0,
            'name' => 'Updated Administrator',
            'email' => $target->email,
            'gender' => $target->gender,
            'phone' => null,
            'address' => null,
            'status' => 1,
            'is_super' => 2,
        ])
        ->assertSessionHasNoErrors();

    expect($target->refresh()->password)->toBe($originalHash)
        ->and($target->name)->toBe('Updated Administrator');
});

test('administrator password is updated only through the password action', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $target = createAdministrator();

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.admin-users.password.update', $target), [
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check('replacement-password', $target->refresh()->password))->toBeTrue();
});

test('duplicate administrator email is rejected', function (): void {
    $admin = createAdministrator(['is_super' => 1]);
    $existing = createAdministrator();
    $role = Role::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.admin-users.store'), [
            'role_id' => $role->id,
            'country_id' => 0,
            'name' => 'Duplicate Email',
            'email' => $existing->email,
            'gender' => 0,
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'is_super' => 2,
        ])
        ->assertSessionHasErrors(['email']);
});

test('the last active super administrator cannot be disabled', function (): void {
    $admin = createAdministrator(['is_super' => 1]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.admin-users.update', $admin), [
            'role_id' => $admin->role_id,
            'country_id' => 0,
            'name' => $admin->name,
            'email' => $admin->email,
            'gender' => $admin->gender,
            'phone' => null,
            'address' => null,
            'status' => 2,
            'is_super' => 1,
        ])
        ->assertSessionHas('error');

    expect($admin->refresh()->status)->toBe(1)
        ->and($admin->is_super)->toBe(1);
});

test('the last active super administrator cannot be demoted', function (): void {
    $admin = createAdministrator(['is_super' => 1]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.admin-users.update', $admin), [
            'role_id' => $admin->role_id,
            'country_id' => 0,
            'name' => $admin->name,
            'email' => $admin->email,
            'gender' => $admin->gender,
            'phone' => null,
            'address' => null,
            'status' => 1,
            'is_super' => 2,
        ])
        ->assertSessionHas('error');

    expect($admin->refresh()->is_super)->toBe(1);
});

test('an inactive signed-in administrator is removed from the admin guard', function (): void {
    $admin = createAdministrator(['status' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.loginpage'));

    $this->assertGuest('admin');
});
