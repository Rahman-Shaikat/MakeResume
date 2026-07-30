@extends('admin.layouts.app')

@section('title', 'Permissions')

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Access control</span>
        <h1>Permissions</h1>
        <p>Manage stable route keys and organize them into parent and child capabilities.</p>
    </div>
    <div class="admin-heading-actions">
        @if (auth('admin')->user()->hasPermission('permissions-create'))
            <a href="{{ route('admin.permission-groups.create') }}" class="btn btn-light">Create group</a>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">Create permission</a>
        @endif
    </div>
</header>

@forelse ($permissionGroups as $group)
    <section class="admin-panel admin-permission-list-panel">
        <div class="admin-panel-heading">
            <div>
                <span>Permission group</span>
                <h2>{{ $group->name }}</h2>
            </div>
            <div class="admin-table-actions">
                <x-admin.actions.edit
                    :url="route('admin.permission-groups.edit', $group)"
                    permission="permissions-update"
                    label="Edit group"
                />
                <x-admin.actions.delete
                    :url="route('admin.permission-groups.destroy', $group)"
                    permission="permissions-delete"
                    label="Deactivate group"
                    confirm="Deactivate this permission group?"
                />
            </div>
        </div>
        @if ($group->parentPermissions->isEmpty())
            <p class="admin-empty-state">This group has no active permissions.</p>
        @else
            <div class="table-responsive">
                <table class="table admin-table admin-permission-table mb-0">
                    <thead>
                        <tr><th>Permission</th><th>Route key</th><th>Assigned roles</th><th>Level</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($group->parentPermissions as $permission)
                            <tr class="admin-permission-parent-row">
                                <td><strong>{{ $permission->name }}</strong><small>{{ $permission->short_desc ?: 'No description provided.' }}</small></td>
                                <td><code>{{ $permission->meta_name }}</code></td>
                                <td>{{ $permission->roles_count }}</td>
                                <td><span class="admin-badge is-super">Parent</span></td>
                                <td>
                                    <div class="admin-table-actions">
                                        <x-admin.actions.edit
                                            :url="route('admin.permissions.edit', $permission)"
                                            permission="permissions-update"
                                        />
                                        <x-admin.actions.delete
                                            :url="route('admin.permissions.destroy', $permission)"
                                            permission="permissions-delete"
                                            confirm="Deactivate this parent permission and all of its child permissions?"
                                        />
                                    </div>
                                </td>
                            </tr>
                            @foreach ($permission->children as $child)
                                <tr class="admin-permission-child-row">
                                    <td><strong>{{ $child->name }}</strong><small>{{ $child->short_desc ?: "Child action of {$permission->name}." }}</small></td>
                                    <td><code>{{ $child->meta_name }}</code></td>
                                    <td>{{ $child->roles_count }}</td>
                                    <td><span class="admin-badge">Child</span></td>
                                    <td>
                                        <div class="admin-table-actions">
                                            <x-admin.actions.edit
                                                :url="route('admin.permissions.edit', $child)"
                                                permission="permissions-update"
                                            />
                                            <x-admin.actions.delete
                                                :url="route('admin.permissions.destroy', $child)"
                                                permission="permissions-delete"
                                                confirm="Deactivate this child permission?"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@empty
    <section class="admin-panel">
        <p class="admin-empty-state">No active permission groups exist. Create a group to begin.</p>
    </section>
@endforelse
@endsection
