@extends('admin.layouts.app')

@section('title', 'Create permission group')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Access control</span><h1>Create permission group</h1><p>Organize related administration capabilities.</p></div>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Back to permissions</a>
</header>

<form action="{{ route('admin.permission-groups.store') }}" method="POST">
    @csrf
    @include('admin.permissions.groups.partials.form')
</form>
@endsection
