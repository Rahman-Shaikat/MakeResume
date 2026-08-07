<?php

declare(strict_types=1);

use App\Models\Resume;
use App\Models\ResumeSection;
use App\Models\ResumeSectionItem;
use App\Models\User;
use App\Services\ResumeBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function resumeFor(User $user): Resume
{
    return Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);
}

test('opening the builder initializes all default sections once', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);

    $this->actingAs($user)->get(route('resume.builder', $resume))->assertOk();
    $this->actingAs($user)->get(route('resume.builder', $resume))->assertOk();

    expect($resume->sections()->count())
        ->toBe(count(ResumeBuilderService::DEFAULT_SECTIONS))
        ->and($resume->sections()->orderBy('sort_order')->pluck('type')->all())
        ->toBe(array_keys(ResumeBuilderService::DEFAULT_SECTIONS));
});

test('a user can create rename and delete a custom section', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);
    $this->actingAs($user)->get(route('resume.builder', $resume));

    $response = $this->actingAs($user)
        ->postJson(route('resume.builder.sections.store', $resume), ['title' => 'Publications'])
        ->assertCreated()
        ->assertJsonPath('section.type', 'custom')
        ->assertJsonPath('section.title', 'Publications');

    $section = ResumeSection::query()->findOrFail($response->json('section.id'));

    $this->actingAs($user)
        ->patchJson(route('resume.builder.sections.update', [$resume, $section]), ['title' => 'Selected Publications'])
        ->assertOk()
        ->assertJsonPath('section.title', 'Selected Publications');

    $this->actingAs($user)
        ->deleteJson(route('resume.builder.sections.destroy', [$resume, $section]))
        ->assertOk();

    $this->assertDatabaseMissing('resume_sections', ['id' => $section->id]);
});

test('default sections cannot be renamed or deleted', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);
    app(ResumeBuilderService::class)->load($resume);
    $summary = $resume->sections()->where('type', 'summary')->firstOrFail();

    $this->actingAs($user)
        ->patchJson(route('resume.builder.sections.update', [$resume, $summary]), ['title' => 'Changed'])
        ->assertOk();

    expect($summary->fresh()->title)->toBe('Professional Summary');

    $this->actingAs($user)
        ->deleteJson(route('resume.builder.sections.destroy', [$resume, $summary]))
        ->assertForbidden();
});

test('repeatable section entries support create update reorder and delete', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);
    app(ResumeBuilderService::class)->load($resume);
    $experience = $resume->sections()->where('type', 'experience')->firstOrFail();

    $firstResponse = $this->actingAs($user)
        ->postJson(route('resume.builder.items.store', [$resume, $experience]), [
            'data' => ['title' => 'Engineer', 'company' => 'Acme'],
        ])
        ->assertCreated();

    $secondResponse = $this->actingAs($user)
        ->postJson(route('resume.builder.items.store', [$resume, $experience]), [
            'data' => ['title' => 'Senior Engineer', 'company' => 'Globex'],
        ])
        ->assertCreated();

    $first = ResumeSectionItem::query()->findOrFail($firstResponse->json('item.id'));
    $second = ResumeSectionItem::query()->findOrFail($secondResponse->json('item.id'));

    $this->actingAs($user)
        ->patchJson(route('resume.builder.items.update', [$resume, $experience, $first]), [
            'data' => ['title' => 'Lead Engineer', 'company' => 'Acme'],
        ])
        ->assertOk()
        ->assertJsonPath('item.data.title', 'Lead Engineer');

    $this->actingAs($user)
        ->postJson(route('resume.builder.items.reorder', [$resume, $experience]), [
            'item_ids' => [$second->id, $first->id],
        ])
        ->assertOk();

    expect($experience->items()->orderBy('sort_order')->pluck('id')->all())
        ->toBe([$second->id, $first->id]);

    $this->actingAs($user)
        ->deleteJson(route('resume.builder.items.destroy', [$resume, $experience, $first]))
        ->assertOk();

    $this->assertDatabaseMissing('resume_section_items', ['id' => $first->id]);
});

test('every repeatable default section accepts its structured data', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);
    app(ResumeBuilderService::class)->load($resume);

    $payloads = [
        'skills' => ['name' => 'Laravel', 'level' => 'Expert', 'category' => 'Backend'],
        'education' => ['degree' => 'BSc', 'institution' => 'Example University', 'start_date' => '2020-01', 'end_date' => '2024-01'],
        'experience' => ['title' => 'Engineer', 'company' => 'Acme', 'current' => true],
        'projects' => ['name' => 'Resume Builder', 'role' => 'Lead', 'tech_stack' => 'Laravel', 'url' => 'https://example.com'],
        'courses' => ['name' => 'Advanced Laravel', 'provider' => 'Example Academy', 'date' => '2026'],
        'awards' => ['title' => 'Engineering Award', 'organization' => 'Example Org', 'date' => '2026'],
        'languages' => ['name' => 'English', 'proficiency' => 'Fluent'],
    ];

    foreach ($payloads as $type => $data) {
        $section = $resume->sections()->where('type', $type)->firstOrFail();

        $response = $this->actingAs($user)
            ->postJson(route('resume.builder.items.store', [$resume, $section]), ['data' => $data])
            ->assertCreated();

        foreach ($data as $key => $value) {
            $response->assertJsonPath("item.data.{$key}", $value);
        }
    }

    expect($resume->sections()->withCount('items')->get()->sum('items_count'))->toBe(7);
});

test('a current experience entry persists and renders present from a browser form request', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);
    app(ResumeBuilderService::class)->load($resume);
    $experience = $resume->sections()->where('type', 'experience')->firstOrFail();
    $item = $experience->items()->create([
        'data' => ['title' => 'Software Engineer'],
        'sort_order' => 0,
    ]);

    $this->actingAs($user)
        ->patch(route('resume.builder.items.update', [$resume, $experience, $item]), [
            'data' => [
                'title' => 'Software Engineer',
                'company' => 'Acme',
                'start_date' => '2024-01',
                'current' => 'true',
            ],
        ])
        ->assertOk()
        ->assertJsonPath('item.data.current', true);

    $this->actingAs($user)
        ->get(route('resume.preview', $resume))
        ->assertOk()
        ->assertSee('2024-01')
        ->assertSee('Present');
});

test('section order and visibility persist in the rendered resume', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);
    app(ResumeBuilderService::class)->load($resume);

    $skills = $resume->sections()->where('type', 'skills')->firstOrFail();
    $experience = $resume->sections()->where('type', 'experience')->firstOrFail();
    $skills->items()->create(['data' => ['name' => 'Laravel'], 'sort_order' => 0]);
    $experience->items()->create(['data' => ['title' => 'Engineer'], 'sort_order' => 0]);

    $ids = $resume->sections()->orderBy('sort_order')->pluck('id')->all();
    $skillsIndex = array_search($skills->id, $ids, true);
    $experienceIndex = array_search($experience->id, $ids, true);
    [$ids[$skillsIndex], $ids[$experienceIndex]] = [$ids[$experienceIndex], $ids[$skillsIndex]];

    $this->actingAs($user)
        ->postJson(route('resume.builder.sections.reorder', $resume), ['section_ids' => $ids])
        ->assertOk();

    expect($resume->sections()->orderBy('sort_order')->pluck('id')->all())->toBe($ids);

    $this->actingAs($user)
        ->patchJson(route('resume.builder.sections.update', [$resume, $experience]), ['is_visible' => false])
        ->assertOk();

    $this->actingAs($user)
        ->get(route('resume.preview', $resume))
        ->assertOk()
        ->assertSee('Laravel')
        ->assertDontSee('Engineer');
});

test('users cannot mutate another users sections or entries', function (): void {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = resumeFor($owner);
    resumeFor($intruder);
    app(ResumeBuilderService::class)->load($resume);
    $section = $resume->sections()->where('type', 'skills')->firstOrFail();
    $item = $section->items()->create(['data' => ['name' => 'Laravel'], 'sort_order' => 0]);

    $this->actingAs($intruder)
        ->patchJson(route('resume.builder.sections.update', [$resume, $section]), ['is_visible' => false])
        ->assertForbidden();

    $this->actingAs($intruder)
        ->patchJson(route('resume.builder.items.update', [$resume, $section, $item]), [
            'data' => ['name' => 'Tampered'],
        ])
        ->assertForbidden();
});

test('sections cannot be mixed between two resumes owned by the same user', function (): void {
    $user = User::factory()->create();
    $firstResume = resumeFor($user);
    $secondResume = resumeFor($user);
    app(ResumeBuilderService::class)->load($firstResume);
    app(ResumeBuilderService::class)->load($secondResume);
    $firstSkills = $firstResume->sections()->where('type', 'skills')->firstOrFail();

    $this->actingAs($user)
        ->postJson(route('resume.builder.items.store', [$secondResume, $firstSkills]), [
            'data' => ['name' => 'Must not be saved'],
        ])
        ->assertNotFound();

    expect($firstSkills->items()->count())->toBe(0);
});

test('invalid item payloads and foreign reorder ids are rejected', function (): void {
    $user = User::factory()->create();
    $resume = resumeFor($user);
    app(ResumeBuilderService::class)->load($resume);
    $skills = $resume->sections()->where('type', 'skills')->firstOrFail();

    $this->actingAs($user)
        ->postJson(route('resume.builder.items.store', [$resume, $skills]), [
            'data' => ['url' => 'javascript:alert(1)'],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('data.url');

    $this->actingAs($user)
        ->postJson(route('resume.builder.sections.reorder', $resume), [
            'section_ids' => [$skills->id, 999999],
        ])
        ->assertUnprocessable();
});
