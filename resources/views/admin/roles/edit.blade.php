@extends('admin.layouts.app')

@section('title', 'Edit role')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Access control</span><h1>Edit {{ $role->name }}</h1><p>Changes apply to every administrator assigned to this role.</p></div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Back to roles</a>
</header>

<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PATCH')
    @include('admin.roles.partials.form')
</form>
@endsection
