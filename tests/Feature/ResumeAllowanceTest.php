<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('gives inherited users the free one-resume allowance', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-one'])
        ->assertCreated();

    $this->actingAs($user)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-two'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.template_slug.0', 'You have reached your 1-resume allowance. Delete a saved resume to create another one.');

    expect($user->resumes()->count())->toBe(1);
});

it('shows inherited users their allowance and disables creation at the limit', function (): void {
    $user = User::factory()->create();
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('1 / 1')
        ->assertSee('Free allowance')
        ->assertSee('Resume limit reached')
        ->assertSee('disabled', false);
});

it('supports zero, custom, and unlimited admin allowances', function (): void {
    $zeroLimitUser = User::factory()->withResumeLimit(0)->create();
    $customLimitUser = User::factory()->withResumeLimit(2)->create();
    $unlimitedUser = User::factory()->unlimited()->create();

    $this->actingAs($zeroLimitUser)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-one'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'New resume creation is currently disabled for your account.');

    $this->actingAs($customLimitUser)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-one'])
        ->assertCreated();
    $this->actingAs($customLimitUser)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-two'])
        ->assertCreated();
    $this->actingAs($customLimitUser)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-three'])
        ->assertUnprocessable();

    $this->actingAs($unlimitedUser)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-one'])
        ->assertCreated();
    $this->actingAs($unlimitedUser)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-two'])
        ->assertCreated();

    expect($zeroLimitUser->resumes()->count())->toBe(0)
        ->and($customLimitUser->resumes()->count())->toBe(2)
        ->and($unlimitedUser->resumes()->count())->toBe(2);
});

it('preserves excess resumes and restores capacity after deletion', function (): void {
    $user = User::factory()->withResumeLimit(1)->create();
    $firstResume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-two',
    ]);

    $this->actingAs($user)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-three'])
        ->assertUnprocessable();

    expect($user->resumes()->count())->toBe(2);

    $this->actingAs($user)->delete(route('resume.destroy', $firstResume))->assertRedirect(route('dashboard'));
    $user->resumes()->oldest()->firstOrFail()->delete();

    $this->actingAs($user)
        ->postJson(route('resume.template.select'), ['template_slug' => 'template-three'])
        ->assertCreated();
});
