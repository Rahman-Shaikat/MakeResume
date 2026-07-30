<aside class="admin-sidebar" data-admin-sidebar>
    <div class="admin-sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" aria-label="Resume Studio admin dashboard">
            <span class="admin-brand-mark">
                <img src="{{ asset('assets/common/media/logo.png') }}" alt="">
            </span>
            <span class="admin-brand-copy">
                <strong>Resume<span>Studio</span></strong>
                <small>Admin console</small>
            </span>
        </a>
    </div>

    @php($admin = auth('admin')->user())
    @php($canViewRoles = $admin->is_super === 1 || $admin->hasPermission('roles'))
    @php($canViewPermissions = $admin->is_super === 1 || $admin->hasPermission('permissions'))
    @php($canViewAdministrators = $admin->is_super === 1 || $admin->hasPermission('admin-users'))
    @php($canViewCategories = $admin->is_super === 1 || $admin->hasPermission('categories'))
    @php($canViewTemplates = $admin->is_super === 1 || $admin->hasPermission('templates'))
    @php($canViewUsers = $admin->is_super === 1 || $admin->hasPermission('users'))

    <nav class="admin-sidebar-nav" aria-label="Admin navigation">
        <span class="admin-nav-label">Overview</span>
        <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
            @include('admin.components.icon', ['name' => 'dashboard'])
            <span>Dashboard</span>
        </a>

        @if ($canViewUsers)
            <span class="admin-nav-label">User management</span>
            <a class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>
                @include('admin.components.icon', ['name' => 'users'])
                <span>Users</span>
            </a>
        @endif

        @if ($canViewCategories || $canViewTemplates)
            <span class="admin-nav-label">Template management</span>
        @endif
        @if ($canViewCategories)
            <a class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}" href="{{ route('admin.categories.index') }}">
                @include('admin.components.icon', ['name' => 'layout'])
                <span>Categories</span>
            </a>
        @endif
        @if ($canViewTemplates)
            <a class="admin-nav-link {{ request()->routeIs('admin.templates.*') ? 'is-active' : '' }}" href="{{ route('admin.templates.index') }}">
                @include('admin.components.icon', ['name' => 'resume'])
                <span>Templates</span>
            </a>
        @endif

        @if ($canViewRoles || $canViewPermissions || $canViewAdministrators)
            <span class="admin-nav-label">Administration</span>
            @if ($canViewRoles)
                <a class="admin-nav-link {{ request()->routeIs('admin.roles.*') ? 'is-active' : '' }}" href="{{ route('admin.roles.index') }}">
                    @include('admin.components.icon', ['name' => 'shield'])
                    <span>Roles & permissions</span>
                </a>
            @endif
            @if ($canViewPermissions)
                <a class="admin-nav-link {{ request()->routeIs('admin.permissions.*', 'admin.permission-groups.*') ? 'is-active' : '' }}" href="{{ route('admin.permissions.index') }}">
                    @include('admin.components.icon', ['name' => 'settings'])
                    <span>Permissions</span>
                </a>
            @endif
            @if ($canViewAdministrators)
                <a class="admin-nav-link {{ request()->routeIs('admin.admin-users.*') ? 'is-active' : '' }}" href="{{ route('admin.admin-users.index') }}">
                    @include('admin.components.icon', ['name' => 'users'])
                    <span>Administrators</span>
                </a>
            @endif
        @endif
    </nav>

    <div class="admin-sidebar-help">
        <span>@include('admin.components.icon', ['name' => 'sparkles'])</span>
        <div>
            <strong>Secure access</strong>
            <small>Your available tools follow your assigned role.</small>
        </div>
    </div>
</aside>
