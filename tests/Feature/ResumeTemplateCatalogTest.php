<?php

declare(strict_types=1);

use App\Models\Resume;
use App\Models\ResumeTemplate;
use App\Models\User;
use App\Services\ResumeBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard exposes active templates as progressively loaded live previews', function (): void {
    $user = User::factory()->create();
    ResumeTemplate::query()->where('slug', 'template-six')->update(['status' => 2]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('template-one.png')
        ->assertSee('data-live-template-preview', false)
        ->assertSee('data-live-preview-url="'.route('resume.templates.show', ['template' => 'template-one', 'embed' => 1]).'"', false)
        ->assertDontSee('data-template-card="template-six"', false)
        ->assertDontSee('<iframe', false);
});

test('inactive templates cannot create a new resume but continue rendering existing resumes', function (): void {
    $user = User::factory()->create();
    $template = ResumeTemplate::query()->where('slug', 'template-two')->firstOrFail();
    $template->update(['status' => 2]);
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => $template->slug,
        'content' => ['full_name' => 'Legacy Owner'],
    ]);

    $this->actingAs($user)
        ->postJson(route('resume.template.select'), ['template_slug' => $template->slug])
        ->assertUnprocessable();

    $this->actingAs($user)
        ->get(route('resume.preview', $resume))
        ->assertOk()
        ->assertSee('Legacy Owner')
        ->assertSee('resume-template-two');
});

test('owner can switch templates without changing resume content sections or profile photo', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'profile_image' => 'images/profile.png',
        'content' => ['full_name' => 'Preserved Name', 'summary' => 'Preserved summary'],
    ]);
    app(ResumeBuilderService::class)->load($resume);
    $sectionIds = $resume->sections()->orderBy('sort_order')->pluck('id')->all();
    $content = $resume->content;

    $this->actingAs($user)
        ->get(route('resume.templates.index', $resume))
        ->assertOk()
        ->assertSee('Change your resume template')
        ->assertSee('Current template')
        ->assertSee('data-live-preview-url="'.route('resume.templates.show', ['template' => 'template-one', 'embed' => 1]).'"', false)
        ->assertDontSee('<iframe', false);

    $this->actingAs($user)
        ->patch(route('resume.template.update', $resume), ['template_slug' => 'template-three'])
        ->assertRedirect(route('resume.builder', $resume));

    $resume->refresh();

    expect($resume->template_slug)->toBe('template-three')
        ->and($resume->content)->toBe($content)
        ->and($resume->profile_image)->toBe('images/profile.png')
        ->and($resume->sections()->orderBy('sort_order')->pluck('id')->all())->toBe($sectionIds);
});

test('another user cannot view the switch screen or switch an owned resume', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $owner->id,
        'template_slug' => 'template-one',
        'content' => [],
    ]);

    $this->actingAs($other)
        ->get(route('resume.templates.index', $resume))
        ->assertForbidden();

    $this->actingAs($other)
        ->patch(route('resume.template.update', $resume), ['template_slug' => 'template-two'])
        ->assertForbidden();

    expect($resume->refresh()->template_slug)->toBe('template-one');
});

test('template preview view is always resolved from the allowlisted renderer key', function (): void {
    $user = User::factory()->create();
    $template = ResumeTemplate::query()->where('slug', 'template-one')->firstOrFail();
    $template->update(['renderer_key' => '../../unsafe']);
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => $template->slug,
        'content' => [],
    ]);

    $this->actingAs($user)
        ->get(route('resume.preview', $resume))
        ->assertNotFound();
});

test('renderer applies the catalog accent and hides profile image output when disabled', function (): void {
    $user = User::factory()->create();
    $template = ResumeTemplate::query()->where('slug', 'template-two')->firstOrFail();
    $template->update([
        'accent_color' => '#AA1122',
        'allows_profile_photo' => 2,
    ]);
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => $template->slug,
        'profile_image' => 'images/private-profile.png',
        'content' => ['full_name' => 'Photo Hidden'],
    ]);

    $this->actingAs($user)
        ->get(route('resume.preview', $resume))
        ->assertOk()
        ->assertSee('--resume-accent: #AA1122', false)
        ->assertDontSee('private-profile.png');
});
