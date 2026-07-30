<?php

declare(strict_types=1);

use App\Models\AdminUser;
use App\Models\Role;
use App\Services\Admin\AdminNotificationService;
use App\Services\Frontend\ResumeService;
use App\Services\Frontend\UserRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function makeNotificationAdmin(array $attributes = []): AdminUser
{
    return AdminUser::factory()
        ->for(Role::factory())
        ->super()
        ->create($attributes);
}

test('admin topbar renders a dynamic empty notification state', function (): void {
    $admin = makeNotificationAdmin();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Notifications, 0 unread', false)
        ->assertSeeText('0 unread updates')
        ->assertSee('No notifications yet')
        ->assertDontSee('New user registered')
        ->assertDontSee('Resume milestone reached');
});

test('notification service delivers database notifications only to active administrators', function (): void {
    $activeAdmin = makeNotificationAdmin();
    $inactiveAdmin = makeNotificationAdmin(['status' => 2]);

    app(AdminNotificationService::class)->notifyActiveAdministrators(
        title: 'New user registered',
        message: 'A user created an account.',
        url: route('admin.dashboard'),
        icon: 'user-plus',
        tone: 'success',
    );

    expect($activeAdmin->notifications()->count())->toBe(1)
        ->and($inactiveAdmin->notifications()->count())->toBe(0);

    $notification = $activeAdmin->notifications()->firstOrFail();

    $this->actingAs($activeAdmin, 'admin')
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Notifications, 1 unread', false)
        ->assertSeeText('1 unread update')
        ->assertSee('New user registered')
        ->assertSee('A user created an account.')
        ->assertSee(route('admin.notifications.open', $notification->id), false);
});

test('administrator can open an owned notification and mark all notifications read', function (): void {
    $admin = makeNotificationAdmin();
    $service = app(AdminNotificationService::class);

    $service->notifyActiveAdministrators(
        title: 'First update',
        message: 'The first update.',
        url: route('admin.dashboard'),
    );
    $service->notifyActiveAdministrators(
        title: 'Second update',
        message: 'The second update.',
        url: route('admin.dashboard'),
    );

    $firstNotification = $admin->notifications()->oldest()->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.notifications.open', $firstNotification->id))
        ->assertRedirect(route('admin.dashboard'));

    expect($firstNotification->refresh()->read_at)->not->toBeNull()
        ->and($admin->unreadNotifications()->count())->toBe(1);

    $this->actingAs($admin, 'admin')
        ->from(route('admin.dashboard'))
        ->post(route('admin.notifications.read-all'))
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('success', 'All notifications marked as read.');

    expect($admin->unreadNotifications()->count())->toBe(0);
});

test('administrator cannot open another administrators notification', function (): void {
    $firstAdmin = makeNotificationAdmin();
    $secondAdmin = makeNotificationAdmin();

    app(AdminNotificationService::class)->notifyActiveAdministrators(
        title: 'Private update',
        message: 'Owned by each recipient.',
        url: route('admin.dashboard'),
    );

    $secondNotification = $secondAdmin->notifications()->firstOrFail();

    $this->actingAs($firstAdmin, 'admin')
        ->post(route('admin.notifications.open', $secondNotification->id))
        ->assertNotFound();
});

test('registration and resume creation create real admin activity notifications', function (): void {
    $admin = makeNotificationAdmin();

    $user = app(UserRegistrationService::class)->register([
        'name' => 'Nadia Rahman',
        'email' => 'nadia@example.com',
        'password' => Hash::make('password'),
    ]);

    app(ResumeService::class)->create($user, 'template-one');

    $notifications = $admin->notifications()->latest()->get();
    $resumeNotification = $notifications->first(
        fn ($notification): bool => $notification->data['title'] === 'New resume created'
    );

    expect($notifications)->toHaveCount(2)
        ->and($notifications->pluck('data')->pluck('title')->sort()->values()->all())
        ->toBe(['New resume created', 'New user registered'])
        ->and($resumeNotification?->data['message'])
        ->toContain('Professional Cyan');
});

test('unsafe notification targets fall back to the admin dashboard', function (): void {
    $admin = makeNotificationAdmin();

    app(AdminNotificationService::class)->notifyActiveAdministrators(
        title: 'Unsafe target',
        message: 'This target must not be followed.',
        url: 'https://example.net/phishing',
    );

    $notification = $admin->notifications()->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.notifications.open', $notification->id))
        ->assertRedirect(route('admin.dashboard'));
});
