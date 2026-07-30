<?php

declare(strict_types=1);

use App\Models\HomepageTemplateShowcase;
use App\Models\ResumeTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests can view the public homepage', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('Build a resume that makes your next move feel possible.')
        ->assertSee('Create my resume')
        ->assertSee('View templates');
});

test('authenticated users see a workspace call to action on the home page', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('Open my workspace');
});

test('the homepage only shows active catalog templates', function (): void {
    $visibleTemplate = ResumeTemplate::factory()->create([
        'name' => 'Homepage Visible Template',
        'status' => 1,
        'is_featured' => 1,
    ]);
    $hiddenTemplate = ResumeTemplate::factory()->create([
        'name' => 'Homepage Hidden Template',
        'status' => 2,
    ]);
    $showcase = HomepageTemplateShowcase::query()->active()->firstOrFail();
    $showcaseTemplateIds = ResumeTemplate::query()
        ->active()
        ->where('id', '!=', $visibleTemplate->id)
        ->orderBy('position')
        ->limit(3)
        ->pluck('id')
        ->prepend($visibleTemplate->id)
        ->values();
    $showcase->templates()->sync(
        $showcaseTemplateIds
            ->mapWithKeys(fn (int $templateId, int $position): array => [$templateId => ['position' => $position]])
            ->all(),
    );

    $this->get('/')
        ->assertOk()
        ->assertSee($visibleTemplate->name)
        ->assertDontSee($hiddenTemplate->name);
});
