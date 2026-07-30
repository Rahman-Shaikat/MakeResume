@extends('admin.layouts.app')

@section('title', 'Roles & permissions')

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Access control</span>
        <h1>Roles & permissions</h1>
        <p>Assign exact administration capabilities through reusable roles.</p>
    </div>
    @if (auth('admin')->user()->hasPermission('roles-create'))
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Create role</a>
    @endif
</header>

<section class="admin-panel">
    <div class="admin-panel-heading">
        <div><span>Administrator access</span><h2>Roles</h2></div>
    </div>
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr><th>Role</th><th>Permissions</th><th>Active administrators</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td><strong>{{ $role->name }}</strong><small>{{ $role->short_desc ?: 'No description provided.' }}</small></td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->active_admin_users_count }}</td>
                        <td><span class="admin-badge {{ $role->status === 1 ? 'is-active' : 'is-inactive' }}">{{ $role->status === 1 ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <x-admin.actions.edit
                                    :url="route('admin.roles.edit', $role)"
                                    permission="roles-update"
                                />
                                @if ($role->status === 1)
                                    <x-admin.actions.delete
                                        :url="route('admin.roles.destroy', $role)"
                                        permission="roles-delete"
                                        confirm="Deactivate this role?"
                                    />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty-state">No roles have been created.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $roles])
</section>
@endsection
