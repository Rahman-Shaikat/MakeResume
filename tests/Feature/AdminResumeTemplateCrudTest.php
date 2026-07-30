<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\Category;
use App\Models\Resume;
use App\Models\ResumeTemplate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makeResumeTemplateAdmin(array $attributes = []): AdminUser
{
    return AdminUser::factory()
        ->for(Role::factory())
        ->create($attributes + ['is_super' => 1]);
}

function validTemplatePayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Executive Focus',
        'slug' => '',
        'renderer_key' => 'template-one',
        'short_desc' => 'A focused executive resume.',
        'thumbnail' => UploadedFile::fake()->image('template.jpg', 700, 990)->size(500),
        'accent_color' => '#123ABC',
        'allows_profile_photo' => 1,
        'is_ats_friendly' => 1,
        'is_featured' => 2,
        'status' => 1,
        'category_ids' => [],
    ], $overrides);
}

test('migration imports all six legacy templates with stable renderer mappings', function (): void {
    expect(ResumeTemplate::query()->count())->toBe(6);

    foreach (['template-one', 'template-two', 'template-three', 'template-four', 'template-five', 'template-six'] as $slug) {
        $this->assertDatabaseHas('resume_templates', [
            'slug' => $slug,
            'renderer_key' => $slug,
            'status' => 1,
        ]);
    }
});

test('administrator without template permission is forbidden', function (): void {
    $admin = makeResumeTemplateAdmin(['is_super' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.templates.index'))
        ->assertForbidden();
});

test('super administrator can browse create and edit template screens', function (): void {
    $admin = makeResumeTemplateAdmin();
    $template = ResumeTemplate::query()->where('slug', 'template-one')->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.templates.index'))
        ->assertOk()
        ->assertSee('Resume templates')
        ->assertSee('Professional Cyan');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.templates.create'))
        ->assertOk()
        ->assertSee('Create resume template');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.templates.edit', $template))
        ->assertOk()
        ->assertSee('Stable identifier. It cannot be changed after creation.');
});

test('super administrator can create an active template with thumbnail and multiple categories', function (): void {
    Storage::fake('public');
    $admin = makeResumeTemplateAdmin();
    $categories = Category::factory()->count(2)->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.templates.store'), validTemplatePayload([
            'category_ids' => $categories->pluck('id')->all(),
        ]))
        ->assertSessionHasNoErrors();

    $template = ResumeTemplate::query()->where('slug', 'executive-focus')->firstOrFail();

    expect($template->categories)->toHaveCount(2)
        ->and($template->accent_color)->toBe('#123ABC')
        ->and($template->position)->toBe(6)
        ->and($template->thumbnail_path)->toStartWith('resume-templates/');

    Storage::disk('public')->assertExists($template->thumbnail_path);
});

test('new templates default inactive and cannot activate without a thumbnail', function (): void {
    $admin = makeResumeTemplateAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.templates.store'), validTemplatePayload([
            'thumbnail' => null,
            'status' => 1,
        ]))
        ->assertSessionHasErrors(['thumbnail']);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.templates.store'), validTemplatePayload([
            'thumbnail' => null,
            'status' => 2,
        ]))
        ->assertSessionHasNoErrors();

    expect(ResumeTemplate::query()->where('slug', 'executive-focus')->value('status'))->toBe(2);
});

test('template upload rejects executable files invalid colors and unknown renderers', function (): void {
    $admin = makeResumeTemplateAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.templates.store'), validTemplatePayload([
            'thumbnail' => UploadedFile::fake()->createWithContent('theme.php', '<?php echo "unsafe";'),
            'accent_color' => 'red',
            'renderer_key' => '../../malicious-view',
        ]))
        ->assertSessionHasErrors(['thumbnail', 'accent_color', 'renderer_key']);
});

test('slug remains immutable and used renderer cannot be changed', function (): void {
    $admin = makeResumeTemplateAdmin();
    $template = ResumeTemplate::query()->where('slug', 'template-one')->firstOrFail();
    $user = User::factory()->create();
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => $template->slug,
        'content' => [],
    ]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.templates.update', $template), [
            ...validTemplatePayload([
                'name' => 'Renamed Catalog Entry',
                'slug' => 'changed-slug',
                'renderer_key' => 'template-two',
                'thumbnail' => null,
            ]),
        ])
        ->assertSessionHasErrors(['renderer_key']);

    expect($template->refresh()->slug)->toBe('template-one')
        ->and($template->renderer_key)->toBe('template-one');
});

test('thumbnail replacement removes the old managed file', function (): void {
    Storage::fake('public');
    $admin = makeResumeTemplateAdmin();
    Storage::disk('public')->put('resume-templates/old.jpg', 'old');
    $template = ResumeTemplate::factory()->create([
        'thumbnail_path' => 'resume-templates/old.jpg',
        'status' => 1,
    ]);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.templates.update', $template), validTemplatePayload([
            'name' => $template->name,
            'renderer_key' => $template->renderer_key,
            'thumbnail' => UploadedFile::fake()->image('replacement.png', 700, 990)->size(400),
        ]))
        ->assertSessionHasNoErrors();

    $template->refresh();
    Storage::disk('public')->assertMissing('resume-templates/old.jpg');
    Storage::disk('public')->assertExists($template->thumbnail_path);
});

test('used templates cannot be deleted but unused templates can', function (): void {
    $admin = makeResumeTemplateAdmin();
    $used = ResumeTemplate::query()->where('slug', 'template-one')->firstOrFail();
    $user = User::factory()->create();
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => $used->slug,
        'content' => [],
    ]);

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.templates.destroy', $used))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('resume_templates', ['id' => $used->id]);

    $unused = ResumeTemplate::factory()->create();
    $this->actingAs($admin, 'admin')
        ->delete(route('admin.templates.destroy', $unused))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('resume_templates', ['id' => $unused->id]);
});

test('unfiltered template list can be reordered with a complete id set', function (): void {
    $admin = makeResumeTemplateAdmin();
    $ids = ResumeTemplate::query()->orderBy('position')->pluck('id')->reverse()->values()->all();

    $this->actingAs($admin, 'admin')
        ->patchJson(route('admin.templates.reorder'), ['template_ids' => $ids])
        ->assertOk();

    expect(ResumeTemplate::query()->orderBy('position')->pluck('id')->all())->toBe($ids);
});
