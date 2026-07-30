@extends('admin.layouts.app')

@section('title', 'Change user password')

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Credential security</span>
        <h1>Change user password</h1>
        <p>Set a new password for {{ $user->name }}.</p>
    </div>
    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light">Back to user</a>
</header>

<form action="{{ route('admin.users.password.update', $user) }}" method="POST">
    @csrf
    @method('PATCH')
    <section class="admin-panel admin-narrow-panel">
        <div class="admin-panel-heading"><div><span>{{ $user->email }}</span><h2>New password</h2></div></div>
        <div class="admin-panel-body admin-form">
            <x-admin.forms.input
                name="password"
                label="Password"
                type="password"
                minlength="8"
                maxlength="255"
                autocomplete="new-password"
                required
            />
            <x-admin.forms.input
                name="password_confirmation"
                label="Confirm password"
                type="password"
                minlength="8"
                maxlength="255"
                autocomplete="new-password"
                required
            />
        </div>
    </section>
    <div class="admin-form-actions admin-narrow-panel">
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-primary">Update password</button>
    </div>
</form>
@endsection
