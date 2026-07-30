@extends('admin.layouts.app')

@section('title', 'Edit permission group')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Access control</span><h1>Edit {{ $permissionGroup->name }}</h1><p>Update the label and description used to organize permissions.</p></div>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Back to permissions</a>
</header>

<form action="{{ route('admin.permission-groups.update', $permissionGroup) }}" method="POST">
    @csrf
    @method('PATCH')
    @include('admin.permissions.groups.partials.form')
</form>
@endsection
