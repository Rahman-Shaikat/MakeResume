<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\Category;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Database\Seeders\PermissionGroupSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

uses(RefreshDatabase::class);

function makeCategoryCrudAdmin(array $attributes = []): AdminUser
{
    return AdminUser::factory()
        ->for(Role::factory())
        ->create($attributes + ['is_super' => 1]);
}

test('normal administrator without category permission receives forbidden', function (): void {
    $admin = makeCategoryCrudAdmin(['is_super' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.categories.index'))
        ->assertForbidden();
});

test('authorized administrator sees category management in the sidebar', function (): void {
    $group = PermissionGroup::query()->create([
        'name' => 'Template Management',
        'type' => 1,
        'status' => 1,
    ]);
    $permission = Permission::query()->create([
        'group_id' => $group->id,
        'parent_id' => 0,
        'name' => 'Categories',
        'meta_name' => 'categories',
        'type' => 1,
        'status' => 1,
    ]);
    $role = Role::factory()->create();
    $role->permissions()->attach($permission);
    $admin = AdminUser::factory()->for($role)->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Template management')
        ->assertSee(route('admin.categories.index'), false);
});

test('super administrator can create a parent category with generated slug', function (): void {
    $admin = makeCategoryCrudAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.categories.store'), [
            'parent_id' => 0,
            'name' => 'Software Engineering',
            'slug' => '',
            'short_desc' => 'Resume templates for software engineering roles.',
            'status' => 1,
            'is_featured' => 1,
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('categories', [
        'parent_id' => 0,
        'name' => 'Software Engineering',
        'slug' => 'software-engineering',
        'status' => 1,
        'position' => 0,
        'is_featured' => 1,
    ]);
});

test('super administrator can create a subcategory under an active parent', function (): void {
    $admin = makeCategoryCrudAdmin();
    $parent = Category::factory()->create(['name' => 'Engineering', 'slug' => 'engineering']);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.categories.store'), [
            'parent_id' => $parent->id,
            'name' => 'Civil Engineering',
            'slug' => 'civil-engineering',
            'short_desc' => null,
            'status' => 1,
            'is_featured' => 2,
        ])
        ->assertSessionHasNoErrors();

    $subcategory = Category::query()->where('slug', 'civil-engineering')->firstOrFail();

    expect($subcategory->parent->is($parent))->toBeTrue();
});

test('subcategory cannot be selected as another category parent', function (): void {
    $admin = makeCategoryCrudAdmin();
    $parent = Category::factory()->create(['slug' => 'engineering']);
    $subcategory = Category::factory()->create([
        'parent_id' => $parent->id,
        'slug' => 'civil-engineering',
    ]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.categories.store'), [
            'parent_id' => $subcategory->id,
            'name' => 'Bridge Engineering',
            'slug' => 'bridge-engineering',
            'status' => 1,
            'is_featured' => 2,
        ])
        ->assertSessionHasErrors(['parent_id']);
});

test('category slug must remain unique', function (): void {
    $admin = makeCategoryCrudAdmin();
    Category::factory()->create(['slug' => 'accounting']);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.categories.store'), [
            'parent_id' => 0,
            'name' => 'Accounting Duplicate',
            'slug' => 'accounting',
            'status' => 1,
            'is_featured' => 2,
        ])
        ->assertSessionHasErrors(['slug']);
});

test('super administrator can update category fields', function (): void {
    $admin = makeCategoryCrudAdmin();
    $category = Category::factory()->create([
        'slug' => 'software',
        'position' => 7,
    ]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.categories.update', $category), [
            'parent_id' => 0,
            'name' => 'Software Engineering',
            'slug' => 'software-engineering',
            'short_desc' => 'Updated description.',
            'status' => 1,
            'position' => 1,
            'is_featured' => 1,
        ])
        ->assertSessionHasNoErrors();

    expect($category->refresh()->name)->toBe('Software Engineering')
        ->and($category->slug)->toBe('software-engineering')
        ->and($category->position)->toBe(7)
        ->and($category->is_featured)->toBe(1);
});

test('category cannot be its own parent', function (): void {
    $admin = makeCategoryCrudAdmin();
    $category = Category::factory()->create(['slug' => 'engineering']);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.categories.update', $category), [
            'parent_id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'short_desc' => null,
            'status' => 1,
            'is_featured' => 2,
        ])
        ->assertSessionHasErrors(['parent_id']);
});

test('parent category with active children cannot become a subcategory', function (): void {
    $admin = makeCategoryCrudAdmin();
    $category = Category::factory()->create(['slug' => 'engineering']);
    Category::factory()->create(['parent_id' => $category->id, 'slug' => 'civil-engineering']);
    $newParent = Category::factory()->create(['slug' => 'technical']);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.categories.update', $category), [
            'parent_id' => $newParent->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'short_desc' => null,
            'status' => 1,
            'is_featured' => 2,
        ])
        ->assertSessionHasErrors(['parent_id']);

    expect($category->refresh()->parent_id)->toBe(0);
});

test('making a parent inactive also deactivates active subcategories', function (): void {
    $admin = makeCategoryCrudAdmin();
    $parent = Category::factory()->create(['slug' => 'engineering']);
    $child = Category::factory()->create([
        'parent_id' => $parent->id,
        'slug' => 'civil-engineering',
    ]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.categories.update', $parent), [
            'parent_id' => 0,
            'name' => $parent->name,
            'slug' => $parent->slug,
            'short_desc' => null,
            'status' => 2,
            'is_featured' => 2,
        ])
        ->assertSessionHasNoErrors();

    expect($parent->refresh()->status)->toBe(2)
        ->and($child->refresh()->status)->toBe(2);
});

test('inactive subcategory retains its parent but cannot reactivate under an inactive parent', function (): void {
    $admin = makeCategoryCrudAdmin();
    $parent = Category::factory()->inactive()->create(['slug' => 'engineering']);
    $child = Category::factory()->inactive()->create([
        'parent_id' => $parent->id,
        'slug' => 'civil-engineering',
    ]);
    $payload = [
        'parent_id' => $parent->id,
        'name' => 'Civil Engineering Updated',
        'slug' => $child->slug,
        'short_desc' => null,
        'status' => 2,
        'is_featured' => 2,
    ];

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.categories.update', $child), $payload)
        ->assertSessionHasNoErrors();

    expect($child->refresh()->parent_id)->toBe($parent->id);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.categories.update', $child), array_merge($payload, ['status' => 1]))
        ->assertSessionHasErrors(['parent_id']);
});

test('deactivating a parent category also deactivates its subcategories', function (): void {
    $admin = makeCategoryCrudAdmin();
    $parent = Category::factory()->create(['slug' => 'medical']);
    $child = Category::factory()->create([
        'parent_id' => $parent->id,
        'slug' => 'medical-coding',
    ]);

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.categories.destroy', $parent))
        ->assertRedirect(route('admin.categories.index'));

    expect($parent->refresh()->status)->toBe(2)
        ->and($child->refresh()->status)->toBe(2);
});

test('category list respects position and filters category level', function (): void {
    $admin = makeCategoryCrudAdmin();
    $later = Category::factory()->create([
        'name' => 'Nursing',
        'slug' => 'nursing',
        'position' => 20,
    ]);
    $first = Category::factory()->create([
        'name' => 'Accounting',
        'slug' => 'accounting',
        'position' => 1,
    ]);
    Category::factory()->create([
        'parent_id' => $later->id,
        'name' => 'Clinical Nursing',
        'slug' => 'clinical-nursing',
        'position' => 0,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.categories.index', ['level' => 'parent']))
        ->assertOk()
        ->assertSeeInOrder([$first->name, $later->name])
        ->assertDontSee('Clinical Nursing');
});

test('category form uses searchable parent selection and radio controls without a position field', function (): void {
    $admin = makeCategoryCrudAdmin();
    Category::factory()->create([
        'name' => 'Engineering',
        'slug' => 'engineering',
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee('js-category-parent-select', false)
        ->assertSee('name="status"', false)
        ->assertSee('name="is_featured"', false)
        ->assertSee('type="radio"', false)
        ->assertDontSee('name="position"', false);
});

test('reusable category form components render validation errors beside fields', function (): void {
    $admin = makeCategoryCrudAdmin();
    $createUrl = route('admin.categories.create');

    $this->actingAs($admin, 'admin')
        ->from($createUrl)
        ->post(route('admin.categories.store'), [
            'parent_id' => 0,
            'name' => '',
            'slug' => '',
            'status' => 1,
            'is_featured' => 2,
        ])
        ->assertRedirect($createUrl)
        ->assertSessionHasErrors(['name', 'slug']);

    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag(['name' => 'The name field is required.']));
    view()->share('errors', $errors);
    $html = Blade::render(
        '<x-admin.forms.input name="name" label="Name" />',
    );

    expect($html)
        ->toContain('admin-field-error')
        ->toContain('The name field is required.');
});

test('new categories append after the highest position', function (): void {
    $admin = makeCategoryCrudAdmin();
    Category::factory()->create(['position' => 4]);
    Category::factory()->create(['position' => 12]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.categories.store'), [
            'parent_id' => 0,
            'name' => 'Technical Writing',
            'slug' => 'technical-writing',
            'status' => 1,
            'is_featured' => 2,
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('categories', [
        'slug' => 'technical-writing',
        'position' => 13,
    ]);
});

test('super administrator can reorder the complete category list', function (): void {
    $admin = makeCategoryCrudAdmin();
    $first = Category::factory()->create(['position' => 10]);
    $second = Category::factory()->create(['position' => 20]);
    $third = Category::factory()->create(['position' => 30]);

    $this->actingAs($admin, 'admin')
        ->patchJson(route('admin.categories.reorder'), [
            'category_ids' => [$third->id, $first->id, $second->id],
        ])
        ->assertOk()
        ->assertJsonPath('message', 'Category order updated successfully.');

    expect($third->refresh()->position)->toBe(0)
        ->and($first->refresh()->position)->toBe(1)
        ->and($second->refresh()->position)->toBe(2);
});

test('category reorder requires update permission', function (): void {
    $admin = makeCategoryCrudAdmin(['is_super' => 2]);
    $category = Category::factory()->create();

    $this->actingAs($admin, 'admin')
        ->patchJson(route('admin.categories.reorder'), [
            'category_ids' => [$category->id],
        ])
        ->assertForbidden();
});

test('category reorder rejects duplicate and partial category lists', function (): void {
    $admin = makeCategoryCrudAdmin();
    $first = Category::factory()->create();
    $second = Category::factory()->create();

    $this->actingAs($admin, 'admin')
        ->patchJson(route('admin.categories.reorder'), [
            'category_ids' => [$first->id, $first->id],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['category_ids.1']);

    $this->actingAs($admin, 'admin')
        ->patchJson(route('admin.categories.reorder'), [
            'category_ids' => [$second->id],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['category_ids']);
});

test('drag ordering is enabled only on the complete unfiltered category list', function (): void {
    $admin = makeCategoryCrudAdmin();
    Category::factory()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('data-category-sortable', false)
        ->assertSee(route('admin.categories.reorder'), false);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.categories.index', ['status' => 1]))
        ->assertOk()
        ->assertDontSee('data-category-sortable', false)
        ->assertSee('Clear all filters to enable drag-and-drop ordering.');
});

test('category permissions are included in the project seeders', function (): void {
    $this->seed([
        PermissionGroupSeeder::class,
        PermissionSeeder::class,
    ]);

    $this->assertDatabaseHas('permission_groups', [
        'name' => 'Template Management',
        'status' => 1,
    ]);

    foreach (['categories', 'categories-create', 'categories-update', 'categories-delete'] as $key) {
        $this->assertDatabaseHas('permissions', [
            'meta_name' => $key,
            'status' => 1,
        ]);
    }
});
