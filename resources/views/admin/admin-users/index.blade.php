@extends('admin.layouts.app')

@section('title', 'Administrators')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Administration team</span><h1>Administrators</h1><p>Manage protected accounts and their assigned roles.</p></div>
    @if (auth('admin')->user()->hasPermission('admin-users-create'))
        <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary">Add administrator</a>
    @endif
</header>

<section class="admin-panel">
    <div class="admin-panel-heading">
        <div><span>Protected accounts</span><h2>Administrator directory</h2></div>
        <form action="{{ route('admin.admin-users.index') }}" method="GET" class="admin-search-form">
            <input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name or email" aria-label="Search administrators">
            <button class="btn btn-light" type="submit">Search</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead><tr><th>Administrator</th><th>Role</th><th>Access</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($adminUsers as $adminUser)
                    <tr>
                        <td><strong>{{ $adminUser->name }}</strong><small>{{ $adminUser->email }}</small></td>
                        <td>{{ $adminUser->role?->name ?? 'No role' }}</td>
                        <td><span class="admin-badge {{ $adminUser->is_super === 1 ? 'is-super' : '' }}">{{ $adminUser->is_super === 1 ? 'Super admin' : 'Role-based' }}</span></td>
                        <td><span class="admin-badge {{ $adminUser->status === 1 ? 'is-active' : 'is-inactive' }}">{{ $adminUser->status === 1 ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <x-admin.actions.edit
                                    :url="route('admin.admin-users.edit', $adminUser)"
                                    permission="admin-users-update"
                                />
                                @if ($adminUser->status === 1)
                                    <x-admin.actions.delete
                                        :url="route('admin.admin-users.destroy', $adminUser)"
                                        permission="admin-users-delete"
                                        confirm="Deactivate this administrator?"
                                    />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty-state">No administrators match your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $adminUsers])
</section>
@endsection
