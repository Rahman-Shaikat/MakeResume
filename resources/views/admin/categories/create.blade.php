@extends('admin.layouts.app')

@section('title', 'Create category')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Template management</span><h1>Create category</h1><p>Add a job category or place it beneath an existing parent category.</p></div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Back to categories</a>
</header>

<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf
    @include('admin.categories.partials.form')
</form>
@endsection
