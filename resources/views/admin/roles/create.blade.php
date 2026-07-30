@extends('admin.layouts.app')

@section('title', 'Create role')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Access control</span><h1>Create role</h1><p>Choose only the capabilities this role requires.</p></div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Back to roles</a>
</header>

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    @include('admin.roles.partials.form')
</form>
@endsection
