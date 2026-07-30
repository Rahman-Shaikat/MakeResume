<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\HomepageHero;
use App\Models\ResumeTemplate;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makeHomepageHeroAdmin(array $attributes = []): AdminUser
{
    return AdminUser::factory()
        ->for(Role::factory())
        ->create($attributes + ['is_super' => 1]);
}

function validHomepageHeroPayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Career changers hero',
        'resume_template_id' => ResumeTemplate::query()->where('slug', 'template-one')->value('id'),
        'preview_image' => null,
        'eyebrow' => 'Your next role starts here',
        'headline' => 'Build a resume made for your next opportunity.',
        'description' => 'Create a focused professional story and see it take shape as you work.',
        'top_badge' => 'Designed around you',
        'editor_title' => 'Professional summary',
        'editor_description' => 'Clear, focused, and ready for the role you want next.',
        'bottom_status_title' => 'Saved automatically',
        'bottom_status_text' => 'Your progress is safe',
        'status' => 1,
    ], $overrides);
}

test('administrator without homepage hero permission is forbidden', function (): void {
    $admin = makeHomepageHeroAdmin(['is_super' => 2]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-heroes.index'))
        ->assertForbidden();
});

test('super administrator can browse create and edit homepage hero screens', function (): void {
    $admin = makeHomepageHeroAdmin();
    $hero = HomepageHero::query()->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-heroes.index'))
        ->assertOk()
        ->assertSee('Homepage hero')
        ->assertSee('Primary homepage hero');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-heroes.create'))
        ->assertOk()
        ->assertSee('Create homepage hero');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.homepage-heroes.edit', $hero))
        ->assertOk()
        ->assertSee('Custom A4 preview image');
});

test('publishing a homepage hero replaces the currently active configuration', function (): void {
    $admin = makeHomepageHeroAdmin();
    $existingHero = HomepageHero::query()->active()->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.homepage-heroes.store'), validHomepageHeroPayload())
        ->assertSessionHasNoErrors();

    $hero = HomepageHero::query()->where('name', 'Career changers hero')->firstOrFail();

    expect($hero->status)->toBe(1)
        ->and($existingHero->refresh()->status)->toBe(2)
        ->and(HomepageHero::query()->active()->count())->toBe(1);
});

test('homepage hero validates a published preview source and secure image uploads', function (): void {
    $admin = makeHomepageHeroAdmin();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.homepage-heroes.store'), validHomepageHeroPayload([
            'resume_template_id' => null,
            'preview_image' => null,
        ]))
        ->assertSessionHasErrors(['resume_template_id']);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.homepage-heroes.store'), validHomepageHeroPayload([
            'preview_image' => UploadedFile::fake()->createWithContent('hero.php', '<?php echo "unsafe";'),
        ]))
        ->assertSessionHasErrors(['preview_image']);
});

test('custom homepage hero preview images are replaced and cleaned up safely', function (): void {
    Storage::fake('public');
    $admin = makeHomepageHeroAdmin();
    Storage::disk('public')->put('homepage-heroes/old.png', 'old');
    $hero = HomepageHero::query()->active()->firstOrFail();
    $hero->update(['preview_image_path' => 'homepage-heroes/old.png']);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.homepage-heroes.update', $hero), validHomepageHeroPayload([
            'name' => $hero->name,
            'preview_image' => UploadedFile::fake()->image('new-hero.png', 700, 990)->size(500),
        ]))
        ->assertSessionHasNoErrors();

    $hero->refresh();

    Storage::disk('public')->assertMissing('homepage-heroes/old.png');
    Storage::disk('public')->assertExists($hero->preview_image_path);
});

test('admin can remove a custom hero preview and return to the selected template', function (): void {
    Storage::fake('public');
    $admin = makeHomepageHeroAdmin();
    Storage::disk('public')->put('homepage-heroes/custom.png', 'custom');
    $hero = HomepageHero::query()->active()->firstOrFail();
    $hero->update(['preview_image_path' => 'homepage-heroes/custom.png']);

    $this->actingAs($admin, 'admin')
        ->patch(route('admin.homepage-heroes.update', $hero), validHomepageHeroPayload([
            'name' => $hero->name,
            'remove_preview_image' => 1,
        ]))
        ->assertSessionHasNoErrors();

    expect($hero->refresh()->preview_image_path)->toBeNull();
    Storage::disk('public')->assertMissing('homepage-heroes/custom.png');
});

test('the public homepage renders only the active homepage hero configuration', function (): void {
    $active = HomepageHero::query()->active()->firstOrFail();
    $active->update([
        'headline' => 'A dynamic homepage message',
        'top_badge' => 'Managed from admin',
    ]);
    HomepageHero::factory()->create([
        'headline' => 'An inactive homepage message',
        'status' => 2,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('A dynamic homepage message')
        ->assertSee('Managed from admin')
        ->assertDontSee('An inactive homepage message');
});
