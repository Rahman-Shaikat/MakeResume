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
            <span>Resume Studio</span>
            <strong>Administration</strong>
        </div>
    </div>

    <ul class="list-unstyled d-flex align-items-center mb-0 gap-2">
        <li class="dropdown">
            <button
                type="button"
                class="btn btn-light admin-icon-button rounded-circle position-relative"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Notifications"
            >
                @include('admin.components.icon', ['name' => 'bell'])
                <span class="admin-notification-dot"><span class="visually-hidden">3 unread notifications</span></span>
            </button>

            <div class="dropdown-menu dropdown-menu-end admin-notification-menu p-0">
                <div class="admin-dropdown-heading">
                    <div>
                        <strong>Notifications</strong>
                        <span>3 unread updates</span>
                    </div>
                    <button type="button">Mark all read</button>
                </div>
                <div class="admin-notification-list">
                    <a href="#" class="admin-notification-item">
                        <span class="admin-event-icon is-success">@include('admin.components.icon', ['name' => 'user-plus'])</span>
                        <span><strong>New user registered</strong><small>Nadia joined 8 minutes ago</small></span>
                        <i></i>
                    </a>
                    <a href="#" class="admin-notification-item">
                        <span class="admin-event-icon is-primary">@include('admin.components.icon', ['name' => 'document'])</span>
                        <span><strong>Resume milestone reached</strong><small>25,000 resumes have been created</small></span>
                        <i></i>
                    </a>
                    <a href="#" class="admin-notification-item">
                        <span class="admin-event-icon is-warning">@include('admin.components.icon', ['name' => 'alert'])</span>
                        <span><strong>Support request</strong><small>A new template issue was reported</small></span>
                        <i></i>
                    </a>
                </div>
                <a href="#" class="admin-dropdown-footer">View all notifications</a>
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
                    <a href="{{ route('home') }}">@include('admin.components.icon', ['name' => 'external']) Open Resume Studio</a>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-profile-signout">@include('admin.components.icon', ['name' => 'logout']) Sign out</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
