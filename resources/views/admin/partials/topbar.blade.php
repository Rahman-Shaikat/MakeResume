<nav class="navbar admin-topbar fixed-top px-3" data-admin-topbar>
    @php($admin = auth('admin')->user())
    <div class="d-flex align-items-center">
        <button
            type="button"
            class="btn btn-light admin-icon-button d-none d-lg-inline-flex"
            data-admin-sidebar-collapse
            aria-label="Collapse sidebar"
            aria-expanded="true"
        >
            @include('admin.components.icon', ['name' => 'sidebar'])
        </button>

        <button
            type="button"
            class="btn btn-light admin-icon-button d-lg-none"
            data-admin-sidebar-open
            aria-label="Open navigation"
            aria-expanded="false"
        >
            @include('admin.components.icon', ['name' => 'menu'])
        </button>

        <div class="admin-topbar-context d-none d-md-block">
            <span>Resume Engineer</span>
            <strong>Administration</strong>
        </div>
    </div>

    <ul class="list-unstyled d-flex align-items-center mb-0 gap-2 admin-topbar-actions">
        <li>
            <a
                href="{{ route('clear-cache') }}"
                class="btn btn-light admin-topbar-action"
                data-admin-clear-cache
                aria-label="Clear application cache"
                title="Clear application cache"
            >
                @include('admin.components.icon', ['name' => 'refresh'])
                <span>Clear cache</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('home') }}"
                class="btn btn-light admin-topbar-action"
                data-admin-view-website
                aria-label="Open frontend website"
                title="Open frontend website"
                target="_blank"
            >
                @include('admin.components.icon', ['name' => 'external'])
                <span>View website</span>
            </a>
        </li>

        <li class="dropdown">
            <button
                type="button"
                class="btn btn-light admin-icon-button rounded-circle position-relative"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Notifications, {{ $adminUnreadNotificationCount }} unread"
            >
                @include('admin.components.icon', ['name' => 'bell'])
                @if ($adminUnreadNotificationCount > 0)
                    <span class="admin-notification-dot">
                        <span class="visually-hidden">{{ $adminUnreadNotificationCount }} unread notifications</span>
                    </span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end admin-notification-menu p-0">
                <div class="admin-dropdown-heading">
                    <div>
                        <strong>Notifications</strong>
                        <span>
                            {{ $adminUnreadNotificationCount }}
                            {{ \Illuminate\Support\Str::plural('unread update', $adminUnreadNotificationCount) }}
                        </span>
                    </div>
                    @if ($adminUnreadNotificationCount > 0)
                        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit">Mark all read</button>
                        </form>
                    @else
                        <span class="admin-notification-caught-up">All caught up</span>
                    @endif
                </div>
                <div class="admin-notification-list">
                    @forelse ($adminNotifications as $notification)
                        <form action="{{ route('admin.notifications.open', $notification->id) }}" method="POST" class="admin-notification-form">
                            @csrf
                            <button type="submit" class="admin-notification-item {{ $notification->read_at ? '' : 'is-unread' }}">
                                <span class="admin-event-icon is-{{ data_get($notification->data, 'tone', 'primary') }}">
                                    @include('admin.components.icon', ['name' => data_get($notification->data, 'icon', 'bell')])
                                </span>
                                <span>
                                    <strong>{{ data_get($notification->data, 'title', 'Administration update') }}</strong>
                                    <small>{{ data_get($notification->data, 'message', 'There is a new administration update.') }}</small>
                                    <time datetime="{{ $notification->created_at?->toIso8601String() }}">{{ $notification->created_at?->diffForHumans() }}</time>
                                </span>
                                @if (! $notification->read_at)
                                    <i aria-hidden="true"></i>
                                @endif
                            </button>
                        </form>
                    @empty
                        <div class="admin-notification-empty">
                            @include('admin.components.icon', ['name' => 'bell'])
                            <strong>No notifications yet</strong>
                            <span>New account and resume activity will appear here.</span>
                        </div>
                    @endforelse
                </div>
                @if ($adminNotifications->isNotEmpty())
                    <span class="admin-dropdown-footer">Showing the latest {{ $adminNotifications->count() }} notifications</span>
                @endif
            </div>
        </li>

        <li class="dropdown ms-1">
            <button
                type="button"
                class="admin-profile-trigger"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <img src="{{ $admin->image_path ? \Illuminate\Support\Facades\Storage::url($admin->image_path) : asset('assets/admin/images/avatars/avatar-1.jpg') }}" alt="{{ $admin->name }}">
                <span class="d-none d-sm-grid"><strong>{{ $admin->name }}</strong><small>{{ $admin->is_super === 1 ? 'Super Administrator' : ($admin->role?->name ?? 'Administrator') }}</small></span>
                @include('admin.components.icon', ['name' => 'chevron-down'])
            </button>

            <div class="dropdown-menu dropdown-menu-end admin-profile-menu p-0">
                <div class="admin-profile-summary">
                    <img src="{{ $admin->image_path ? \Illuminate\Support\Facades\Storage::url($admin->image_path) : asset('assets/admin/images/avatars/avatar-1.jpg') }}" alt="">
                    <div><strong>{{ $admin->name }}</strong><span>{{ $admin->email }}</span></div>
                </div>
                <div class="admin-profile-links">
                    <a href="{{ route('home') }}">@include('admin.components.icon', ['name' => 'external']) Open Resume Engineer</a>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-profile-signout">@include('admin.components.icon', ['name' => 'logout']) Sign out</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
