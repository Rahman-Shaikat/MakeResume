<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makePermissionCrudAdmin(array $attributes = []): AdminUser
{
    return AdminUser::factory()
        ->for(Role::factory())
        ->create($attributes + ['is_super' => 1]);
}

function makePermissionCrudGroup(array $attributes = []): PermissionGroup
{
    return PermissionGroup::query()->create($attributes + [
        'name' => fake()->unique()->words(2, true),
        'type' => 1,
        'status' => 1,
    ]);
}

function makePermissionCrudPermission(
    PermissionGroup $group,
    string $metaName,
    ?Permission $parent = null,
): Permission {
    return Permission::query()->create([
        'group_id' => $group->id,
        'parent_id' => $parent?->id ?? 0,
        'name' => str($metaName)->headline()->toString(),
        'meta_name' => $metaName,
        'type' => 1,
        'status' => 1,
    ]);
}

test('permission management is shown in the sidebar to an authorized administrator', function (): void {
    $viewPermission = makePermissionCrudPermission(
        makePermissionCrudGroup(),
        'permissions',
    );
    $role = Role::factory()->create();
    $role->permissions()->attach($viewPermission);
    $admin = AdminUser::factory()->for($role)->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.permissions.index'))
        ->assertOk()
        ->assertSee('Permissions')
        ->assertSee(route('admin.permissions.index'), false);
});

test('administrator without permission cannot access permission management', function (): void {
    $admin = makePermissionCrudAdmin(['is_super' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.permissions.index'))
        ->assertForbidden();
});

test('super administrator can create and update a permission group', function (): void {
    $admin = makePermissionCrudAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.permission-groups.store'), [
            'name' => 'Resume Management',
            'short_desc' => 'Resume administration permissions.',
            'type' => 1,
        ])
        ->assertSessionHasNoErrors();

    $group = PermissionGroup::query()->where('name', 'Resume Management')->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.permission-groups.update', $group), [
            'name' => 'Resume Operations',
            'short_desc' => 'Updated description.',
            'type' => 1,
        ])
        ->assertSessionHasNoErrors();

    expect($group->refresh()->name)->toBe('Resume Operations')
        ->and($group->short_desc)->toBe('Updated description.');
});

test('empty permission group can be deactivated', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.permission-groups.destroy', $group))
        ->assertRedirect(route('admin.permissions.index'));

    expect($group->refresh()->status)->toBe(2);
});

test('permission group with active permissions cannot be deactivated', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();
    makePermissionCrudPermission($group, 'resumes');

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.permission-groups.destroy', $group))
        ->assertSessionHas('error');

    expect($group->refresh()->status)->toBe(1);
});

test('super administrator can create parent and child permissions', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.permissions.store'), [
            'group_id' => $group->id,
            'parent_id' => 0,
            'name' => 'Resumes',
            'meta_name' => 'resumes',
            'short_desc' => 'View resumes.',
            'type' => 1,
        ])
        ->assertSessionHasNoErrors();

    $parent = Permission::query()->where('meta_name', 'resumes')->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.permissions.store'), [
            'group_id' => $group->id,
            'parent_id' => $parent->id,
            'name' => 'Update',
            'meta_name' => 'resumes-update',
            'short_desc' => 'Update resumes.',
            'type' => 1,
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('permissions', [
        'group_id' => $group->id,
        'parent_id' => $parent->id,
        'meta_name' => 'resumes-update',
        'status' => 1,
    ]);
});

test('permission key must use unique lowercase kebab case', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();
    makePermissionCrudPermission($group, 'resumes');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.permissions.store'), [
            'group_id' => $group->id,
            'parent_id' => 0,
            'name' => 'Invalid key',
            'meta_name' => 'Resume Update',
            'type' => 1,
        ])
        ->assertSessionHasErrors(['meta_name']);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.permissions.store'), [
            'group_id' => $group->id,
            'parent_id' => 0,
            'name' => 'Duplicate key',
            'meta_name' => 'resumes',
            'type' => 1,
        ])
        ->assertSessionHasErrors(['meta_name']);
});

test('child permission must belong to the same group as its parent', function (): void {
    $admin = makePermissionCrudAdmin();
    $parentGroup = makePermissionCrudGroup();
    $otherGroup = makePermissionCrudGroup();
    $parent = makePermissionCrudPermission($parentGroup, 'resumes');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.permissions.store'), [
            'group_id' => $otherGroup->id,
            'parent_id' => $parent->id,
            'name' => 'Update',
            'meta_name' => 'resumes-update',
            'type' => 1,
        ])
        ->assertSessionHasErrors(['parent_id']);
});

test('updating a permission preserves its released route key', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();
    $permission = makePermissionCrudPermission($group, 'resumes');

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.permissions.update', $permission), [
            'group_id' => $group->id,
            'parent_id' => 0,
            'name' => 'Resume Directory',
            'meta_name' => 'changed-key',
            'short_desc' => 'Updated display details.',
            'type' => 1,
        ])
        ->assertSessionHasNoErrors();

    expect($permission->refresh()->meta_name)->toBe('resumes')
        ->and($permission->name)->toBe('Resume Directory');
});

test('parent permission with active children cannot become a child', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();
    $parent = makePermissionCrudPermission($group, 'resumes');
    makePermissionCrudPermission($group, 'resumes-update', $parent);
    $otherParent = makePermissionCrudPermission($group, 'users');

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.permissions.update', $parent), [
            'group_id' => $group->id,
            'parent_id' => $otherParent->id,
            'name' => $parent->name,
            'short_desc' => null,
            'type' => 1,
        ])
        ->assertSessionHasErrors(['parent_id']);

    expect($parent->refresh()->parent_id)->toBe(0);
});

test('deactivating a child permission removes its role assignments', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();
    $parent = makePermissionCrudPermission($group, 'resumes');
    $child = makePermissionCrudPermission($group, 'resumes-update', $parent);
    $role = Role::factory()->create();
    $role->permissions()->attach($child);

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.permissions.destroy', $child))
        ->assertRedirect(route('admin.permissions.index'));

    expect($child->refresh()->status)->toBe(2)
        ->and($role->permissions()->whereKey($child->id)->exists())->toBeFalse();
});

test('deactivating a parent also deactivates children and removes assignments', function (): void {
    $admin = makePermissionCrudAdmin();
    $group = makePermissionCrudGroup();
    $parent = makePermissionCrudPermission($group, 'resumes');
    $child = makePermissionCrudPermission($group, 'resumes-update', $parent);
    $role = Role::factory()->create();
    $role->permissions()->attach([$parent->id, $child->id]);

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.permissions.destroy', $parent))
        ->assertRedirect(route('admin.permissions.index'));

    expect($parent->refresh()->status)->toBe(2)
        ->and($child->refresh()->status)->toBe(2)
        ->and($role->permissions()->count())->toBe(0);
});
