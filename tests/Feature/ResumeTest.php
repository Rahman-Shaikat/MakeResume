<?php

declare(strict_types=1);

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('the dashboard is protected from guests', function (): void {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('an authenticated user can view the dashboard and template', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Choose a resume template')
        ->assertSee('Professional Cyan');

    $this->actingAs($user)
        ->get(route('resume.templates.show', 'template-one'))
        ->assertOk()
        ->assertSee(strtoupper($user->name))
        ->assertSee('Laravel Web Application Developer');
});

test('a user can select a resume template with ajax', function (): void {
    $user = User::factory()->create();

    $firstResponse = $this->actingAs($user)
        ->postJson(route('resume.template.select'), [
            'template_slug' => 'template-one',
        ])
        ->assertCreated()
        ->assertJsonPath('template_slug', 'template-one')
        ->assertJsonStructure(['resume_id', 'builder_url', 'preview_url']);

    $secondResponse = $this->actingAs($user)
        ->postJson(route('resume.template.select'), [
            'template_slug' => 'template-one',
        ])
        ->assertCreated();

    expect($user->resumes()->count())->toBe(2)
        ->and($firstResponse->json('resume_id'))->not->toBe($secondResponse->json('resume_id'));
});

test('the dashboard lists previously saved resumes with edit links', function (): void {
    $user = User::factory()->create();
    $first = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'content' => ['professional_title' => 'Backend Engineer'],
    ]);
    $second = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'content' => ['professional_title' => 'Platform Engineer'],
    ]);
    $otherUser = User::factory()->create();
    Resume::query()->create([
        'user_id' => $otherUser->id,
        'template_slug' => 'template-one',
        'content' => ['professional_title' => 'Private Other Resume'],
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Previously saved resumes')
        ->assertSee('Backend Engineer')
        ->assertSee('Platform Engineer')
        ->assertDontSee('Private Other Resume')
        ->assertSee(route('resume.builder', $first))
        ->assertSee(route('resume.builder', $second));
});

test('a user with a selected template can open the resume builder', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($user)
        ->get(route('resume.builder', $resume))
        ->assertOk()
        ->assertSee('Personal Details')
        ->assertSeeText('Resume content')
        ->assertSeeText('Add custom section');
});

test('personal details can be saved from the resume builder', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $details = [
        'full_name' => 'Alex Morgan',
        'professional_title' => 'Senior Laravel Developer',
        'email' => 'alex@example.com',
        'phone' => '+880 1700 000000',
        'location' => 'Dhaka, Bangladesh',
        'website' => 'https://alex.example.com',
        'linkedin' => 'https://linkedin.com/in/alex',
        'github' => 'https://github.com/alex',
        'summary' => 'Laravel engineer focused on reliable products and maintainable application architecture.',
    ];

    $this->actingAs($user)
        ->patchJson(route('resume.builder.content.update', $resume), $details)
        ->assertOk()
        ->assertJsonPath('content.full_name', 'Alex Morgan');

    expect($resume->fresh()->content)->toMatchArray($details);

    $this->actingAs($user)
        ->get(route('resume.preview', $resume))
        ->assertOk()
        ->assertSee('ALEX MORGAN')
        ->assertSee('Senior Laravel Developer');
});

test('editing a selected resume does not change another saved resume', function (): void {
    $user = User::factory()->create();
    $first = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'content' => ['full_name' => 'First Resume'],
    ]);
    $second = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'content' => ['full_name' => 'Second Resume'],
    ]);

    $this->actingAs($user)
        ->patchJson(route('resume.builder.content.update', $first), [
            'full_name' => 'Updated First Resume',
            'professional_title' => 'Engineer',
            'email' => $user->email,
            'phone' => '',
            'location' => '',
            'website' => '',
            'linkedin' => '',
            'github' => '',
            'summary' => '',
        ])
        ->assertOk();

    expect($first->fresh()->content['full_name'])->toBe('Updated First Resume')
        ->and($second->fresh()->content['full_name'])->toBe('Second Resume');
});

test('users cannot open or modify another users resume', function (): void {
    Storage::fake('public');
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $owner->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($intruder)
        ->get(route('resume.builder', $resume))
        ->assertForbidden();

    $this->actingAs($intruder)
        ->get(route('resume.preview', $resume))
        ->assertForbidden();

    $this->actingAs($intruder)
        ->postJson(route('resume.profile-image.store', $resume), [
            'profile_image' => UploadedFile::fake()->image('intruder.jpg', 600, 600),
        ])
        ->assertForbidden();

    expect($resume->fresh()->profile_image)->toBeNull();
});

test('unknown resume templates are rejected', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('resume.template.select'), [
            'template_slug' => 'unknown-template',
        ]);

    expect($response->status())->toBe(422)
        ->and($response->json('errors.template_slug.0'))->toBeString();
});

test('a user profile image is stored under public images', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($user)
        ->post(route('resume.profile-image.store', $resume), [
            'profile_image' => UploadedFile::fake()->image('profile.jpg', 600, 600)->size(500),
        ])
        ->assertRedirect();

    $resume->refresh();

    expect($resume->profile_image)
        ->toStartWith('images/')
        ->toEndWith('.jpg');

    Storage::disk('public')->assertExists($resume->profile_image);
});

test('profile image upload returns its public URL for the live preview', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($user)
        ->postJson(route('resume.profile-image.store', $resume), [
            'profile_image' => UploadedFile::fake()->image('profile.webp', 600, 600)->size(500),
        ])
        ->assertOk()
        ->assertJsonPath('message', 'Profile photo updated successfully.')
        ->assertJsonStructure(['profile_image_url']);

    $resume->refresh();
    expect($resume->profile_image)->toStartWith('images/');
    Storage::disk('public')->assertExists($resume->profile_image);
});

test('replacing a profile image removes the previous upload', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    Storage::disk('public')->put('images/old-profile.jpg', 'old image');
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'profile_image' => 'images/old-profile.jpg',
    ]);

    $this->actingAs($user)
        ->post(route('resume.profile-image.store', $resume), [
            'profile_image' => UploadedFile::fake()->image('new-profile.png', 600, 600),
        ])
        ->assertRedirect();

    Storage::disk('public')->assertMissing('images/old-profile.jpg');
    Storage::disk('public')->assertExists($resume->fresh()->profile_image);
});

test('profile image validation rejects unsupported files', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('resume.profile-image.store', $resume), [
            'profile_image' => UploadedFile::fake()->create('profile.svg', 20, 'image/svg+xml'),
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHasErrors('profile_image');

    Storage::disk('public')->assertDirectoryEmpty('images');
});
