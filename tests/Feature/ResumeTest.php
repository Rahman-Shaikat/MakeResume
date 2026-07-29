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
        ->assertSee('Choose your resume template')
        ->assertSee('Professional Cyan');

    $this->actingAs($user)
        ->get(route('resume.templates.show', 'template-one'))
        ->assertOk()
        ->assertSee(strtoupper($user->name))
        ->assertSee('Laravel Web Application Developer');
});

test('a user can select a resume template with ajax', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('resume.template.select'), [
            'template_slug' => 'template-one',
        ])
        ->assertOk()
        ->assertJsonPath('template_slug', 'template-one');

    $this->assertDatabaseHas('resumes', [
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);
});

test('the next button is shown after a template has been selected', function (): void {
    $user = User::factory()->create();
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Template selected')
        ->assertSee(route('resume.builder'));
});

test('the builder requires a selected template', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('resume.builder'))
        ->assertRedirect(route('dashboard'));
});

test('a user with a selected template can open the resume builder', function (): void {
    $user = User::factory()->create();
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
    ]);

    $this->actingAs($user)
        ->get(route('resume.builder'))
        ->assertOk()
        ->assertSee('Personal details')
        ->assertSeeText('Save & continue');
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
        'linkedin' => 'https://linkedin.com/in/alex',
        'github' => 'https://github.com/alex',
        'summary' => 'Laravel engineer focused on reliable products and maintainable application architecture.',
    ];

    $this->actingAs($user)
        ->put(route('resume.builder.update'), $details)
        ->assertRedirect(route('resume.builder'));

    expect($resume->fresh()->content)->toMatchArray($details);

    $this->actingAs($user)
        ->get(route('resume.templates.show', 'template-one'))
        ->assertOk()
        ->assertSee('ALEX MORGAN')
        ->assertSee('Senior Laravel Developer');
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

    $this->actingAs($user)
        ->post(route('resume.profile-image.store'), [
            'profile_image' => UploadedFile::fake()->image('profile.jpg', 600, 600)->size(500),
        ])
        ->assertRedirect();

    $resume = Resume::query()->whereBelongsTo($user)->firstOrFail();

    expect($resume->profile_image)
        ->toStartWith('images/')
        ->toEndWith('.jpg');

    Storage::disk('public')->assertExists($resume->profile_image);
});

test('replacing a profile image removes the previous upload', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();
    Storage::disk('public')->put('images/old-profile.jpg', 'old image');
    Resume::query()->create([
        'user_id' => $user->id,
        'template_slug' => 'template-one',
        'profile_image' => 'images/old-profile.jpg',
    ]);

    $this->actingAs($user)
        ->post(route('resume.profile-image.store'), [
            'profile_image' => UploadedFile::fake()->image('new-profile.png', 600, 600),
        ])
        ->assertRedirect();

    Storage::disk('public')->assertMissing('images/old-profile.jpg');
    Storage::disk('public')->assertExists($user->resume()->firstOrFail()->profile_image);
});

test('profile image validation rejects unsupported files', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('resume.profile-image.store'), [
            'profile_image' => UploadedFile::fake()->create('profile.svg', 20, 'image/svg+xml'),
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHasErrors('profile_image');

    Storage::disk('public')->assertDirectoryEmpty('images');
});
