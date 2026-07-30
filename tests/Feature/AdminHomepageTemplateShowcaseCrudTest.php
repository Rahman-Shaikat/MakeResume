<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\HomepageTemplateShowcase;
use App\Models\ResumeTemplate;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeHomepageTemplateShowcaseAdmin(array $attributes = []): AdminUser
{
    return AdminUser::factory()
        ->for(Role::factory())
        ->create($attributes + ['is_super' => 1]);
}

function homepageShowcaseTemplateIds(): array
{
    return ResumeTemplate::query()
        ->active()
        ->orderBy('position')
        ->limit(4)
        ->pluck('id')
        ->all();
}

function validHomepageTemplateShowcasePayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Career-focused showcase',
        'eyebrow' => 'Designed for every next step',
        'headline' => 'Find the resume format that makes your experience clear.',
        'cta_label' => 'Explore your workspace',
        'status' => 1,
        'template_ids' => homepageShowcaseTemplateIds(),
    ], $overrides);
}

test('administrator without homepage template showcase permission is forbidden', function (): void {
    $admin = makeHomepageTemplateShowcaseAdmin(['is_super' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-template-showcases.index'))
        ->assertForbidden();
});

test('super administrator can browse create and edit homepage template showcase screens', function (): void {
    $admin = makeHomepageTemplateShowcaseAdmin();
    $showcase = HomepageTemplateShowcase::query()->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-template-showcases.index'))
        ->assertOk()
        ->assertSee('Homepage template showcase')
        ->assertSee('Primary template showcase');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-template-showcases.create'))
        ->assertOk()
        ->assertSee('Create template showcase');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-template-showcases.edit', $showcase))
        ->assertOk()
        ->assertSee('Search and select exactly four templates');
});

test('publishing a template showcase selects four templates and replaces the active showcase', function (): void {
    $admin = makeHomepageTemplateShowcaseAdmin();
    $existingShowcase = HomepageTemplateShowcase::query()->active()->firstOrFail();
    $templateIds = homepageShowcaseTemplateIds();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.homepage-template-showcases.store'), validHomepageTemplateShowcasePayload())
        ->assertSessionHasNoErrors();

    $showcase = HomepageTemplateShowcase::query()->where('name', 'Career-focused showcase')->firstOrFail();

    expect($showcase->status)->toBe(1)
        ->and($existingShowcase->refresh()->status)->toBe(2)
        ->and($showcase->templates()->pluck('resume_templates.id')->all())->toBe($templateIds)
        ->and(HomepageTemplateShowcase::query()->active()->count())->toBe(1);
});

test('an active template showcase must contain exactly four active catalog templates', function (): void {
    $admin = makeHomepageTemplateShowcaseAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.homepage-template-showcases.store'), validHomepageTemplateShowcasePayload([
            'template_ids' => array_slice(homepageShowcaseTemplateIds(), 0, 3),
        ]))
        ->assertSessionHasErrors(['template_ids']);

    $inactiveTemplate = ResumeTemplate::factory()->create(['status' => 2]);
    $templateIds = homepageShowcaseTemplateIds();
    $templateIds[3] = $inactiveTemplate->id;

    $this->actingAs($admin, 'admin')
        ->post(route('admin.homepage-template-showcases.store'), validHomepageTemplateShowcasePayload([
            'template_ids' => $templateIds,
        ]))
        ->assertSessionHasErrors(['template_ids.3']);
});

test('the public homepage renders the active showcase copy and four selected templates', function (): void {
    $showcase = HomepageTemplateShowcase::query()->active()->firstOrFail();
    $templates = ResumeTemplate::query()->active()->orderBy('position')->limit(4)->get();
    $showcase->update([
        'eyebrow' => 'Admin managed showcase',
        'headline' => 'Four dynamic templates for every career move.',
        'cta_label' => 'Browse your options',
    ]);
    $showcase->templates()->sync(
        $templates->mapWithKeys(fn (ResumeTemplate $template, int $position): array => [$template->id => ['position' => $position]])->all(),
    );

    $this->get('/')
        ->assertOk()
        ->assertSee('Admin managed showcase')
        ->assertSee('Four dynamic templates for every career move.')
        ->assertSee('Browse your options');

    foreach ($templates as $template) {
        $this->get('/')->assertSee($template->name);
    }
});
