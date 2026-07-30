@extends('admin.layouts.app')

@section('title', 'Change administrator password')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Credential security</span><h1>Change password</h1><p>Set a new password for {{ $adminUser->name }}.</p></div>
    <a href="{{ route('admin.admin-users.edit', $adminUser) }}" class="btn btn-light">Back to administrator</a>
</header>

<form action="{{ route('admin.admin-users.password.update', $adminUser) }}" method="POST">
    @csrf
    @method('PATCH')
    <section class="admin-panel admin-narrow-panel">
        <div class="admin-panel-heading"><div><span>{{ $adminUser->email }}</span><h2>New password</h2></div></div>
        <div class="admin-panel-body admin-form">
            <x-admin.forms.input
                name="password"
                label="Password"
                type="password"
                minlength="8"
                maxlength="50"
                autocomplete="new-password"
                required
            />
            <x-admin.forms.input
                name="password_confirmation"
                label="Confirm password"
                type="password"
                minlength="8"
                maxlength="50"
                autocomplete="new-password"
                required
            />
        </div>
    </section>
    <div class="admin-form-actions admin-narrow-panel">
        <a href="{{ route('admin.admin-users.edit', $adminUser) }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-primary">Update password</button>
    </div>
</form>
@endsection
