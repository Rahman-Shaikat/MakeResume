<?php

declare(strict_types=1);

use App\Models\ResumeTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests can view an active template through the public embedded preview route', function (): void {
    $template = ResumeTemplate::query()->where('slug', 'template-one')->firstOrFail();

    $this->get(route('home.template-preview', [
        'template' => $template->slug,
        'v' => $template->updated_at?->getTimestamp(),
    ]))
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=3600, public')
        ->assertSee('is-embedded', false)
        ->assertSee('resume-template-one', false)
        ->assertSee('ALEX MORGAN');
});

test('public template previews reject inactive unknown and non-allowlisted templates', function (): void {
    $inactiveTemplate = ResumeTemplate::query()->where('slug', 'template-two')->firstOrFail();
    $inactiveTemplate->update(['status' => 2]);

    $this->get(route('home.template-preview', $inactiveTemplate->slug))
        ->assertNotFound();

    $unsafeTemplate = ResumeTemplate::query()->where('slug', 'template-three')->firstOrFail();
    $unsafeTemplate->update(['renderer_key' => '../../unsafe']);

    $this->get(route('home.template-preview', $unsafeTemplate->slug))
        ->assertNotFound();

    $this->get(route('home.template-preview', 'not-a-template'))
        ->assertNotFound();
});
