@extends('admin.layouts.app')

@section('title', 'Create permission')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Access control</span><h1>Create permission</h1><p>Add a stable route key for an administration capability.</p></div>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Back to permissions</a>
</header>

<form action="{{ route('admin.permissions.store') }}" method="POST">
    @csrf
    @include('admin.permissions.partials.form')
</form>
@endsection
