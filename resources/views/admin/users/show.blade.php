@extends('admin.layouts.app')

@section('title', $user->name)

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">User management</span>
        <h1>{{ $user->name }}</h1>
        <p>Review account status, membership details, and saved resumes.</p>
    </div>
    <div class="admin-heading-actions">
        @if (auth('admin')->user()->hasPermission('users-update'))
            <a href="{{ route('admin.users.password.edit', $user) }}" class="btn btn-light">Change password</a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit user</a>
        @endif
        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Back to users</a>
    </div>
</header>

<section class="row g-3 mb-3">
    <div class="col-lg-7">
        <article class="admin-panel h-100">
            <div class="admin-panel-heading"><div><span>Account profile</span><h2>User details</h2></div></div>
            <div class="table-responsive">
                <table class="table admin-table admin-detail-table mb-0">
                    <tbody>
                        <tr><th>Full name</th><td>{{ $user->name }}</td></tr>
                        <tr><th>Email address</th><td>{{ $user->email }}</td></tr>
                        <tr>
                            <th>Verification</th>
                            <td>
                                <span class="admin-badge {{ $user->email_verified_at ? 'is-active' : 'is-pending' }}">
                                    {{ $user->email_verified_at ? 'Verified' : 'Pending verification' }}
                                </span>
                            </td>
                        </tr>
                        <tr><th>Verified at</th><td>{{ $user->email_verified_at?->format('M j, Y \a\t g:i A') ?? 'Not verified' }}</td></tr>
                        <tr><th>Joined</th><td>{{ $user->created_at?->format('M j, Y \a\t g:i A') }}</td></tr>
                        <tr><th>Last updated</th><td>{{ $user->updated_at?->diffForHumans() }}</td></tr>
                    </tbody>
                </table>
            </div>
        </article>
    </div>
    <div class="col-lg-5">
        <article class="admin-panel h-100">
            <div class="admin-panel-heading"><div><span>Resume activity</span><h2>Account summary</h2></div></div>
            <div class="admin-panel-body">
                <div class="admin-user-summary">
                    <span class="admin-event-icon is-primary">@include('admin.components.icon', ['name' => 'resume'])</span>
                    <div>
                        <strong>{{ number_format($user->resumes_count) }}</strong>
                        <span>{{ \Illuminate\Support\Str::plural('saved resume', $user->resumes_count) }}</span>
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>

<section class="admin-panel">
    <div class="admin-panel-heading"><div><span>Resume activity</span><h2>Saved resumes</h2></div></div>
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead><tr><th>Template</th><th>Created</th><th>Last updated</th></tr></thead>
            <tbody>
                @forelse ($resumes as $resume)
                    <tr>
                        <td>
                            <strong>{{ $resume->template?->name ?? str($resume->template_slug)->headline() }}</strong>
                            <small>{{ $resume->template_slug }}</small>
                        </td>
                        <td>{{ $resume->created_at?->format('M j, Y') }}</td>
                        <td>{{ $resume->updated_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="admin-empty-state">This user has not created a resume yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $resumes])
</section>

@if (auth('admin')->user()->hasPermission('users-delete'))
    <section class="admin-danger-zone">
        <div>
            <strong>Delete this user</strong>
            <span>Permanently removes the account and all associated resume data.</span>
        </div>
        <x-admin.actions.delete
            :url="route('admin.users.destroy', $user)"
            permission="users-delete"
            label="Delete user"
            confirm="Permanently delete this user and all associated resume data? This cannot be undone."
        />
    </section>
@endif
@endsection
