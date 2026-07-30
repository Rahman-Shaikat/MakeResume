@extends('admin.layouts.app')

@section('title', 'Edit user')

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">User management</span>
        <h1>Edit {{ $user->name }}</h1>
        <p>Update account details and verification without changing the password.</p>
    </div>
    <div class="admin-heading-actions">
        <a href="{{ route('admin.users.password.edit', $user) }}" class="btn btn-light">Change password</a>
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light">View user</a>
    </div>
</header>

<form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PATCH')
    @include('admin.users.partials.form')
</form>
@endsection
