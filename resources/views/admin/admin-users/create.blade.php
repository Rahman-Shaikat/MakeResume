@extends('admin.layouts.app')

@section('title', 'Add administrator')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Administration team</span><h1>Add administrator</h1><p>Create a protected account and assign its access role.</p></div>
    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-light">Back to administrators</a>
</header>

<form action="{{ route('admin.admin-users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.admin-users.partials.form')
</form>
@endsection
