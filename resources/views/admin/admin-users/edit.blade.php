@extends('admin.layouts.app')

@section('title', 'Edit administrator')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Administration team</span><h1>Edit {{ $adminUser->name }}</h1><p>Update account details without changing the current password.</p></div>
    <div class="admin-heading-actions">
        <a href="{{ route('admin.admin-users.password.edit', $adminUser) }}" class="btn btn-light">Change password</a>
        <a href="{{ route('admin.admin-users.index') }}" class="btn btn-light">Back</a>
    </div>
</header>

<form action="{{ route('admin.admin-users.update', $adminUser) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    @include('admin.admin-users.partials.form')
</form>
@endsection
