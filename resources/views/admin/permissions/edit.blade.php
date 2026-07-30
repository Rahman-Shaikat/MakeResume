@extends('admin.layouts.app')

@section('title', 'Edit permission')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Access control</span><h1>Edit {{ $permission->name }}</h1><p>Display details and hierarchy can change; the released route key remains stable.</p></div>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Back to permissions</a>
</header>

<form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
    @csrf
    @method('PATCH')
    @include('admin.permissions.partials.form')
</form>
@endsection
