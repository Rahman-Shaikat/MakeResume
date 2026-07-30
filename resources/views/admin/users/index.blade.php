@extends('admin.layouts.app')

@section('title', 'Users')

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">User management</span>
        <h1>Users</h1>
        <p>Manage website accounts, email verification, credentials, and resume activity.</p>
    </div>
    @if (auth('admin')->user()->hasPermission('users-create'))
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add user</a>
    @endif
</header>

<section class="admin-panel">
    <div class="admin-panel-heading">
        <div><span>Website accounts</span><h2>User directory</h2></div>
        <form action="{{ route('admin.users.index') }}" method="GET" class="admin-search-form">
            <input
                class="form-control"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name or email"
                aria-label="Search users"
            >
            <select class="form-select" name="verification" aria-label="Filter by verification">
                @foreach ($verificationFilters as $filter)
                    <option value="{{ $filter['value'] }}" @selected(request('verification') === $filter['value'])>
                        {{ $filter['label'] }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-light" type="submit">Filter</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Verification</th>
                    <th>Resumes</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></td>
                        <td>
                            <span class="admin-badge {{ $user->email_verified_at ? 'is-active' : 'is-pending' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                        <td>{{ number_format($user->resumes_count) }}</td>
                        <td>{{ $user->created_at?->format('M j, Y') }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <x-admin.actions.view
                                    :url="route('admin.users.show', $user)"
                                    permission="users"
                                />
                                <x-admin.actions.edit
                                    :url="route('admin.users.edit', $user)"
                                    permission="users-update"
                                />
                                <x-admin.actions.delete
                                    :url="route('admin.users.destroy', $user)"
                                    permission="users-delete"
                                    label="Delete"
                                    confirm="Permanently delete this user and all associated resume data? This cannot be undone."
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty-state">No users match the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.pagination', ['paginator' => $users])
</section>
@endsection
