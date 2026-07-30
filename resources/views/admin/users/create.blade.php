@extends('admin.layouts.app')

@section('title', 'Add user')

@section('content')
<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">User management</span>
        <h1>Add user</h1>
        <p>Create a website account and choose its initial verification state.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Back to users</a>
</header>

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    @include('admin.users.partials.form')
</form>
@endsection
